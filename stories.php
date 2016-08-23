<?php
date_default_timezone_set('America/Chicago');

if (!logged_in()) {
  login_then_redirect_back_here();
}
$user_id = $_SESSION['user_id'] ?? '';

// Get story list to choose from
$stories = execute_query(
    "SELECT visitor_cookie, MIN(created) started
    FROM recruiting_token_click
    WHERE visitor_cookie != ''
    AND MONTH(created) = MONTH(NOW())
    GROUP BY visitor_cookie
    ORDER BY started DESC"
)->fetch_all(MYSQLI_ASSOC);


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
      <h2>Recent Candidate User Stories</h2>
      <table id="responsive-table" class="table table-striped table-hover">
        <thead>
          <th>Story Created</th>
        </thead>
        <tbody>
            <?php
            foreach ($stories as $story) {
                echo '<tr><td>';
                echo "<a href=\"/story?id={$story['visitor_cookie']}\">";
                echo date('m/d/Y g:i a', strtotime($story['started']));
                echo '</a>';
                echo '</td></tr>';
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
