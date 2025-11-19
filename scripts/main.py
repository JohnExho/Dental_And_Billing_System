from fastapi import FastAPI
import mysql.connector
from sklearn.cluster import KMeans
from sklearn.linear_model import LinearRegression
import numpy as np
from datetime import datetime, timedelta
import pandas as pd
from statsmodels.tsa.arima.model import ARIMA
import joblib
import os
from sklearn.metrics import mean_absolute_percentage_error, mean_squared_error, r2_score
from statsmodels.tsa.holtwinters import ExponentialSmoothing


app = FastAPI()

# --- MySQL config ---
MYSQL_HOST = "127.0.0.1"
MYSQL_USER = "root"
MYSQL_PASSWORD = ""
MYSQL_DB = "dayao"

def get_mysql_connection():
    return mysql.connector.connect(
        host=MYSQL_HOST,
        user=MYSQL_USER,
        password=MYSQL_PASSWORD,
        database=MYSQL_DB
    )

# --- KMeans Location Model ---
KMEANS_FILE = "kmeans_model.pkl"
kmeans_model = None
trained_data = None
location_rows = None
cluster_variances = None
last_training_count = 0

def load_kmeans_model():
    global kmeans_model, trained_data, location_rows, cluster_variances, last_training_count
    if os.path.exists(KMEANS_FILE):
        saved = joblib.load(KMEANS_FILE)
        kmeans_model = saved.get("model")
        trained_data = saved.get("trained_data")
        location_rows = saved.get("location_rows")
        cluster_variances = saved.get("cluster_variances")
        last_training_count = len(location_rows)
        print("Loaded saved KMeans model.")

load_kmeans_model()

def train_kmeans():
    global kmeans_model, trained_data, location_rows, cluster_variances, last_training_count

    conn = get_mysql_connection()
    cursor = conn.cursor(dictionary=True)
    query = """
        SELECT province_id, city_id, barangay_id, COUNT(*) AS demand_30d
        FROM addresses
        WHERE patient_id IS NOT NULL
          AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY province_id, city_id, barangay_id
    """
    cursor.execute(query)
    location_rows = cursor.fetchall()
    
    if not location_rows:
        cursor.close()
        conn.close()
        return False

    trained_data = np.array([[row["demand_30d"]] for row in location_rows])
    num_clusters = 3 if len(trained_data) >= 3 else len(trained_data)
    kmeans_model = KMeans(n_clusters=num_clusters, random_state=42)
    kmeans_model.fit(trained_data)

    centroids = kmeans_model.cluster_centers_
    labels = kmeans_model.labels_

    cluster_variances = {}
    for c in range(num_clusters):
        points = trained_data[labels == c]
        cluster_variances[c] = float(np.mean(np.linalg.norm(points - centroids[c], axis=1))) if len(points) > 1 else 0.0001

    joblib.dump({
        "model": kmeans_model,
        "trained_data": trained_data,
        "location_rows": location_rows,
        "cluster_variances": cluster_variances
    }, KMEANS_FILE)

    last_training_count = len(location_rows)
    cursor.close()
    conn.close()
    return True

@app.get("/train/location")
def train_location():
    if train_kmeans():
        return {"status": "trained and saved", "rows": len(trained_data), "clusters": kmeans_model.n_clusters}
    return {"error": "No data to train on"}

