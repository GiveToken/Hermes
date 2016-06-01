<?php
use \Sizzle\Report;

if (!logged_in()) {
    header('Location: '.'/');
}

$dates = (new Report())->usageGrowth();
array_pop($dates);
$labels = '';
$tokenViews = '';
$emailsSent = '';
foreach ($dates as $date) {
  $labels .= "'".$date['Week Starting']."',";
  $tokenViews .= $date['Nonuser Token Views'].',';
  $emailsSent .= $date['Emails Sent'].',';
}
$dataObj = '{';
$dataObj .= "labels:[$labels],";
$dataObj .= "datasets:[";
$dataObj .= "{label:'Nonuser Token Views',data:[$tokenViews],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(75,192,192,1)',pointRadius:0},";
$dataObj .= "{label:'Emails Sent',data:[$emailsSent],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(255,206,86,1)',pointRadius:0},";
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
</style>
</head>
<body id="visitors">
  <div>
    <?php require __DIR__.'/../navbar.php';?>
  </div>
  <div class="row" id="org-info">
    <div class="col-sm-offset-1 col-sm-10">
      <h1>Usage Growth</h1>
      <canvas id="myChart" width="1000" height="400"></canvas>
      <p>
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
