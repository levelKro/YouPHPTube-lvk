<?php
if (!file_exists('../videos/configuration.php')) {
    if (!file_exists('../install/index.php')) {
        die("No Configuration and no Installation");
    }
    header("Location: install/index.php");
}

require_once '../videos/configuration.php';

require_once $global['systemRootPath'] . 'objects/user.php';
require_once $global['systemRootPath'] . 'objects/functions.php';
require_once $global['systemRootPath'] . 'objects/video.php';

$videos = Video::getAllVideos("viewableNotAd", true, true, array(), true);

$labelToday = array();
for ($i = 0; $i < 24; $i++) {
    $labelToday[] = "{$i} h";
}
$label7Days = array();
for ($i = 7; $i >= 0; $i--) {
    $label7Days[] = date("Y-m-d", strtotime("-{$i} days"));
}
$label30Days = array();
for ($i = 30; $i >= 0; $i--) {
    $label30Days[] = date("Y-m-d", strtotime("-{$i} days"));
}
$label90Days = array();
for ($i = 90; $i >= 0; $i--) {
    $label90Days[] = date("Y-m-d", strtotime("-{$i} days"));
}
$statistc_lastToday = VideoStatistic::getTotalToday("");
$statistc_last7Days = VideoStatistic::getTotalLastDays("", 7);
$statistc_last30Days = VideoStatistic::getTotalLastDays("", 30);
$statistc_last90Days = VideoStatistic::getTotalLastDays("", 90);

