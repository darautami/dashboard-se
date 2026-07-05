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
                        tension: 0.35,
                        pointRadius: 3,
                    },
                    {
                        label: 'Open',
                        data: trend.open,
                        borderColor: COLORS.open,
                        backgroundColor: COLORS.open,
                        tension: 0.35,
                        pointRadius: 3,
                    },
                    {
                        label: 'Draft',
                        data: trend.draft,
                        borderColor: COLORS.draft,
                        backgroundColor: COLORS.draft,
                        tension: 0.35,
                        pointRadius: 3,
                    },
                    {
                        label: 'Submitted by Pencacah',
                        data: trend.submitted,
                        borderColor: COLORS.submitted,
                        backgroundColor: COLORS.submitted,
                        tension: 0.35,
                        pointRadius: 3,
                    },
                    {
                        label: 'Approved by Pengawas',
                        data: trend.approved,
                        borderColor: COLORS.approved,
                        backgroundColor: COLORS.approved,
                        tension: 0.35,
                        pointRadius: 3,
                    },
                    {
                        label: 'Rejected by Pengawas',
                        data: trend.rejected,
                        borderColor: COLORS.rejected,
                        backgroundColor: COLORS.rejected,
                        tension: 0.35,
                        pointRadius: 3,
                    },
                ],
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
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
                cutout: '62%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } },
                },
            },
        });
    }
});