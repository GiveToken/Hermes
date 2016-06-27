<?php
use \Sizzle\Report;

if (!logged_in()) {
    header('Location: '.'/');
}

$dates = (new Report())->organizationGrowth();
array_pop($dates);
$labels = '';
$count = '';
$paying = '';
foreach ($dates as $date) {
  $labels .= "'".$date['Week Starting']."',";
  $count .= $date['active_organizations'].',';
  $paying .= $date['paying'].',';
}
$dataObj = '{';
$dataObj .= "labels:[$labels],";
$dataObj .= "datasets:[";
$dataObj .= "{label:'Active Organizations',data:[$count],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(75,192,192,1)',pointRadius:0},";
$dataObj .= "{label:'Paying Organizations',data:[$paying],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(250,128,114,1)',pointRadius:0},";
$dataObj .= ']}';

date_default_timezone_set('America/Chicago');

define('TITLE', 'Organization Growth');
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
      <h1>Active Organization Growth</h1>
      <canvas id="myChart" width="1000" height="400"></canvas>
      <p>
        * Includes only organizations with one or more users active
        (logged in or with a non-user token view) that week
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
