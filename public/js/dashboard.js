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

    // ---------------- Trend Line Chart ----------------
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
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 5,
                    },
                    
                    {
                        label: 'Open',
                        data: trend.open,
                        borderColor: COLORS.open,
                        backgroundColor: COLORS.open,
                        borderWidth: 3,
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 5,
                    },
                    {
                        label: 'Draft',
                        data: trend.draft,
                        borderColor: COLORS.draft,
                        backgroundColor: COLORS.draft,
                        borderWidth: 3,
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 5,
                    },
                    {
                        label: 'Submitted by Pencacah',
                        data: trend.submitted,
                        borderColor: COLORS.submitted,
                        backgroundColor: COLORS.submitted,
                        borderWidth: 3,
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 5,
                    },
                    {
                        label: 'Approved by Pengawas',
                        data: trend.approved,
                        borderColor: COLORS.approved,
                        backgroundColor: COLORS.approved,
                        borderWidth: 3,
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 5,
                    },
                    {
                        label: 'Rejected by Pengawas',
                        data: trend.rejected,
                        borderColor: COLORS.rejected,
                        backgroundColor: COLORS.rejected,
                        borderWidth: 3,
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 5,
                    },
                ],
            },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: false,

                            layout: {
                    padding: {
                        top: 10,
                        right: 10,
                        bottom: 10,
                        left: 10
                    }
                },

                    interaction: {
                    mode: 'index',
                    intersect: false
                },

                hover: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } },
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                },
            },
        });
    }

    const donutCanvas = document.getElementById('donutChart');
    if (donutCanvas) {
        new Chart(donutCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Open', 'Draft', 'Submitted by Pencacah', 'Approved by Pengawas', 'Rejected by Pengawas'],
                datasets: [{
                    data: [summary.open, summary.draft, summary.submitted, summary.approved, summary.rejected],
                    backgroundColor: [COLORS.open, COLORS.draft, COLORS.submitted, COLORS.approved, COLORS.rejected],
                    borderWidth: 0,
                }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    cutout: '62%',

                    layout: {
            padding: {
                top: 10,
                right: 10,
                bottom: 10,
                left: 10
            }
    },

                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } },
                },
            },
        });
    }
});