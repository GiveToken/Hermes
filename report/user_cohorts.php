<?php
use \Sizzle\Report;

if (!logged_in()) {
    header('Location: '.'/');
}

$report = new Report();
$dates = $report->userCohorts();
//echo '<pre>';print_r($dates);die;
array_pop($dates);
$labels = "'0','1','2','3','4','5','6','7','8','9','10','11','12',";
$rowData = array();
foreach ($dates as $date) {
    $newLabel = $date['Cohort Month']." (".$date['total'].")";
    $newRow = '';
    $newRow .= $date['active0month'] > 0 ? 100*$date['active0month']/$date['total'].',' : '';
    $newRow .= $date['active1month'] > 0 ? 100*$date['active1month']/$date['total'].',' : '';
    $newRow .= $date['active2month'] > 0 ? 100*$date['active2month']/$date['total'].',' : '';
    $newRow .= $date['active3month'] > 0 ? 100*$date['active3month']/$date['total'].',' : '';
    $newRow .= $date['active4month'] > 0 ? 100*$date['active4month']/$date['total'].',' : '';
    $newRow .= $date['active5month'] > 0 ? 100*$date['active5month']/$date['total'].',' : '';
    $newRow .= $date['active6month'] > 0 ? 100*$date['active6month']/$date['total'].',' : '';
    $newRow .= $date['active7month'] > 0 ? 100*$date['active7month']/$date['total'].',' : '';
    $newRow .= $date['active8month'] > 0 ? 100*$date['active8month']/$date['total'].',' : '';
    $newRow .= $date['active9month'] > 0 ? 100*$date['active9month']/$date['total'].',' : '';
    $newRow .= $date['active10month'] > 0 ? 100*$date['active10month']/$date['total'].',' : '';
    $newRow .= $date['active11month'] > 0 ? 100*$date['active11month']/$date['total'].',' : '';
    $newRow .= $date['active12month'] > 0 ? 100*$date['active12month']/$date['total'].',' : '';
    $rowData[] = array('data'=>$newRow, 'label'=>$newLabel);
}
$dataObj = '{';
$dataObj .= "labels:[$labels],";
$dataObj .= "datasets:[";
foreach ($rowData as $row) {
    $dataObj .= "{label:'{$row['label']}',data:[{$row['data']}],backgroundColor:'rgba(0,0,0,0)',borderColor:'".$report->randRGBA()."',pointRadius:0},";
}
$dataObj .= ']}';

date_default_timezone_set('America/Chicago');

define('TITLE', 'User Cohorts');
require __DIR__.'/../header.php';
?>
<style>
body {
  background-color: white;
}
#org-info {
  margin-top: 100px;
  color: black;
  text-align: left;
}
</style>
</head>
<body id="visitors">
  <div>
    <?php require __DIR__.'/../navbar.php';?>
  </div>
  <div class="row" id="org-info">
    <div class="col-sm-offset-1 col-sm-10">
      <h1>User Cohorts</h1>
      <i>Percentages of active users n months after user account creation</i>
      <br />
      <canvas id="myChart" width="1000" height="400"></canvas>
      <p>
        * Includes only non-S!zzle users.
      </p>
    </div>
  </div>
  <?php require __DIR__.'/../footer.php';?>
  <script src="/js/Chart.min.js"></script>
  <script>
  var ctx = document.getElementById("myChart");
  var myChart = new Chart(ctx, {
      type: 'line',
      data: <?=$dataObj?>,
      options: {
        responsive: false
      }
  });
  </script>
</body>
</html>
