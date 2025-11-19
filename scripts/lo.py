# location workingfrom fastapi import FastAPI
# import mysql.connector
# from sklearn.cluster import KMeans
# import numpy as np
# from datetime import datetime, timedelta
# import random  # for small daily variations

# app = FastAPI()

# kmeans_model = None
# trained_data = None
# location_rows = None
# cluster_variances = None

# MYSQL_HOST = "127.0.0.1"
# MYSQL_USER = "root"
# MYSQL_PASSWORD = ""
# MYSQL_DB = "dayao"

# def get_mysql_connection():
#     return mysql.connector.connect(
#         host=MYSQL_HOST,
#         user=MYSQL_USER,
#         password=MYSQL_PASSWORD,
#         database=MYSQL_DB
#     )

# @app.get("/train/location")
# def train_location():
#     global kmeans_model, trained_data, location_rows, cluster_variances

#     conn = get_mysql_connection()
#     cursor = conn.cursor(dictionary=True)

#     query = """
#         SELECT 
#             province_id,
#             city_id,
#             barangay_id,
#             COUNT(*) AS demand_30d
#         FROM addresses
#         WHERE patient_id IS NOT NULL
#           AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
#         GROUP BY province_id, city_id, barangay_id
#     """

#     cursor.execute(query)
#     location_rows = cursor.fetchall()
    
#     cursor.close()
#     conn.close()

#     if not location_rows:
#         return {"error": "No address data found in past 30 days"}

#     trained_data = np.array([[row["demand_30d"]] for row in location_rows])
#     num_clusters = 3 if len(trained_data) >= 3 else len(trained_data)

#     kmeans_model = KMeans(n_clusters=num_clusters, random_state=42)
#     kmeans_model.fit(trained_data)

#     centroids = kmeans_model.cluster_centers_
#     labels = kmeans_model.labels_

#     cluster_variances = {}
#     for c in range(num_clusters):
#         points = trained_data[labels == c]
#         if len(points) > 1:
#             distances = np.linalg.norm(points - centroids[c], axis=1)
#             cluster_variances[c] = float(np.mean(distances))
#         else:
#             cluster_variances[c] = 0.0001

#     return {
#         "status": "trained",
#         "rows": len(trained_data),
#         "clusters": num_clusters
#     }

# @app.get("/forecastlocation")
# def forecast_location():
#     global kmeans_model, trained_data, location_rows, cluster_variances

#     if kmeans_model is None:
#         return {"error": "Model not trained. Call /train/location first."}

#     labels = kmeans_model.labels_
#     centroids = kmeans_model.cluster_centers_

#     results = []
#     today = datetime.today()

#     for idx, row in enumerate(location_rows):
#         demand = trained_data[idx][0]
#         cluster_id = int(labels[idx])

#         distance = float(np.linalg.norm([demand] - centroids[cluster_id]))
#         variance = cluster_variances[cluster_id] or 0.0001
#         error_margin = min(distance / variance, 1.0)

#         # 7-day forecast based on cluster centroid ± small random variation
#         forecast = {}
#         centroid_value = int(round(centroids[cluster_id][0]))
#         for i in range(1, 8):
#             forecast_date = (today + timedelta(days=i)).strftime("%Y-%m-%d")
#             daily_variation = random.randint(-1, 1)  # small fluctuation
#             forecast[forecast_date] = max(0, centroid_value + daily_variation)

#         results.append({
#             "province_id": int(row["province_id"]),
#             "city_id": int(row["city_id"]),
#             "barangay_id": int(row["barangay_id"]),
#             "demand_30d": int(demand),
#             "cluster": cluster_id,
#             "distance_from_centroid": round(distance, 4),
#             "cluster_variance": round(variance, 4),
#             "error_margin": round(error_margin, 4),
#             "forecast_next_7_days": forecast
#         })

#     return {"clusters": results}

# @app.get("/")
# def root():
#     return {"service": "AI forecast running"}
