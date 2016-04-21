<?php
use \Sizzle\Bacon\Database\RecruitingTokenResponse;

if (!logged_in()) {
    header('Location: '.'/');
}

define('TITLE', 'S!zzle - Visitors');
require __DIR__.'/../header.php';
?>
<style>
body {
  background-color: white;
}
#visitors-table {
  margin-top: 100px;
  color: black;
}
</style>
</head>
<body id="visitors">
  <div>
    <?php require __DIR__.'/../navbar.php';?>
  </div>
  <div class="row">
    <div class="col-sm-offset-3 col-sm-6">
      <table id="visitors-table" class="table table-striped table-hover">
        <thead>
          <th>Type</th>
          <th>Today</th>
          <th>This Week</th>
          <th>This Month</th>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT 'users' type_visitors,
                  (SELECT COUNT(distinct user_id)
                  FROM web_request
                  WHERE created > CAST(NOW() AS DATE)) today_visitors,
                  (SELECT COUNT(distinct user_id)
                  FROM web_request
                  WHERE created > (CAST(NOW() AS DATE) - INTERVAL 7 DAY)) week_visitors,
                  (SELECT COUNT(distinct user_id)
                  FROM web_request
                  WHERE created > (CAST(NOW() AS DATE) - INTERVAL 30 DAY)) month_visitors
                  UNION
                  SELECT 'total',
                  (SELECT COUNT(distinct visitor_cookie)
                  FROM web_request
                  WHERE created > CAST(NOW() AS DATE)),
                  (SELECT COUNT(distinct visitor_cookie)
                  FROM web_request
                  WHERE created > (CAST(NOW() AS DATE) - INTERVAL 7 DAY)),
                  (SELECT COUNT(distinct visitor_cookie)
                  FROM web_request
                  WHERE created > (CAST(NOW() AS DATE) - INTERVAL 30 DAY))
                  UNION
                  SELECT 'returning',
                  (SELECT COUNT(distinct visitor_cookie)
                  FROM web_request
                  WHERE created > CAST(NOW() AS DATE)
                  AND visitor_cookie in
                  (SELECT visitor_cookie
                  FROM web_request
                  WHERE created < CAST(NOW() AS DATE))),
                  (SELECT COUNT(distinct visitor_cookie)
                  FROM web_request
                  WHERE created > (CAST(NOW() AS DATE) - INTERVAL 7 DAY)
                  AND visitor_cookie in
                  (SELECT visitor_cookie
                  FROM web_request
                  WHERE created < (CAST(NOW() AS DATE) - INTERVAL 7 DAY))),
                  (SELECT COUNT(distinct visitor_cookie)
                  FROM web_request
                  WHERE created > (CAST(NOW() AS DATE) - INTERVAL 30 DAY)
                  AND visitor_cookie in
                  (SELECT visitor_cookie
                  FROM web_request
                  WHERE created < (CAST(NOW() AS DATE) - INTERVAL 30 DAY)))";
            $results = execute_query($sql);
            $rows = array();
            while ($row = $results->fetch_assoc()) {
                $rows[$row['type_visitors']] = $row;
            }
            echo '<tr>';
            echo "<td>{$rows['total']['type_visitors']}</td>";
            echo "<td>{$rows['total']['today_visitors']}</td>";
            echo "<td>{$rows['total']['week_visitors']}</td>";
            echo "<td>{$rows['total']['month_visitors']}</td>";
            echo '</tr>';
            echo '<tr>';
            echo "<td>{$rows['users']['type_visitors']}</td>";
            echo "<td>{$rows['users']['today_visitors']}</td>";
            echo "<td>{$rows['users']['week_visitors']}</td>";
            echo "<td>{$rows['users']['month_visitors']}</td>";
            echo '</tr>';
            echo '<tr>';
            echo "<td>{$rows['returning']['type_visitors']}</td>";
            echo "<td>{$rows['returning']['today_visitors']}</td>";
            echo "<td>{$rows['returning']['week_visitors']}</td>";
            echo "<td>{$rows['returning']['month_visitors']}</td>";
            echo '</tr>';
            echo '<tr>';
            echo "<td>new</td>";
            echo '<td>'.($rows['total']['today_visitors']-$rows['returning']['today_visitors']).'</td>';
            echo '<td>'.($rows['total']['week_visitors']-$rows['returning']['week_visitors']).'</td>';
            echo '<td>'.($rows['total']['month_visitors']-$rows['returning']['month_visitors']).'</td>';
            echo '</tr>';
            ?>
        </tbody>
      </table>
    </div>
  </div>
    <?php require __DIR__.'/../footer.php';?>
</body>
</html>
