<?php
use \Sizzle\Report;

if (!logged_in()) {
    header('Location: '.'/');
}

$period = strtolower($_GET['period'] ?? '');
if (!in_array($period, ['weekly', 'monthly'])) {
    $period = 'weekly';
}

if (isset($_GET['reload']) && 'true' === $_GET['reload']) {
    unset($_SESSION['report'][$period.'UserGrowth']);
}

if (isset($_SESSION['report'][$period.'UserGrowth'])) {
    $dates = $_SESSION['report'][$period.'UserGrowth']['data'];
} else {
    $dates = (new Report())->userGrowth($period);
    array_pop($dates);
    $_SESSION['report'][$period.'UserGrowth']['data'] = $dates;
    $_SESSION['report'][$period.'UserGrowth']['cached'] = time();
}

$labels = '';
$count = '';
foreach ($dates as $date) {
  $labels .= "'".($date['Week Starting'] ?? $date['Month'])."',";
  $count .= $date['users'].',';
}
$dataObj = '{';
$dataObj .= "labels:[$labels],";
$dataObj .= "datasets:[";
$dataObj .= "{label:'Active Users',data:[$count],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(75,192,192,1)',pointRadius:0},";
$dataObj .= ']}';

date_default_timezone_set('America/Chicago');

define('TITLE', 'User Growth');
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
      <h1>Active User Growth</h1>
      <input id="period-weekly" type="radio" name="period" value="weekly" <?=($period=='weekly' ? 'checked' :'')?>> Weekly
      <input id="period-monthly" type="radio" name="period" value="monthly" <?=($period=='monthly' ? 'checked' :'')?>> Monthly
      <br />
      <canvas id="myChart" width="1000" height="400"></canvas>
      <p>
        * Includes only non-S!zzle users logged in or with a non-user token view.
        <br />
        ** Report has a one week cache duration
        (<a href="<?=$_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'], '?') ? '&' : '?')?>reload=true">reload</a>).
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
      window.location = '/report/user_growth?period='+this.value;
  })
  </script>
</body>
</html>
