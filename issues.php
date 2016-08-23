<?php
date_default_timezone_set('America/Chicago');

if (!logged_in()) {
  login_then_redirect_back_here();
}
$user_id = $_SESSION['user_id'] ?? '';

//echo '<pre>';
try {
    $client = new \Github\Client();
    $client->authenticate(GITHUB_TOKEN, \Github\Client::AUTH_HTTP_TOKEN);
    $repositories = $client->api('organization')->repositories('GiveToken');
    $issues = [];
    foreach ($repositories as $repo) {
        echo $repo['full_name']."\n";
        $issues = array_merge($issues, $client->api('issue')->all('GiveToken', $repo['name']));
    }
    //print_r($issues);die;
} catch (Exception $e) {
  echo '<pre>';print_r($e);
  die;
}

define('TITLE', 'S!zzle - Tokens');
require __DIR__.'/header.php';
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/s/dt/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.10,b-1.1.0,b-flash-1.1.0,b-html5-1.1.0,b-print-1.1.0/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="/css/datatables.min.css"/>

</head>
<body id="issues-listing">
  <div>
    <?php require __DIR__.'/navbar.php';?>
  </div>
  <div class="row" id="datatable-div">
    <div class="col-sm-offset-2 col-sm-8">
      <h2>Issue Requests</h2>
      <table id="responsive-table" class="table table-striped table-hover">
        <thead>
          <th>Requests</th>
          <th>Repository</th>
          <th>Issue</th>
          <th>Created</th>
        </thead>
        <tbody>
            <?php
            foreach ($issues as $issue) {
                echo '<tr>';
                echo "<td>";
                echo "0";
                echo "</td>";
                echo "<td align=left>";
                $url = explode('/',$issue['repository_url']);
                echo array_pop($url);
                echo "</td>";
                echo "<td align=left>";
                echo "<a href=\"{$issue['html_url']}\">{$issue['title']}</a>";
                echo "</td>";
                echo "<td>".date('m/d/Y g:i a', strtotime($issue['created_at']))."</td>";
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
