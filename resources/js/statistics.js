import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

document.addEventListener('DOMContentLoaded', function () {
    var dataEl = document.getElementById('stats-data');
    if (!dataEl) return;

    var data;
    try {
        data = JSON.parse(dataEl.textContent);
    } catch (e) {
        return;
    }

    const colors = [
        '#6366f1', '#10b981', '#f59e0b', '#ef4444', '#3b82f6',
        '#8b5cf6', '#ec4899', '#14b8a6', '#f97316', '#84cc16',
        '#06b6d4', '#d946ef', '#22c55e', '#eab308', '#64748b'
    ];

    function getCtx(id) {
        var el = document.getElementById(id);
        return el ? el.getContext('2d') : null;
    }

    var ctx;

    if (data.statusLabels) {
        ctx = getCtx('statusChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.statusLabels,
                    datasets: [{
                        data: data.statusValues,
                        backgroundColor: ['#10b981', '#6b7280', '#ef4444'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 10, usePointStyle: true, boxWidth: 10, font: { size: 11 } } }
                    }
                }
            });
        }
    }

    if (data.typeLabels) {
        ctx = getCtx('typeChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.typeLabels,
                    datasets: [{
                        label: 'Documents',
                        data: data.typeValues,
                        backgroundColor: colors.slice(0, data.typeLabels.length),
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } },
                        y: { ticks: { font: { size: 10 } } }
                    }
                }
            });
        }
    }

    if (data.artaLabels) {
        ctx = getCtx('artaChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.artaLabels,
                    datasets: [{
                        data: data.artaValues,
                        backgroundColor: colors.slice(0, data.artaLabels.length),
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 10, usePointStyle: true, boxWidth: 10, font: { size: 11 } } }
                    }
                }
            });
        }
    }

    if (data.trendLabels) {
        ctx = getCtx('trendChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.trendLabels,
                    datasets: [{
                        label: 'Documents Created',
                        data: data.trendValues,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99,102,241,0.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 2,
                        pointBackgroundColor: '#6366f1',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } },
                        x: { ticks: { font: { size: 10 } } }
                    }
                }
            });
        }
    }

    if (data.officeLabels) {
        ctx = getCtx('officeChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.officeLabels,
                    datasets: [
                        {
                            label: 'Handled',
                            data: data.officeHandled,
                            backgroundColor: 'rgba(99,102,241,0.7)',
                            borderRadius: 4,
                        },
                        {
                            label: 'Avg Hours',
                            data: data.officeAvgHours,
                            backgroundColor: 'rgba(245,158,11,0.7)',
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 10, usePointStyle: true, boxWidth: 10, font: { size: 11 } } }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { font: { size: 10 } } },
                        x: { ticks: { font: { size: 10 } } }
                    }
                }
            });
        }
    }
});