$bg = $bc = $labels = $labelsFull = $datas = $datas7 = $datas30 = $datasToday = array();
foreach ($videos as $value) {
    $labelsFull[] = $value["title"];
    $labels[] = substr($value["title"], 0, 20);
    $datas[] = $value["statistc_all"];
    $datasToday[] = $value["statistc_today"];
    $datas7[] = $value["statistc_week"];
    $datas30[] = $value["statistc_month"];
    $datasUnique[] = $value["statistc_unique_user"];
    $r = rand(0, 255);
    $g = rand(0, 255);
    $b = rand(0, 255);
    $bg[] = "rgba({$r}, {$g}, {$b}, 0.5)";
    $bc[] = "rgba({$r}, {$g}, {$b}, 1)";
}
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
    <head>
        <title>Chart - <?php echo $config->getWebSiteTitle(); ?></title>
        <meta name="generator" content="YouPHPTube - A Free Youtube Clone Script " />
        <?php
        include $global['systemRootPath'] . 'view/include/head.php';
        ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.bundle.min.js" integrity="sha256-+q+dGCSrVbejd3MDuzJHKsk2eXd4sF5XYEMfPZsOnYE=" crossorigin="anonymous"></script>
        
    </head>
    <body>
        <?php
        include 'include/navbar.php';
        //var_dump($videos);
        ?>
        <div class="container-fluid">
            <nav class="navbar navbar-default nav-chart">
                <div class="container-fluid">
                    <div class="btn-group">
                    <button class="btn btn-primary navbar-btn active" id="btnAll" ><?php echo __("Total Views"); ?></button>
                    <button class="btn btn-warning navbar-btn" id="btnToday"><?php echo __("Today Views"); ?></button>
                    <button class="btn btn-default navbar-btn" id="btn7"><?php echo __("Last 7 Days"); ?></button>
                    <button class="btn btn-default navbar-btn" id="btn30" ><?php echo __("Last 30 Days"); ?></button>
                    <!--
                    <button class="btn btn-default navbar-btn" id="btnUnique" ><?php echo __("Unique Users"); ?></button>
                    --></div>
                </div>
            </nav>

            <div class="col-md-3">

                <div class="panel panel-default">
                    <div class="panel-heading when"><?php echo __("Color Legend"); ?></div>
                    <div class="panel-body" style="height: 600px; overflow-y: scroll;">
                        <div class="list-group">

                            <?php
                            foreach ($labelsFull as $key => $value) {
                                ?>
                                <a class="list-group-item " style="border-color: <?= $bg[$key] ?>; border-width: 1px 20px 1px 5px; font-size: 0.9em;">
                                    <?= $value ?>
                                </a>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading when"># <?php echo __("Total Views"); ?></div>
                            <div class="panel-body">
                                <canvas id="myChart" height="60" ></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading when"># <?php echo __("Total Views"); ?></div>
                            <div class="panel-body">
                                <canvas id="myChartPie" height="200"  ></canvas> 
                            </div>
                        </div>                       
                    </div>
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-heading when"># <?php echo __("Timeline"); ?></div>
                            <div class="panel-body" id="timeline">
                                <canvas id="myChartLine" height="90"  ></canvas> 
                            </div>
                        </div>                       
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading when"># <?php echo __("Total Views Today"), " - ", date("Y-m-d"); ?></div>
                            <div class="panel-body">
                                <canvas id="myChartLineToday" height="60" ></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            var ctx = document.getElementById("myChart");
            var ctxPie = document.getElementById("myChartPie");
            var ctxLine = document.getElementById("myChartLine");
            var ctxLineToday = document.getElementById("myChartLineToday");
            var chartData = {
                labels: <?php echo json_encode($labelsFull); ?>,
                datasets: [{
                        label: '# <?php echo __("Total Views"); ?>',
                        data: <?php echo json_encode($datas); ?>,
                        backgroundColor: <?php echo json_encode($bg); ?>,
                        borderColor: <?php echo json_encode($bc); ?>,
                        borderWidth: 1
                    }]
            };
            
            var lineChartData = {
                labels: <?php echo json_encode($label90Days); ?>,
                datasets: [{
                        backgroundColor: 'rgba(255, 0, 0, 0.3)',
                        borderColor: 'rgba(255, 0, 0, 0.5)',
                        label: '# <?php echo __("Total Views (90 Days)"); ?>',
                        data: <?php echo json_encode($statistc_last90Days); ?>
                    }]
            };
            
            var lineChartDataToday = {
                labels: <?php echo json_encode($labelToday); ?>,
                datasets: [{
                        backgroundColor: 'rgba(0, 0, 255, 0.3)',
                        borderColor: 'rgba(0, 0, 255, 0.5)',
                        label: '# <?php echo __("Total Views (Today)"); ?>',
                        data: <?php echo json_encode($statistc_lastToday); ?>
                    }]
            };

            var myChart = new Chart(ctx, {
                type: 'bar',
                data: chartData,
                options: {
                    scales: {
                        yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    callback: function (value, index, values) {
                                        if (Math.floor(value) === value) {
                                            return value;
                                        }
                                    }
                                }
                            }],
                        xAxes: [{
                                display: false
                            }]
                    },
                    legend: {
                        display: false
                    },
                    responsive: true
                }
            });
            var myChartPie = new Chart(ctxPie, {
                type: 'pie',
                data: chartData,
                options: {
                    legend: {
                        display: false
                    },
                    responsive: true
                }
            });

            var myChartLine = new Chart(ctxLine, {
                type: 'line',
                data: lineChartData,
                fill: false,
                options: {
                    scales: {
                        yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    callback: function (value, index, values) {
                                        if (Math.floor(value) === value) {
                                            return value;
                                        }
                                    }
                                }
                            }]
                    },
                    legend: {
                        display: false
                    },
                    responsive: true,
                    title: {
                        display: true
                    }
                }
            });
            
            var myChartLineToday = new Chart(ctxLineToday, {
                type: 'line',
                data: lineChartDataToday,
                fill: false,
                options: {
                    scales: {
                        yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    callback: function (value, index, values) {
                                        if (Math.floor(value) === value) {
                                            return value;
                                        }
                                    }
                                }
                            }]
                    },
                    legend: {
                        display: false
                    },
                    responsive: true,
                    title: {
                        display: true
                    }
                }
            });

            $(document).ready(function () {

                $('#btnAll').click(function () {
                    $('.nav-chart .btn').removeClass('active');
                    $(this).addClass('active');
                    chartData.datasets[0].data = <?php echo json_encode($datas); ?>;
                    chartData.datasets[0].label = '# <?php echo __("Total Views"); ?>';
                    lineChartData.labels = <?php echo json_encode($label90Days); ?>;
                    lineChartData.datasets[0].data = <?php echo json_encode($statistc_last90Days); ?>;
                    lineChartData.datasets[0].label = '# <?php echo __("Total Views (90 Days)"); ?>';
                    myChart.update();
                    myChartPie.update();
                    myChartLine.update();
                });
                $('#btnToday').click(function () {
                    $('.nav-chart .btn').removeClass('active');
                    $(this).addClass('active');
                    chartData.datasets[0].data = <?php echo json_encode($datasToday); ?>;
                    chartData.datasets[0].label = '# <?php echo __("Today"); ?>';
                    lineChartData.labels = <?php echo json_encode($labelToday); ?>;
                    lineChartData.datasets[0].data = <?php echo json_encode($statistc_lastToday); ?>;
                    lineChartData.datasets[0].label = '# <?php echo __("Today"); ?>';
                    myChart.update();
                    myChartPie.update();
                    myChartLine.update();
                });
                $('#btn7').click(function () {
                    $('.nav-chart .btn').removeClass('active');
                    $(this).addClass('active');
                    chartData.datasets[0].data = <?php echo json_encode($datas7); ?>;
                    chartData.datasets[0].label = '# <?php echo __("Last 7 Days"); ?>';
                    lineChartData.labels = <?php echo json_encode($label7Days); ?>;
                    lineChartData.datasets[0].data = <?php echo json_encode($statistc_last7Days); ?>;
                    lineChartData.datasets[0].label = '# <?php echo __("Last 7 Days"); ?>';
                    myChart.update();
                    myChartPie.update();
                    myChartLine.update();
                });
                $('#btn30').click(function () {
                    $('.nav-chart .btn').removeClass('active');
                    $(this).addClass('active');
                    chartData.datasets[0].data = <?php echo json_encode($datas30); ?>;
                    chartData.datasets[0].label = '# <?php echo __("Last 30 Days"); ?>';
                    lineChartData.labels = <?php echo json_encode($label30Days); ?>;
                    lineChartData.datasets[0].data = <?php echo json_encode($statistc_last30Days); ?>;
                    lineChartData.datasets[0].label = '# <?php echo __("Last 30 Days"); ?>';
                    myChart.update();
                    myChartPie.update();
                    myChartLine.update();
                });
                $('#btnUnique').click(function () {
                    $('.nav-chart .btn').removeClass('active');
                    $(this).addClass('active');
                    chartData.datasets[0].data = <?php echo json_encode($datasUnique); ?>;
                    chartData.datasets[0].label = '# <?php echo __("Unique Users"); ?>';
                    myChart.update();
                    myChartPie.update();
                });
            });
        </script>
        <?php
        include 'include/footer.php';
        ?>


    </body>
</html>