@app.get("/forecastlocation")
def forecast_location():
    global kmeans_model, trained_data, location_rows, cluster_variances, last_training_count

    # retrain if new data
    conn = get_mysql_connection()
    cursor = conn.cursor()
    cursor.execute("SELECT COUNT(*) FROM addresses WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND patient_id IS NOT NULL")
    total_rows = cursor.fetchone()[0]
    cursor.close()
    conn.close()

    if total_rows != last_training_count or kmeans_model is None:
        train_kmeans()

    if kmeans_model is None:
        return {"error": "Model not trained."}

    labels = kmeans_model.labels_
    centroids = kmeans_model.cluster_centers_
    results = []
    today = datetime.today()
    actuals_all, predicted_all = [], []

    conn = get_mysql_connection()
    cursor = conn.cursor(dictionary=True)

    for idx, row in enumerate(location_rows):
        province_id = row["province_id"]
        city_id = row["city_id"]
        barangay_id = row["barangay_id"]
        cluster_id = int(labels[idx])

        cursor.execute("""
            SELECT DATE(created_at) AS day, COUNT(*) AS daily_demand
            FROM addresses
            WHERE patient_id IS NOT NULL
              AND province_id=%s AND city_id=%s AND barangay_id=%s
              AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at)
        """, (province_id, city_id, barangay_id))
        daily_data = cursor.fetchall()

        day_map = {r['day'].strftime('%Y-%m-%d'): r['daily_demand'] for r in daily_data}
        daily_series = np.array([day_map.get((today - timedelta(days=29-i)).strftime('%Y-%m-%d'), 0) for i in range(30)])

        X = np.arange(len(daily_series)).reshape(-1, 1)
        y = daily_series
        model = LinearRegression()
        model.fit(X, y)
        future_X = np.arange(len(daily_series), len(daily_series)+7).reshape(-1, 1)
        forecast_values = [max(0, int(round(val))) for val in model.predict(future_X)]

        distance = float(np.linalg.norm([sum(y)] - centroids[cluster_id]))
        variance = cluster_variances[cluster_id] or 0.0001
        error_margin = min(distance / variance, 1.0)

        forecast_dict = {(today + timedelta(days=i+1)).strftime("%Y-%m-%d"): forecast_values[i] for i in range(7)}

        results.append({
            "province_id": province_id,
            "city_id": city_id,
            "barangay_id": barangay_id,
            "demand_30d": int(sum(y)),
            "cluster": cluster_id,
            "distance_from_centroid": round(distance, 4),
            "cluster_variance": round(variance, 4),
            "error_margin": round(error_margin, 4),
            "forecast_next_7_days": forecast_dict
        })

        actuals_all.extend(y)
        predicted_all.extend(list(model.predict(X)))

    cursor.close()
    conn.close()

    try:
        mape = mean_absolute_percentage_error(actuals_all, predicted_all)
        rmse = mean_squared_error(actuals_all, predicted_all, squared=False)
        r2 = r2_score(actuals_all, predicted_all)
    except:
        mape = rmse = r2 = None

    return {"clusters": results, "forecast_metrics": {"MAPE": mape, "RMSE": rmse, "R2": r2}}


# --- Dynamic ARIMA Waitlist Model ---
WAITLIST_FILE = "waitlist_arima_model.pkl"
waitlist_model_fit = None
last_waitlist_count = 0

def load_waitlist_model():
    global waitlist_model_fit, last_waitlist_count
    if os.path.exists(WAITLIST_FILE):
        saved = joblib.load(WAITLIST_FILE)
        waitlist_model_fit = saved.get("model_fit")
        last_waitlist_count = saved.get("row_count")
        print("Loaded saved waitlist model.")

load_waitlist_model()

