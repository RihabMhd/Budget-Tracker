let trendChart = null;
let categoryChart = null;

function renderCategoryChart(data) {
    const canvas = document.getElementById('categoryDonutChart');
    if (!canvas || !data || data.length === 0) return;

    if (categoryChart) {
        categoryChart.destroy();
    }

    categoryChart = new Chart(canvas.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: data.map(i => i.name || 'Unknown'),
            datasets: [{
                data: data.map(i => i.amount || 0),
                backgroundColor: data.map(i => i.color || '#FBCF97'),
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}
function renderTrendChart(data) {
    const canvas = document.getElementById('analyticsTrendChart');
    if (!canvas) return;

    if (trendChart) {
        trendChart.destroy();
    }

    trendChart = new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels: data.chartMonths,
            datasets: [{
                label: 'Spending',
                data: data.chartExpenses,
                backgroundColor: '#FBCF97',
                borderRadius: 8,
            }, {
                label: 'Budget Limit',
                data: data.chartAllowances,
                type: 'line',
                borderColor: '#1C1C1E',
                borderDash: [5, 5],
                pointRadius: 0,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
        }
    });
}


window.addEventListener('load', () => {
    if (window.chartData) {
        renderTrendChart(window.chartData);
    }

    if (window.categoryData) {
        renderCategoryChart(window.categoryData);
    }
});