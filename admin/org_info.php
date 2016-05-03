<?php
use \Sizzle\Bacon\Database\{
    Organization,
    User
};

if (!logged_in()) {
    header('Location: '.'/');
}

date_default_timezone_set('America/Chicago');

// collect id
$endpoint_parts = explode('/', $_SERVER['REQUEST_URI']);
if (isset($endpoint_parts[2]) && (int) $endpoint_parts[2] > 0) {
    $org_id = (int) $endpoint_parts[2];
    $org = new Organization($org_id);
} else {
    $org_id = 0;
}

define('TITLE', 'S!zzle - Organization Info');
require __DIR__.'/../header.php';
?>
<style>
body {
  background-color: white;
}
#org-info {
  margin-top: 100px;
  color: black;
  text-align: left;
}
.greyed {
  background-color: lightgrey;
  font-style: normal;
}
</style>
</head>
<body id="visitors">
  <div>
    <?php require __DIR__.'/../navbar.php';?>
  </div>
  <div class="row" id="org-info">
    <div class="col-sm-offset-1 col-sm-10">
        <?php if (0 !== $org_id) { ?>
        <a href="/admin/edit_organization?id=<?= $org->id ?>">
          <button class="btn pull-right" id="edit-org-button">Edit</button>
        </a>
        <h3><i class="greyed"><?php echo $org->name;?></i></h3>
        <br />
        Created <?php echo date('m/d/Y g:i a', strtotime($org->created));?>
        <br />
        <?php
        if (isset($org->paying_user) && '' !== $org->paying_user) {
            echo "<a href=\"/user/{$org->paying_user}\">Paying User</a>";
        } else {
            echo 'No paying user';
        }
        ?>
        <br />
        <h4><i class="greyed">Activity:</i></h4>
        <?php
        $query = "SELECT count(*) users
                  FROM user
                  WHERE organization_id = '$org_id'";
        $result = execute_query($query);
        if ($row = $result->fetch_assoc()) {
            echo $row['users'] . ' users created<br />';
        }
        $query = "SELECT count(*) tokens
                  FROM recruiting_token, user
                  WHERE recruiting_token.user_id = user.id
                  AND organization_id = '$org_id'";
        $result = execute_query($query);
        if ($row = $result->fetch_assoc()) {
            echo $row['tokens'] . ' tokens created<br />';
        }
        $query = "SELECT count(*) token_views
                  FROM recruiting_token, web_request, user
                  WHERE recruiting_token.user_id = user.id
                  AND organization_id = '$org_id'
                  AND web_request.user_id IS NULL
                  AND web_request.uri LIKE CONCAT('%/token/recruiting/',recruiting_token.long_id,'%')";
        $result = execute_query($query);
        if ($row = $result->fetch_assoc()) {
            echo $row['token_views'] . ' token views';
        }
        ?>
        <br />
        <h4><i class="greyed">Users:</i></h4>
        <?php
        $sql = "SELECT email_address, id
                FROM user
                WHERE organization_id = '$org_id'";
        $result = execute_query($sql);
        while ($row = $result->fetch_assoc()) {
            echo "<a href=\"/user/{$row['id']}\">{$row['email_address']}</a><br />";
        }
        ?>
        <br />
        <?php } else { ?>
        <h2>Invalid user</h2>
        <?php }?>
    </div>
  </div>
    <?php require __DIR__.'/../footer.php';?>
</body>
</html>