@app.get("/forecastwaitlist")
def forecast_waitlist(clinic_id: str = None):
    global waitlist_model_fit, last_waitlist_count

    conn = get_mysql_connection()
    cursor = conn.cursor(dictionary=True)
    
    # Get ALL historical data with optional clinic filter
    if clinic_id:
        cursor.execute("""
            SELECT DATE(w.requested_at_date) AS day, COUNT(*) AS waitlist_count
            FROM waitlist w
            JOIN patients p ON w.patient_id = p.patient_id
            WHERE p.clinic_id=%s
            GROUP BY DATE(w.requested_at_date)
            ORDER BY DATE(w.requested_at_date)
        """, (clinic_id,))
    else:
        cursor.execute("""
            SELECT DATE(requested_at_date) AS day, COUNT(*) AS waitlist_count
            FROM waitlist
            WHERE status='waiting'
            GROUP BY DATE(requested_at_date)
            ORDER BY DATE(requested_at_date)
        """)
    
    rows = cursor.fetchall()
    conn.close()

    if not rows:
        return {"error": "No waitlist data found"}

    # Prepare data
    df = pd.DataFrame(rows)
    df['day'] = pd.to_datetime(df['day'])
    df.set_index('day', inplace=True)
    
    # Fill missing dates with 0
    all_days = pd.date_range(df.index.min(), df.index.max(), freq='D')
    df = df.reindex(all_days, fill_value=0)
    series = df['waitlist_count'].values
    
    if len(series) < 7:
        avg_value = max(1, int(round(np.mean(series))))
        today = datetime.today()
        forecast_dict = {
            (today + timedelta(days=i+1)).strftime("%Y-%m-%d"): avg_value
            for i in range(7)
        }
        return {
            "forecast_metrics": {"note": "Insufficient data"},
            "forecast_next_7_days": forecast_dict
        }
    
    # Data diagnostics
    non_zero_days = np.count_nonzero(series)
    zero_days = len(series) - non_zero_days
    max_val = np.max(series)
    min_val = np.min(series)
    std_dev = np.std(series)
    mean_val = np.mean(series)
    
    # Calculate coefficient of variation (std/mean) - high CV means high variability
    cv = (std_dev / mean_val * 100) if mean_val > 0 else 0
    
    # Recent trend (last 30 days vs previous 30 days)
    if len(series) >= 60:
        recent_30 = np.mean(series[-30:])
        previous_30 = np.mean(series[-60:-30])
        trend_change = ((recent_30 - previous_30) / previous_30 * 100) if previous_30 > 0 else 0
    else:
        recent_30 = np.mean(series[-14:]) if len(series) >= 14 else mean_val
        trend_change = 0
    
    # Use simple moving average + trend for sparse data
    # This works better than ARIMA for highly variable small counts
    
    # Calculate 7-day and 14-day moving averages
    if len(series) >= 14:
        ma_7 = np.mean(series[-7:])
        ma_14 = np.mean(series[-14:])
        ma_30 = np.mean(series[-30:]) if len(series) >= 30 else ma_14
    else:
        ma_7 = ma_14 = ma_30 = mean_val
    
    # Weight recent data more heavily
    base_forecast = ma_7 * 0.5 + ma_14 * 0.3 + ma_30 * 0.2
    
    # Add trend component
    X = np.arange(len(series)).reshape(-1, 1)
    y = series
    lr_model = LinearRegression()
    lr_model.fit(X, y)
    trend_slope = lr_model.coef_[0]
    
    # Day of week pattern analysis
    df_dow = pd.DataFrame({
        'count': series,
        'dow': [(df.index.min() + timedelta(days=i)).weekday() for i in range(len(series))]
    })
    
    # Calculate day-of-week multipliers (only if enough data per day)
    dow_multipliers = {}
    for dow in range(7):
        dow_values = df_dow[df_dow['dow'] == dow]['count']
        if len(dow_values) >= 4:  # At least 4 occurrences
            dow_avg = dow_values.mean()
            dow_multipliers[dow] = dow_avg / mean_val if mean_val > 0 else 1.0
        else:
            dow_multipliers[dow] = 1.0
    
    # Generate forecast with variation based on day-of-week
    forecast_values = []
    today = datetime.today()
    
    for i in range(7):
        future_date = today + timedelta(days=i+1)
        future_dow = future_date.weekday()
        
        # Base forecast
        forecast = base_forecast
        
        # Apply trend (dampened for stability)
        forecast += trend_slope * (len(series) + i + 1) * 0.3
        
        # Apply day-of-week pattern
        dow_factor = dow_multipliers.get(future_dow, 1.0)
        forecast *= dow_factor
        
        # Add controlled randomness based on historical variance
        # Use smaller random variation for more stable forecasts
        if std_dev > 0:
            variation = np.random.normal(0, std_dev * 0.2)
            forecast += variation
        
        # Ensure positive integer, minimum 1 if historical data shows activity
        final_forecast = max(1, int(round(forecast))) if non_zero_days > 0 else 0
        
        forecast_values.append(final_forecast)
    
    # Calculate simple metrics using moving average as baseline
    predicted = np.convolve(series, np.ones(7)/7, mode='same')
    
    # Only calculate metrics on non-zero values to avoid inflation
    mask = series > 0
    if np.sum(mask) > 10:
        actual_nonzero = series[mask]
        predicted_nonzero = predicted[mask]
        
        mape = float(np.mean(np.abs((actual_nonzero - predicted_nonzero) / actual_nonzero)) * 100)
        rmse = float(np.sqrt(np.mean((series - predicted)**2)))
        r2 = float(r2_score(series, predicted)) if len(series) > 10 else 0
    else:
        # Use full series for sparse data
        mape = float(np.mean(np.abs((series - predicted) / (series + 0.01))) * 100)
        rmse = float(np.sqrt(np.mean((series - predicted)**2)))
        r2 = 0.0
    
    # Create forecast dictionary
    forecast_dict = {
        (today + timedelta(days=i+1)).strftime("%Y-%m-%d"): forecast_values[i] 
        for i in range(7)
    }
    
    return {
        "forecast_metrics": {
            "MAPE": round(mape, 2),
            "RMSE": round(rmse, 2),
            "R2": round(r2, 4),
            "data_points": len(series),
            "non_zero_days": int(non_zero_days),
            "zero_days": int(zero_days),
            "mean": round(mean_val, 2),
            "std_dev": round(std_dev, 2),
            "cv_percent": round(cv, 2),
            "recent_avg_7d": round(ma_7, 2),
            "recent_avg_14d": round(ma_14, 2),
            "trend_slope": round(trend_slope, 4),
            "trend_change_30d_pct": round(trend_change, 2),
            "forecast_method": "moving_average_with_dow_pattern",
            "clinic_id": clinic_id if clinic_id else "all"
        }, 
        "forecast_next_7_days": forecast_dict,
        "data_quality": {
            "status": "good" if cv < 100 and non_zero_days > len(series) * 0.5 else "sparse" if non_zero_days < len(series) * 0.3 else "moderate",
            "recommendation": "Consider collecting more consistent data" if cv > 150 else "Data suitable for forecasting"
        }
    }

