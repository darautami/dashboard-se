document.addEventListener('DOMContentLoaded', function () {
    const dataEl = document.getElementById('dashboard-data');
    if (!dataEl) return;

    const payload = JSON.parse(dataEl.textContent);
    const trend = payload.trend;
    const summary = payload.summary;

    const COLORS = {
        total: '#64748b',
        open: '#0ea5e9',
        draft: '#d97706',
        submitted: '#2563eb',
        approved: '#059669',
        rejected: '#ef4444',
    };

    // ==================== TREND CHART ====================
    const trendCanvas = document.getElementById('trendChart');

    if (trendCanvas) {
        new Chart(trendCanvas, {
            type: 'line',
            data: {
                labels: trend.labels,
                datasets: [
                    {
                        label: 'Total Assignment',
                        data: trend.total,
                        borderColor: COLORS.total,
                        backgroundColor: COLORS.total,
                        borderWidth: 3,
                        fill: false,
                    },
                    {
                        label: 'Open',
                        data: trend.open,
                        borderColor: COLORS.open,
                        backgroundColor: COLORS.open,
                        borderWidth: 3,
                        fill: false,
                    },
                    {
                        label: 'Draft',
                        data: trend.draft,
                        borderColor: COLORS.draft,
                        backgroundColor: COLORS.draft,
                        borderWidth: 3,
                        fill: false,
                    },
                    {
                        label: 'Submitted by Pencacah',
                        data: trend.submitted,
                        borderColor: COLORS.submitted,
                        backgroundColor: COLORS.submitted,
                        borderWidth: 3,
                        fill: false,
                    },
                    {
                        label: 'Approved by Pengawas',
                        data: trend.approved,
                        borderColor: COLORS.approved,
                        backgroundColor: COLORS.approved,
                        borderWidth: 3,
                        fill: false,
                    },
                    {
                        label: 'Rejected by Pengawas',
                        data: trend.rejected,
                        borderColor: COLORS.rejected,
                        backgroundColor: COLORS.rejected,
                        borderWidth: 3,
                        fill: false,
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,

                layout: {
                    padding: 15
                },

                interaction: {
                    mode: 'index',
                    intersect: false
                },

                hover: {
                    mode: 'nearest',
                    intersect: false
                },

                elements: {
                    line: {
                        tension: 0.35,
                        borderWidth: 3
                    },

                    point: {
                        radius: 4,
                        hoverRadius: 6,
                        hitRadius: 10,
                        borderWidth: 2
                    }
                },

                plugins: {
                    legend: {
                        position: 'bottom',

                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 10,
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },

                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },

                scales: {
                    x: {
                        grid: {
                            display: false
                        },

                        ticks: {
                            maxRotation: 0
                        }
                    },

                    y: {
                        beginAtZero: true,

                        ticks: {
                            precision: 0
                        },

                        grid: {
                            color: '#e5e7eb'
                        }
                    }
                }
            }
        });
    }

    // ==================== DONUT CHART ====================
    const donutCanvas = document.getElementById('donutChart');

    if (donutCanvas) {
        new Chart(donutCanvas, {
            type: 'doughnut',

            data: {
                labels: [
                    'Open',
                    'Draft',
                    'Submitted by Pencacah',
                    'Approved by Pengawas',
                    'Rejected by Pengawas'
                ],

                datasets: [{
                    data: [
                        summary.open,
                        summary.draft,
                        summary.submitted,
                        summary.approved,
                        summary.rejected
                    ],

                    backgroundColor: [
                        COLORS.open,
                        COLORS.draft,
                        COLORS.submitted,
                        COLORS.approved,
                        COLORS.rejected
                    ],

                    borderWidth: 0
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,

                cutout: '62%',

                layout: {
                    padding: 15
                },

                plugins: {
                    legend: {
                        position: 'bottom',

                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 10,
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    }
});