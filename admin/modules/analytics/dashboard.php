// admin/modules/analytics/dashboard.php
<?php
require_once '../../../includes/auth-check.php';
require_once '../../../config/database.php';

// Date range (default last 30 days)
$dateFrom = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$dateTo = $_GET['to'] ?? date('Y-m-d');

// Get visitor statistics
$visitors = $pdo->query("
    SELECT 
        DATE(visit_date) as date,
        COUNT(*) as visits,
        COUNT(DISTINCT ip_address) as unique_visitors,
        SUM(page_views) as page_views,
        AVG(time_spent) as avg_time
    FROM SiteVisits
    WHERE DATE(visit_date) BETWEEN '$dateFrom' AND '$dateTo'
    GROUP BY DATE(visit_date)
    ORDER BY date ASC
")->fetchAll();

// Get traffic sources
$trafficSources = $pdo->query("
    SELECT 
        source,
        COUNT(*) as visits,
        ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM SiteVisits WHERE DATE(visit_date) BETWEEN '$dateFrom' AND '$dateTo'), 1) as percentage
    FROM SiteVisits
    WHERE DATE(visit_date) BETWEEN '$dateFrom' AND '$dateTo'
    GROUP BY source
    ORDER BY visits DESC
")->fetchAll();

// Get popular pages
$popularPages = $pdo->query("
    SELECT 
        page_url,
        page_title,
        COUNT(*) as views,
        COUNT(DISTINCT ip_address) as unique_views
    FROM PageViews
    WHERE DATE(view_date) BETWEEN '$dateFrom' AND '$dateTo'
    GROUP BY page_url, page_title
    ORDER BY views DESC
    LIMIT 10
")->fetchAll();

// Get user acquisition
$userAcquisition = $pdo->query("
    SELECT 
        DATE(created_at) as date,
        COUNT(*) as signups,
        SUM(CASE WHEN verified = 1 THEN 1 ELSE 0 END) as verified
    FROM usersmanage
    WHERE DATE(created_at) BETWEEN '$dateFrom' AND '$dateTo'
    GROUP BY DATE(created_at)
    ORDER BY date ASC
")->fetchAll();

include '../../../includes/header.php';
?>

<div class="container-fluid">
    <!-- Date Range Selector -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Analytics Dashboard</h6>
            <form method="GET" class="form-inline">
                <div class="input-group">
                    <input type="date" class="form-control" name="from" value="<?= $dateFrom ?>">
                    <span class="input-group-text">to</span>
                    <input type="date" class="form-control" name="to" value="<?= $dateTo ?>">
                    <button type="submit" class="btn btn-primary">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Visits</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format(array_sum(array_column($visitors, 'visits'))) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Other summary cards... -->
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Visitors Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Visitors Overview</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                            <a class="dropdown-item" href="analytics-export.php?type=visitors&from=<?= $dateFrom ?>&to=<?= $dateTo ?>">Export Data</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="visitorsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Traffic Sources -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Traffic Sources</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="trafficSourcesChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <?php foreach ($trafficSources as $source): ?>
                        <span class="mr-2">
                            <i class="fas fa-circle" style="color: <?= getSourceColor($source['source']) ?>"></i>
                            <?= $source['source'] ?> (<?= $source['percentage'] ?>%)
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row -->
    <div class="row">
        <!-- Popular Pages -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Pages</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Page</th>
                                    <th>Views</th>
                                    <th>Unique</th>
                                    <th>% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($popularPages as $page): ?>
                                <tr>
                                    <td>
                                        <a href="<?= $page['page_url'] ?>" target="_blank">
                                            <?= $page['page_title'] ? htmlspecialchars($page['page_title']) : $page['page_url'] ?>
                                        </a>
                                    </td>
                                    <td><?= number_format($page['views']) ?></td>
                                    <td><?= number_format($page['unique_views']) ?></td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?= min(100, ($page['views'] / array_sum(array_column($popularPages, 'views')) * 100) ?>%" 
                                                 aria-valuenow="<?= ($page['views'] / array_sum(array_column($popularPages, 'views')) * 100 ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                        <?= round(($page['views'] / array_sum(array_column($popularPages, 'views')) * 100, 1) ?>%
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Acquisition -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Acquisition</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="acquisitionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Third Row - Additional Metrics -->
    <div class="row">
        <!-- Device Breakdown -->
        <div class="col-xl-4 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Devices</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="devicesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Geographic Data -->
        <div class="col-xl-8 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Geographic Distribution</h6>
                </div>
                <div class="card-body">
                    <div id="geoMap" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Charts -->
<script>
// Visitors Chart
const visitorsCtx = document.getElementById('visitorsChart').getContext('2d');
const visitorsChart = new Chart(visitorsCtx, {
    type: 'line',
    data: {
        labels: [<?= implode(',', array_map(function($v) { return "'" . date('M j', strtotime($v['date'])) . "'"; }, $visitors)) ?>],
        datasets: [
            {
                label: 'Visits',
                data: [<?= implode(',', array_column($visitors, 'visits')) ?>],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                tension: 0.3
            },
            {
                label: 'Unique Visitors',
                data: [<?= implode(',', array_column($visitors, 'unique_visitors')) ?>],
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.05)',
                tension: 0.3
            }
        ]
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Traffic Sources Chart
const trafficSourcesCtx = document.getElementById('trafficSourcesChart').getContext('2d');
const trafficSourcesChart = new Chart(trafficSourcesCtx, {
    type: 'doughnut',
    data: {
        labels: [<?= implode(',', array_map(function($v) { return "'" . $v['source'] . "'"; }, $trafficSources)) ?>],
        datasets: [{
            data: [<?= implode(',', array_column($trafficSources, 'visits')) ?>],
            backgroundColor: [<?= implode(',', array_map(function($v) { return "'" . getSourceColor($v['source']) . "'"; }, $trafficSources)) ?>],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        cutout: '70%',
    },
});

// Initialize other charts similarly...
</script>

<!-- Load Map Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
<script>
// Initialize geographic map
const map = L.map('geoMap').setView([20, 0], 2);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Add your geographic data here...
</script>

<?php include '../../../includes/footer.php'; ?>