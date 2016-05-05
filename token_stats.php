<?php
use \Sizzle\Bacon\Database\RecruitingToken;

date_default_timezone_set('America/Chicago');

if (!logged_in()) {
  login_then_redirect_back_here();
}
$user_id = $_SESSION['user_id'] ?? '';


define('TITLE', 'S!zzle - Tokens');
require __DIR__.'/header.php';
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/s/dt/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.10,b-1.1.0,b-flash-1.1.0,b-html5-1.1.0,b-print-1.1.0/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="/css/datatables.min.css"/>

</head>
<body id="token-listing">
  <div>
    <?php require __DIR__.'/navbar.php';?>
  </div>
  <div class="row" id="datatable-div">
    <div class="col-sm-offset-2 col-sm-8">
      <h2>Token View Statistics</h2>
      <p>
        (These only include non-user token views.)
      </p>

      <h3>by operating system</h3>
      <table id="responsive-table" class="table table-striped table-hover">
        <thead>
          <th>Operating System</th>
          <th>Count</th>
          <th>Percent</th>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT SUM(IF(user_agent LIKE '%Macintosh%',1, 0)) as osx,
                    SUM(IF(user_agent LIKE '%iPad%',1, 0)) as ipad,
                    SUM(IF(user_agent LIKE '%iPhone OS%',1, 0)) as iphone,
                    SUM(IF(user_agent LIKE '%Android%',1, 0)) as android,
                    SUM(IF(user_agent LIKE '%Windows NT%',1, 0)) as windows,
                    SUM(IF(user_agent LIKE '%bot%',1, 0)) as bot,
                    COUNT(*) total
                    FROM giftbox.web_request
                    WHERE uri LIKE '/token/recruiting%'
                    AND user_id IS NULL";
            $row = (execute_query($sql))->fetch_all(MYSQLI_ASSOC)[0];
            $responses = (new RecruitingToken())->getUserTokens((int) $user_id);
            ?>
            <tr>
              <td>OS X</td>
              <td><?=$row['osx']?></td>
              <td><?=round(100*$row['osx']/$row['total'],2)?>%</td>
            </tr>
            <tr>
              <td>Windows</td>
              <td><?=$row['windows']?></td>
              <td><?=round(100*$row['windows']/$row['total'],2)?>%</td>
            </tr>
            <tr>
              <td>iPhone</td>
              <td><?=$row['iphone']?></td>
              <td><?=round(100*$row['iphone']/$row['total'],2)?>%</td>
            </tr>
            <tr>
              <td>Android</td>
              <td><?=$row['android']?></td>
              <td><?=round(100*$row['android']/$row['total'],2)?>%</td>
            </tr>
            <tr>
              <td>iPad</td>
              <td><?=$row['ipad']?></td>
              <td><?=round(100*$row['ipad']/$row['total'],2)?>%</td>
            </tr>
            <tr>
              <td>Bot</td>
              <td><?=$row['bot']?></td>
              <td><?=round(100*$row['bot']/$row['total'],2)?>%</td>
            </tr>
        </tbody>
      </table>

      <h3>by web browser</h3>
      <table id="responsive-table2" class="table table-striped table-hover">
        <thead>
          <th>Browser</th>
          <th>Count</th>
          <th>Percent</th>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT SUM(IF(user_agent LIKE '%Chrome%' AND user_agent NOT LIKE '%Edge%',1, 0)) as chrome,
                    SUM(IF(user_agent LIKE '%Firefox%',1, 0)) as firefox,
                    SUM(IF(user_agent LIKE '%Trident%',1, 0)) as ie,
                    SUM(IF(user_agent LIKE '%AppleWebKit%' AND user_agent NOT LIKE '%Chrome%' ,1, 0)) as safari,
                    SUM(IF(user_agent LIKE '%Edge%',1, 0)) as edge,
                    SUM(IF(user_agent LIKE '%bot%',1, 0)) as bot,
                    COUNT(*) total
                    FROM giftbox.web_request
                    WHERE uri LIKE '/token/recruiting%'
                    AND user_id IS NULL";
            $row = (execute_query($sql))->fetch_all(MYSQLI_ASSOC)[0];
            $responses = (new RecruitingToken())->getUserTokens((int) $user_id);
            ?>
            <tr>
              <td>Chrome</td>
              <td><?=$row['chrome']?></td>
              <td><?=round(100*$row['chrome']/$row['total'],2)?>%</td>
            </tr>
            <tr>
              <td>Firefox</td>
              <td><?=$row['firefox']?></td>
              <td><?=round(100*$row['firefox']/$row['total'],2)?>%</td>
            </tr>
            <tr>
              <td>Internet Explorer</td>
              <td><?=$row['ie']?></td>
              <td><?=round(100*$row['ie']/$row['total'],2)?>%</td>
            </tr>
            <tr>
              <td>Safari</td>
              <td><?=$row['safari']?></td>
              <td><?=round(100*$row['safari']/$row['total'],2)?>%</td>
            </tr>
            <tr>
              <td>Edge</td>
              <td><?=$row['edge']?></td>
              <td><?=round(100*$row['edge']/$row['total'],2)?>%</td>
            </tr>
            <tr>
              <td>Bot</td>
              <td><?=$row['bot']?></td>
              <td><?=round(100*$row['bot']/$row['total'],2)?>%</td>
            </tr>
        </tbody>
      </table>
    </div>
  </div>
    <?php require __DIR__.'/footer.php';?>
  <script type="text/javascript" src="https://cdn.datatables.net/s/dt/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.10,b-1.1.0,b-flash-1.1.0,b-html5-1.1.0,b-print-1.1.0/datatables.min.js"></script>
  <script>
  $(document).ready(function() {
      var table = $('#responsive-table').DataTable({
          dom: 'B<"clear">lfrtip',
          buttons: [
              'copy', 'csv', 'excel', 'pdf','print'
          ]
      });
      var table = $('#responsive-table2').DataTable({
          dom: 'B<"clear">lfrtip',
          buttons: [
              'copy', 'csv', 'excel', 'pdf','print'
          ]
      });
  });
  </script>
</body>
</html>
