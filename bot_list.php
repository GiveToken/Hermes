<?php
use \Sizzle\Bacon\Database\BotUserAgent;

date_default_timezone_set('America/Chicago');

if (!logged_in()) {
  login_then_redirect_back_here();
}

define('TITLE', 'S!zzle - Bots');
require __DIR__.'/header.php';
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/s/dt/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.10,b-1.1.0,b-flash-1.1.0,b-html5-1.1.0,b-print-1.1.0/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="/css/datatables.min.css"/>

</head>
<body id="bot-listing">
  <div>
    <?php require __DIR__.'/navbar.php';?>
  </div>
  <div class="row" id="datatable-div">
    <div class="col-sm-offset-2 col-sm-8">
      <h2>Bots (User Agents)</h2>
      <table id="responsive-table" class="table table-striped table-hover">
        <thead>
          <th>Hits</th>
          <th>Bot User Agent</th>
        </thead>
        <tbody>
            <?php
            $bots = (new BotUserAgent())->getAllHits();
            foreach ($bots as $bot) {
                echo '<tr>';
                echo "<td>{$bot['hits']}</td>";
                echo "<td align=left>";
                echo "{$bot['user_agent']}";
                echo "</td>";
                echo '</tr>';
            }?>
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
  });
  </script>
</body>
</html>
