<?php
use \Sizzle\Report;

if (!logged_in()) {
    header('Location: '.'/');
}

$rates = (new Report())->responseRate();
array_pop($rates);
$labels = '';
$yes = '';
$no = '';
$maybe = '';
foreach ($rates as $rate) {
  $labels .= "'".$rate['Week Starting']."',";
  $yes .= $rate['Yes %'].',';
  $maybe .= ($rate['Yes %']+$rate['Maybe %']).',';
  $no .= ($rate['Yes %']+$rate['No %']+$rate['Maybe %']).',';
}
$dataObj = '{';
$dataObj .= "labels:[$labels],";
$dataObj .= "datasets:[";
$dataObj .= "{label:'Yes',data:[$yes],backgroundColor:'rgba(75,192,192,1)',borderColor:'rgba(75,192,192,1)',pointRadius:0},";
$dataObj .= "{label:'Maybe',data:[$maybe],backgroundColor:'rgba(255,206,86,1)',borderColor:'rgba(255,206,86,1)',pointRadius:0},";
$dataObj .= "{label:'No',data:[$no],backgroundColor:'rgba(255,99,132,1)',borderColor:'rgba(255,99,132,1)',pointRadius:0}";
$dataObj .= ']}';
//echo $dataObj; die;

date_default_timezone_set('America/Chicago');

define('TITLE', 'Repsonse Rate');
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
.greyed {
  background-color: lightgrey;
  font-style: normal;
}
canvas {
  height:400px;
}
</style>
</head>
<body id="visitors">
  <div>
    <?php require __DIR__.'/../navbar.php';?>
  </div>
  <div class="row" id="org-info">
    <div class="col-sm-offset-1 col-sm-10">
      <h1>Response Rates</h1>
      <canvas id="myChart" width="1000" height="400"></canvas>
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
