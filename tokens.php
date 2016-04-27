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
      <h2>Tokens</h2>
      <table id="responsive-table" class="table table-striped table-hover">
        <thead>
          <th>Token</th>
          <th>Created</th>
          <th>Repsonses</th>
        </thead>
        <tbody>
            <?php
            $responses = (new RecruitingToken())->getUserTokens((int) $user_id);
            foreach ($responses as $response) {
                echo '<tr>';
                echo "<td align=left>";
                echo "<a href=\"/create_recruiting?id={$response->long_id}\">{$response->job_title}</a>";
                echo " (<a href=\"/token/recruiting/{$response->long_id}\">Preview</a>)";
                echo "</td>";
                echo "<td>".date('m/d/Y g:i a', strtotime($response->created))."</td>";
                echo "<td><a href=\"/token_responses?id={$response->long_id}\">responses</a></td>";
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
