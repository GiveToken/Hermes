<?php
use \Sizzle\Bacon\Database\RecruitingTokenResponse;

if (!logged_in()) {
    header('Location: '.'/');
}

define('TITLE', 'S!zzle - Active Users');
require __DIR__.'/../header.php';
?>
<style>
body {
  background-color: white;
}
#top-ten-table {
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
    <div class="col-sm-offset-3 col-sm-6" id="top-ten-table">
      <h2>Active Users</h2>
      <table class="table table-striped table-hover">
        <thead>
          <th>User</th>
          <th>Last Access</th>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT user.id, user.email_address, user.first_name, user.last_name, MAX(web_request.created) AS last_access
                  FROM user, web_request
                  WHERE user.id = web_request.user_id
                  GROUP BY user.id, user.email_address
                  ORDER BY last_access DESC";
            $results = execute_query($sql);
            $rows = array();
            while ($row = $results->fetch_assoc()) { ?>
                <tr>
                  <td>
                    <?php
                    echo "<a href=\"/user/{$row['id']}\">{$row['first_name']} {$row['last_name']}</a>";
                    echo " (<a href=\"mailto:{$row['email_address']}\">{$row['email_address']}</a>)";
                    ?>
                  </td>
                  <td><?php echo $row['last_access'];?></td>
                </tr>
            <?php }?>
        </tbody>
      </table>
    </div>
  </div>
    <?php require __DIR__.'/../footer.php';?>
</body>
</html>
