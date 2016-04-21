<?php
use \Sizzle\Bacon\Database\RecruitingTokenResponse;

if (!logged_in()) {
    header('Location: '.'/');
}

date_default_timezone_set('America/Chicago');

define('TITLE', 'S!zzle - All Organizations');
require __DIR__.'/../header.php';
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/s/dt/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.10,b-1.1.0,b-flash-1.1.0,b-html5-1.1.0,b-print-1.1.0/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="/css/datatables.min.css"/>
<style>
body {
  background-color: white;
}
#orgs-table {
  margin-top: 100px;
  color: black;
  font-size: 13px;
  margin-bottom: 50px;
}
</style>
</head>
<body id="users">
  <div>
    <?php require __DIR__.'/../navbar.php';?>
  </div>
  <div class="row">
    <div class="col-sm-offset-1 col-sm-10" id="orgs-table">
      <h2>ORGANIZATIONS</h2>
      <table class="table table-striped table-hover" id="responsive-table" hidden>
        <thead>
          <th>Created</th>
          <th>Name</th>
          <th>Users</th>
          <th>Tokens</th>
          <th>Views</th>
          <th>Paying User</th>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT organization.id, `name`,
                    COALESCE(user.users, 0) AS users,
                    COALESCE(recruiting_token.tokens, 0) AS tokens,
                    COALESCE(view.views, 0) AS views,
                    COALESCE(paying_user, 'none') AS paying_user,
                    organization.created
                    FROM organization
                    LEFT JOIN
                    (SELECT user.organization_id, COUNT(*) as users
                     FROM user
                     GROUP BY user.organization_id
                    ) as user
                    ON user.organization_id = organization.id
                    LEFT JOIN
                    (SELECT user.organization_id, COUNT(*) as tokens
                     FROM recruiting_token, user
                     WHERE recruiting_token.user_id = user.id
                     GROUP BY user.organization_id
                    ) AS recruiting_token
                    ON organization.id = recruiting_token.organization_id
                    LEFT JOIN
                    (SELECT user.organization_id, COUNT(*) as views
                     FROM web_request, recruiting_token, user
                     WHERE recruiting_token.user_id = user.id
                     AND web_request.user_id IS NULL
                     AND web_request.uri LIKE CONCAT('/token/recruiting/', recruiting_token.long_id)
                     GROUP BY user.organization_id
                    ) as view
                    ON organization.id = view.organization_id
                    GROUP BY organization.id
                    ORDER BY organization.id
                    ";
            $results = execute_query($sql);
            $rows = array();
            while ($row = $results->fetch_assoc()) { ?>
                <tr>
                  <td><?php echo date('m/d/Y g:i a', strtotime($row['created']));?></td>
                  <td><a href="/organization/<?php echo $row['id'];?>"><?php echo $row['name'];?></a></td>
                  <td><?php echo $row['users'];?></td>
                  <td><?php echo $row['tokens'];?></td>
                  <td><?php echo $row['views'];?></td>
                  <td>
                    <?php if ('none' != $row['paying_user']) {
                        echo "<a href=\"/user/{$row['paying_user']}\">{$row['paying_user']}</a>";
                    } else {
                        echo 'none';
                    }?>
                  </td>
                </tr>
            <?php }?>
        </tbody>
      </table>
    </div>
  </div>
    <?php require __DIR__.'/../footer.php';?>
  <script type="text/javascript" src="https://cdn.datatables.net/s/dt/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.10,b-1.1.0,b-flash-1.1.0,b-html5-1.1.0,b-print-1.1.0/datatables.min.js"></script>
  <script>
  $(document).ready(function() {
      var table = $('#responsive-table').DataTable({
          dom: 'B<"clear">lfrtip',
          buttons: [
              'copy', 'csv', 'excel', 'pdf','print'
          ]
      });
      $('#responsive-table').show();
  });
  </script>
</body>
</html>
