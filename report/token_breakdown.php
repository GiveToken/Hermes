<?php
use \Sizzle\Report;

if (!logged_in()) {
    header('Location: '.'/');
}

// what are we breaking down by
$breakdown = strtolower($_GET['by'] ?? 'source');
if (!in_array($breakdown, ['source', 'os', 'browser'])) {
    $breakdown = 'source';
}

// by number or percent
$percent = strtolower($_GET['percent'] ?? 'no');
if (!in_array($percent, ['yes', 'no'])) {
    $percent = 'no';
}

switch ($breakdown) {
    case 'source':
        $dates = (new Report())->tokenSource();
        $twitter = '';
        $facebook = '';
        $linkedin = '';
        $other = '';
        break;
    case 'os':
        $dates = (new Report())->tokenOS();
        $osx = '';
        $ipad = '';
        $iphone = '';
        $android = '';
        $windows = '';
        $bot = '';
        break;
    case 'browser':
        $dates = (new Report())->tokenBrowser();
        $chrome = '';
        $firefox = '';
        $ie = '';
        $safari = '';
        $edge = '';
        $bot = '';
        break;
}
array_pop($dates);
$labels = '';
foreach ($dates as $date) {
    $labels .= "'".$date['Week Starting']."',";
    switch ($breakdown) {
        case 'source':
            $twitter .= ('no' == $percent ? $date['Twitter'] : round(100*$date['Twitter']/$date['total'],2)).',';
            $facebook .= ('no' == $percent ? $date['Facebook'] : round(100*$date['Facebook']/$date['total'],2)).',';
            $linkedin .= ('no' == $percent ? $date['LinkedIn'] : round(100*$date['LinkedIn']/$date['total'],2)).',';
            $other .= ('no' == $percent ? $date['Other'] : round(100*$date['Other']/$date['total'],2)).',';
            break;
        case 'os':
            $osx .= ('no' == $percent ? $date['osx'] : round(100*$date['osx']/$date['total'],2)).',';
            $ipad .= ('no' == $percent ? $date['ipad'] : round(100*$date['ipad']/$date['total'],2)).',';
            $iphone .= ('no' == $percent ? $date['iphone'] : round(100*$date['iphone']/$date['total'],2)).',';
            $android .= ('no' == $percent ? $date['android'] : round(100*$date['android']/$date['total'],2)).',';
            $windows .= ('no' == $percent ? $date['windows'] : round(100*$date['windows']/$date['total'],2)).',';
            $bot .= ('no' == $percent ? $date['bot'] : round(100*$date['bot']/$date['total'],2)).',';
            break;
        case 'browser':
            $chrome .= ('no' == $percent ? $date['chrome'] : round(100*$date['chrome']/$date['total'],2)).',';
            $firefox .= ('no' == $percent ? $date['firefox'] : round(100*$date['firefox']/$date['total'],2)).',';
            $ie .= ('no' == $percent ? $date['ie'] : round(100*$date['ie']/$date['total'],2)).',';
            $safari .= ('no' == $percent ? $date['safari'] : round(100*$date['safari']/$date['total'],2)).',';
            $edge .= ('no' == $percent ? $date['edge'] : round(100*$date['edge']/$date['total'],2)).',';
            $bot .= ('no' == $percent ? $date['bot'] : round(100*$date['bot']/$date['total'],2)).',';
            break;
    }
}
$dataObj = '{';
$dataObj .= "labels:[$labels],";
$dataObj .= "datasets:[";
switch ($breakdown) {
    case 'source':
        $dataObj .= "{label:'Twitter',data:[$twitter],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(75,192,192,1)',pointRadius:0},";
        $dataObj .= "{label:'Facebook',data:[$facebook],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(255,206,86,1)',pointRadius:0},";
        $dataObj .= "{label:'LinkedIn',data:[$linkedin],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(255,99,132,1)',pointRadius:0},";
        $dataObj .= "{label:'Other',data:[$other],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(255,1,86,200)',pointRadius:0},";
        break;
    case 'os':
        $dataObj .= "{label:'OS X',data:[$osx],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(75,192,192,1)',pointRadius:0},";
        $dataObj .= "{label:'iPad',data:[$ipad],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(255,206,86,1)',pointRadius:0},";
        $dataObj .= "{label:'iPhone',data:[$iphone],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(255,99,132,1)',pointRadius:0},";
        $dataObj .= "{label:'Android',data:[$android],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(255,1,86,200)',pointRadius:0},";
        $dataObj .= "{label:'Windows',data:[$windows],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(100,100,100,100)',pointRadius:0},";
        $dataObj .= "{label:'Bot',data:[$bot],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(5,150,86,200)',pointRadius:0},";
        break;
    case 'browser':
        $dataObj .= "{label:'Chrome',data:[$chrome],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(75,192,192,1)',pointRadius:0},";
        $dataObj .= "{label:'Firefox',data:[$firefox],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(255,206,86,1)',pointRadius:0},";
        $dataObj .= "{label:'Internet Explorer',data:[$ie],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(255,99,132,1)',pointRadius:0},";
        $dataObj .= "{label:'Safari',data:[$safari],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(255,1,86,200)',pointRadius:0},";
        $dataObj .= "{label:'Edge',data:[$edge],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(100,100,100,100)',pointRadius:0},";
        $dataObj .= "{label:'Bot',data:[$bot],backgroundColor:'rgba(0,0,0,0)',borderColor:'rgba(5,150,86,200)',pointRadius:0},";
        break;
}
$dataObj .= ']}';

date_default_timezone_set('America/Chicago');

define('TITLE', 'Token Breakdown');
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
      <h1>Token View Breakdown</h1>
      <input id="breakdown-source" type="radio" name="breakdown" value="source" <?=($breakdown=='source' ? 'checked' :'')?>> By Source
      <input id="breakdown-os" type="radio" name="breakdown" value="os" <?=($breakdown=='os' ? 'checked' :'')?>> By OS
      <input id="breakdown-browser" type="radio" name="breakdown" value="browser" <?=($breakdown=='browser' ? 'checked' :'')?>> By Browser
      <br />
      <input id="percent-no" type="radio" name="percent" value="no" <?=($percent=='no' ? 'checked' :'')?>> By Number
      <input id="percent-yes" type="radio" name="percent" value="yes" <?=($percent=='yes' ? 'checked' :'')?>> By Percent
      <br />
      <canvas id="myChart" width="1000" height="400"></canvas>
      <p>
      </p>
    </div>
  </div>
  * View count excludes logged in users<?=($breakdown=='source' ? ' and <a href="/bot_list">bots</a>' :'')?>.
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
  $('input[type=radio][name=breakdown]').change(function() {
      window.location = '/report/token_breakdown?by='+this.value+'&percent='+$('input[type=radio][name=percent]:checked').val();
  })
  $('input[type=radio][name=percent]').change(function() {
      window.location = '/report/token_breakdown?by='+$('input[type=radio][name=breakdown]:checked').val()+'&percent='+this.value;
  })
  </script>
</body>
</html>
