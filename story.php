<?php
date_default_timezone_set('America/Chicago');

if (!logged_in()) {
  login_then_redirect_back_here();
}
$user_id = $_SESSION['user_id'] ?? '';

// get cookie
$visitor_cookie = $_GET['id'] ?? '';

$invalidCookie = true;
if ('' != $visitor_cookie) {
    $cookies = execute_query(
        "SELECT visitor_cookie
        FROM recruiting_token_click"
    )->fetch_all(MYSQLI_ASSOC);
    foreach ($cookies as $cookie) {
        if ($cookie['visitor_cookie'] == $visitor_cookie) {
            $invalidCookie = false;
        }
    }
}

if (!$invalidCookie) {
    // Get story list to choose from
    $stories = execute_query(
        "SELECT CONCAT('Candidate clicked on ',COALESCE(story_text, CONCAT('<code>', REPLACE(REPLACE(html_tag, '<', '&lt;'), '>', '&gt;'), '</code>')), '.') story_line,
        recruiting_token_click.created
        FROM recruiting_token_click
        LEFT JOIN html_tag ON html_tag.html_tag_id = recruiting_token_click.html_tag_id
        WHERE visitor_cookie = '{$visitor_cookie}'
        ORDER BY visitor_cookie, recruiting_token_click.created"
    )->fetch_all(MYSQLI_ASSOC);
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
  <?php if (!$invalidCookie) { ?>
      <div class="row" id="datatable-div">
        <div class="col-sm-offset-2 col-sm-8">
          <h2>Candidate User Story</h2>
          <table id="responsive-table" class="table table-striped table-hover">
            <thead>
              <th>Time</th>
              <th>Event</th>
            </thead>
            <tbody>
                <?php
                foreach ($stories as $story) {
                    echo '<tr>';
                    echo "<td>".date('m/d/Y g:i a', strtotime($story['created']))."</td>";
                    echo "<td align=left>";
                    echo $story['story_line'];
                    echo "</td>";
                    echo '</tr>';
                }?>
            </tbody>
          </table>
        </div>
      </div>
  <?php
  } else {
      echo 'Invalid <a href="https://youtu.be/gsusakRf7T8?t=1m19s" target="sid">Story</a>';
  }
  require __DIR__.'/footer.php';
  ?>
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
