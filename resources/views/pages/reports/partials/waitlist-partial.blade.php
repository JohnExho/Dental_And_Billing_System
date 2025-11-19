<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2"></i> Waitlist Detail</h6>
        <select id="timePeriod" class="form-select form-select-sm w-auto">
            <option value="daily">Daily</option>
            <option value="weekly" selected>Weekly</option>
            <option value="monthly">Monthly</option>
            <option value="quarterly">Quarterly</option>
            <option value="semi-annual">Semi-Annual</option>
            <option value="annually">Annually</option>
        </select>
    </div>
    <div class="card-body">
        <canvas id="waitlistDetailChart" style="height:400px;"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let chart = null;

    function aggregateWaitlistData(period) {
        const data = window.waitlistData || {};
        const entries = Object.entries(data).sort((a,b)=> moment(a[0]) - moment(b[0]));
        const now = moment();
        let labels = [], values = [];

        const mapByDay = (daysBack) => {
            const map = {};
            entries.forEach(([date,count]) => {
                const day = moment(date).format('YYYY-MM-DD');
                map[day] = (map[day] || 0) + count;
            });
            for (let i=daysBack-1;i>=0;i--) {
                const day = now.clone().subtract(i,'days').format('YYYY-MM-DD');
                labels.push(moment(day).format('MMM DD'));
                values.push(map[day] || 0);
            }
        };

        const mapByMonth = (monthsBack) => {
            const map = {};
            entries.forEach(([date,count]) => {
                const month = moment(date).format('YYYY-MM');
                map[month] = (map[month] || 0) + count;
            });
            for (let i=monthsBack-1;i>=0;i--) {
                const month = now.clone().subtract(i,'months').format('YYYY-MM');
                labels.push(moment(month).format('MMM YYYY'));
                values.push(map[month] || 0);
            }
        };

        if (period==='daily') {
            const today = now.format('YYYY-MM-DD');
            const hourlyMap = {};
            entries.forEach(([date,count]) => {
                if(moment(date).format('YYYY-MM-DD')===today) {
                    const hour = moment(date).hour();
                    hourlyMap[hour] = (hourlyMap[hour] || 0) + count;
                }
            });
            for(let i=0;i<24;i++){
                labels.push(moment(`${today} ${i}:00`,'YYYY-MM-DD HH:mm').format('HH:mm'));
                values.push(hourlyMap[i]||0);
            }
        } else if(period==='weekly') mapByDay(7);
        else if(period==='monthly') mapByDay(31);
        else if(period==='quarterly') mapByMonth(4);
        else if(period==='semi-annual') mapByMonth(6);
        else if(period==='annually') mapByMonth(12);

        return { labels, values };
    }

    function initChart() {
        const ctx = document.getElementById('waitlistDetailChart');
        if(!ctx) return;

        const period = document.getElementById('timePeriod')?.value || 'weekly';
        const {labels, values} = aggregateWaitlistData(period);

        let allLabels = [...labels];
        let historicalData = [...values];
        let forecastData = [];

        if(period==='weekly' && window.forecastedWaitlistValue) {
            const forecast = window.forecastedWaitlistValue.forecast_next_7_days || {};
            const forecastDates = Object.keys(forecast).sort();
            const forecastLabels = forecastDates.map(d => moment(d).format('MMM DD'));
            const forecastValues = forecastDates.map(d => forecast[d]);
            allLabels = [...allLabels, ...forecastLabels];
            historicalData = [...historicalData, ...new Array(forecastValues.length).fill(null)];
            forecastData = [...new Array(values.length).fill(null), ...forecastValues];
        }

        if(chart) chart.destroy();

        chart = new Chart(ctx.getContext('2d'), {
            type:'line',
            data:{
                labels: allLabels,
                datasets:[
                    {
                        label:'Historical Waitlist',
                        data: historicalData,
                        borderColor:'#0d6efd',
                        backgroundColor:'rgba(13,110,253,0.2)',
                        fill:true,
                        tension:0.4,
                        spanGaps:false
                    },
                    {
                        label:'Forecasted Waitlist',
                        data: forecastData,
                        borderColor:'#fd7e14',
                        backgroundColor:'rgba(253,126,20,0.2)',
                        fill:true,
                        tension:0.4,
                        borderDash:[5,5],
                        spanGaps:true
                    }
                ]
            },
            options:{
                responsive:true,
                maintainAspectRatio:false,
                interaction:{ mode:'index', intersect:false },
                scales:{ y:{ beginAtZero:true, ticks:{ stepSize:1 } } },
                plugins:{
                    legend:{ display:true, position:'top' },
                    tooltip:{
                        mode:'index',
                        intersect:false,
                        filter: t => t.parsed.y !== null
                    }
                }
            }
        });
    }

    document.getElementById('timePeriod')?.addEventListener('change', e => initChart());
    initChart();
});
</script>
