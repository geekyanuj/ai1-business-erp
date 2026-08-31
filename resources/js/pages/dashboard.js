import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    // Product Status Doughnut Chart
    const productChartEl = document.getElementById('productStatusChart');
    if (productChartEl) {
        new Chart(productChartEl, {
            type: 'doughnut',
            data: {
                labels: window.productStatusData.map(i => i.category),
                datasets: [{
                    label: 'Products',
                     data: window.productStatusData.map(i => i.total),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Product Status Overview',
                        font: { size: 15, weight: 'bold' }
                    },
                    legend: { display: true, position: 'bottom' }
                }
            }
        });
    }






    

    // Order Status Line Chart
    const orderChartEl = document.getElementById('orderStatusChart');
    if (orderChartEl) {
        new Chart(orderChartEl, {
            type: 'line',
            data: {
                labels: window.orderStatusData.map(i => i.status),
                datasets: [{
                    label: 'Count of Orders',
                    data: window.orderStatusData.map(i => i.total),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: false,
                    tension: 0.4,
                    pointBackgroundColor: 'white',
                    pointBorderColor: 'rgba(75, 192, 192, 1)',
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: { display: true, text: 'Order Status Overview', font: { size: 15, weight: 'bold' } },
                    legend: { display: true, position: 'top' }
                },
                interaction: { mode: 'nearest', axis: 'x', intersect: false },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Count' } },
                    x: { title: { display: true, text: 'Month' } }
                }
            }
        });
    }
});
