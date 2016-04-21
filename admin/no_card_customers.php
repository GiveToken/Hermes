<?php
use \Sizzle\Bacon\Database\RecruitingTokenResponse;

if (!logged_in()) {
    header('Location: '.'/');
}

date_default_timezone_set('America/Chicago');

define('TITLE', 'S!zzle - All Users');
require __DIR__.'/../header.php';
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/s/dt/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.10,b-1.1.0,b-flash-1.1.0,b-html5-1.1.0,b-print-1.1.0/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="/css/datatables.min.css"/>
<style>
body {
  background-color: white;
}
#users-table {
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
    <div class="col-sm-offset-1 col-sm-10" id="users-table">
      <h2>USERS IN FREE TRIAL</h2>
      <i>We started tracking user creation 12/7, so the oldest clump is on that date.</i>
      <table class="table table-striped table-hover" id="responsive-table" hidden>
        <thead>
          <th>Days Into Trial</th>
          <th>Name</th>
          <th>Email</th>
          <th>Organization</th>
          <th>Tokens</th>
          <th>Views</th>
          <th>Account Created</th>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT user.id, organization_id,
                    CONCAT(user.first_name, ' ', user.last_name) as full_name,
                    user.email_address,
                    COALESCE(organization.`name`, '-') AS organization,
                    COALESCE(recruiting_token.tokens, 0) AS tokens,
                    COALESCE(view.views, 0) AS views,
                    user.created,
                    DATEDIFF(CURDATE(), user.created) AS free_days
                    FROM user
                    LEFT JOIN
                    (SELECT user_id, COUNT(*) as tokens
                     FROM recruiting_token
                     GROUP BY recruiting_token.user_id
                    ) AS recruiting_token
                    ON user.id = recruiting_token.user_id
                    LEFT JOIN
                    (SELECT recruiting_token.user_id, COUNT(*) as views
                     FROM web_request, recruiting_token
                     WHERE web_request.user_id IS NULL
                     AND web_request.uri LIKE CONCAT('/token/recruiting/', recruiting_token.long_id)
                     GROUP BY recruiting_token.user_id
                    ) as view
                    ON user.id = view.user_id
                    LEFT JOIN organization
                    ON user.organization_id = organization.id
                    WHERE user.stripe_id is NULL
                    -- AND user.ignore_onboard_status = 'N'
                    AND user.internal = 'N'
                    AND (user.organization_id IS NULL
                      OR user.organization_id NOT IN (
                        SELECT id FROM organization
                        WHERE paying_user > 0
                      )
                    )
                    GROUP BY user.id
                    ORDER BY free_days, created DESC
                    ";
            $results = execute_query($sql);
            $rows = array();
            while ($row = $results->fetch_assoc()) { ?>
                <tr <?php if ($row['free_days'] > 30) echo 'style="color:#ff4c4c;"';?>>
                  <td><?php echo $row['free_days'];?></td>
                  <td><a href="/user/<?php echo $row['id'];?>" <?php if ($row['free_days'] > 30) echo 'style="color:#ff4c4c;"';?>><?php echo $row['full_name'];?></a></td>
                  <td><a href="/user/<?php echo $row['id'];?>" <?php if ($row['free_days'] > 30) echo 'style="color:#ff4c4c;"';?>><?php echo $row['email_address'];?></td>
                  <td>
                    <?php if (0 < (int) $row['organization_id']) {
                      echo "<a href=\"/organization/{$row['organization_id']}\"";
                      echo $row['free_days'] > 30 ? 'style="color:#ff4c4c;"' : '';
                      echo ">{$row['organization']}</a>";
                    }?>
                  </td>
                  <td><?php echo $row['tokens'];?></td>
                  <td><?php echo $row['views'];?></td>
                  <td><?php echo date('m/d/Y g:i a', strtotime($row['created']));?></td>
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
