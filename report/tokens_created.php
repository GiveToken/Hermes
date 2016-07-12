<?php
use \Sizzle\Report;

if (!logged_in()) {
    header('Location: '.'/');
}

$period = strtolower($_GET['period']) ?? 'weekly';

$dates = (new Report())->tokensCreated($period);
array_pop($dates);
$labels = '';
$count = '';
foreach ($dates as $date) {
  $labels .= "'".($date['Week Starting'] ?? $date['Month'])."',";
  $count .= $date['tokens'].',';
}
$dataObj = '{';
$dataObj .= "labels:[$labels],";
$dataObj .= "datasets:[";
$dataObj .= "{label:'Tokens',data:[$count],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(75,192,192,1)',pointRadius:0},";
$dataObj .= ']}';

date_default_timezone_set('America/Chicago');

define('TITLE', 'Tokens Created');
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
      <h1>Tokens Created</h1>
      <input id="period-weekly" type="radio" name="period" value="weekly" <?=($period=='weekly' ? 'checked' :'')?>> Weekly
      <input id="period-monthly" type="radio" name="period" value="monthly" <?=($period=='monthly' ? 'checked' :'')?>> Monthly
      <br />
      <canvas id="myChart" width="1000" height="400"></canvas>
      <p>
        * Includes only tokens owned by non-S!zzle users.
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
  $('input[type=radio][name=period]').change(function() {
      window.location = '/report/tokens_created?period='+this.value;
  })
  </script>
</body>
</html>