@app.get("/forecastrevenue")
def forecast_revenue(clinic_id: str = None):
    """
    Forecast revenue for the next 7 days using log-transformed,
    zero-inflated model with day-of-week adjustments and exponential smoothing.
    """
    conn = get_mysql_connection()
    cursor = conn.cursor(dictionary=True)
    
    # Fetch data
    if clinic_id:
        cursor.execute("""
            SELECT DATE(p.paid_at_date) AS day, SUM(p.amount) AS daily_revenue
            FROM payments p
            WHERE p.clinic_id=%s AND p.paid_at_date IS NOT NULL
            GROUP BY DATE(p.paid_at_date)
            ORDER BY DATE(p.paid_at_date)
        """, (clinic_id,))
    else:
        cursor.execute("""
            SELECT DATE(paid_at_date) AS day, SUM(amount) AS daily_revenue
            FROM payments
            WHERE paid_at_date IS NOT NULL
            GROUP BY DATE(paid_at_date)
            ORDER BY DATE(paid_at_date)
        """)
    
    rows = cursor.fetchall()
    cursor.close()
    conn.close()
    
    if not rows:
        return {"error": "No revenue data found"}
    
    # Prepare DataFrame
    df = pd.DataFrame(rows)
    df['day'] = pd.to_datetime(df['day'])
    df['daily_revenue'] = df['daily_revenue'].astype(float)
    df.set_index('day', inplace=True)
    
    # Fill missing dates with 0
    all_days = pd.date_range(df.index.min(), df.index.max(), freq='D')
    df = df.reindex(all_days, fill_value=0.0)
    series = df['daily_revenue'].values.astype(float)
    
    today = datetime.today()
    
    if len(series) < 7:
        # Fallback: simple average if too little data
        avg_val = float(np.mean(series))
        forecast_dict = {
            (today + timedelta(days=i+1)).strftime("%Y-%m-%d"): round(avg_val, 2)
            for i in range(7)
        }
        return {
            "forecast_metrics": {"note": "Insufficient data, using average"},
            "forecast_next_7_days": forecast_dict
        }
    
    # Separate zero vs non-zero days for zero-inflation handling
    non_zero_mask = series > 0
    series_non_zero = series[non_zero_mask]
    
    # Log-transform non-zero revenue to reduce impact of spikes
    series_log = np.log1p(series_non_zero)
    
    # Linear trend on log-transformed non-zero revenue
    X = np.arange(len(series_non_zero)).reshape(-1, 1)
    ltm_model = LinearRegression()
    ltm_model.fit(X, series_log)
    
    trend_slope = float(ltm_model.coef_[0])
    trend_intercept = float(ltm_model.intercept_)
    
    # Exponential smoothing for recent trend capture
    if len(series_non_zero) >= 14:
        exp_model = ExponentialSmoothing(series_non_zero, trend="add", seasonal="add", seasonal_periods=7)
        exp_fit = exp_model.fit()
        exp_forecast = exp_fit.forecast(7)
    else:
        exp_forecast = np.full(7, series_non_zero[-1] if len(series_non_zero) > 0 else 0)
    
    # Day-of-week multipliers
    df_dow = pd.DataFrame({
        'revenue': series,
        'dow': [ (df.index.min() + timedelta(days=i)).weekday() for i in range(len(series)) ]
    })
    mean_val = float(series_non_zero.mean() if len(series_non_zero) > 0 else 0)
    dow_multipliers = {}
    for dow in range(7):
        dow_vals = df_dow[df_dow['dow'] == dow]['revenue']
        if len(dow_vals) > 0:
            dow_avg = float(dow_vals.mean())
            dow_multipliers[dow] = float(dow_avg / mean_val) if mean_val > 0 else 1.0
        else:
            dow_multipliers[dow] = 1.0
    
    # Generate forecast combining linear trend (log), exp smoothing, and DOW multiplier
    forecast_values = []
    for i in range(7):
        future_x = len(series_non_zero) + i + 1
        ltm_pred_log = ltm_model.predict(np.array([[future_x]]))[0]
        ltm_pred = np.expm1(ltm_pred_log)
        
        # Weighted combination
        base_forecast = 0.5 * ltm_pred + 0.5 * exp_forecast[i]
        
        # Apply day-of-week factor
        future_dow = (today + timedelta(days=i+1)).weekday()
        forecast = base_forecast * dow_multipliers.get(future_dow, 1.0)
        
        forecast_values.append(round(max(0, forecast), 2))
    
    # Metrics calculation on non-zero days
    ltm_pred_series = np.expm1(ltm_model.predict(X))
    mape = float(np.mean(np.abs((series_non_zero - ltm_pred_series) / (series_non_zero + 0.01))) * 100)
    rmse = float(np.sqrt(np.mean((series_non_zero - ltm_pred_series)**2)))
    r2 = float(r2_score(series_non_zero, ltm_pred_series))
    
    forecast_dict = {
        (today + timedelta(days=i+1)).strftime("%Y-%m-%d"): forecast_values[i]
        for i in range(7)
    }
    
    return {
        "forecast_metrics": {
            "MAPE": round(mape, 2),
            "RMSE": round(rmse, 2),
            "R2": round(r2, 4),
            "data_points": len(series),
            "non_zero_days": int(np.count_nonzero(series)),
            "zero_days": int(len(series) - np.count_nonzero(series)),
            "mean_daily": round(mean_val, 2),
            "trend_slope": round(trend_slope, 4),
            "total_forecast_7d": round(sum(forecast_values), 2),
            "forecast_method": "log_zero_inflated_ltm_exp_dow",
            "clinic_id": clinic_id if clinic_id else "all"
        },
        "forecast_next_7_days": forecast_dict,
        "data_quality": {
            "status": "good" if np.std(series) / mean_val < 1.5 else "moderate",
            "recommendation": "Data suitable for forecasting" if np.count_nonzero(series)/len(series) > 0.6 else "Consider collecting more consistent revenue data",
            "trend_direction": "increasing" if trend_slope > 0 else "decreasing" if trend_slope < 0 else "stable"
        }
    }

@app.get("/")
def root():
    return {"service": "AI forecast running"}
