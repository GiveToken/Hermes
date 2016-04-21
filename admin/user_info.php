<?php
use \Sizzle\Bacon\Database\User;

if (!logged_in()) {
    header('Location: '.'/');
}

date_default_timezone_set('America/Chicago');

// collect id
$endpoint_parts = explode('/', $_SERVER['REQUEST_URI']);
if (isset($endpoint_parts[2]) && (int) $endpoint_parts[2] > 0) {
    $user_id = (int) $endpoint_parts[2];
    $User = new User($user_id);
    if (isset($User->first_name) || isset($User->last_name)) {
        $user_name = $User->first_name . ' ' . $User->last_name;
        $email = $User->email_address;
    } else if (isset($User->email_address)) {
        $user_name = $User->email_address;
        $email = '';
    } else {
        $user_id = 0;
    }
} else {
    $user_id = 0;
}

define('TITLE', 'S!zzle - UserInfo');
require __DIR__.'/../header.php';
?>
<style>
body {
  background-color: white;
}
#add-box {
  margin-top: 100px;
  margin-right:100px;
}
#user-info {
  color: black;
  text-align: left;
}
.greyed {
  background-color: lightgrey;
  font-style: normal;
}
.btn {
  color: black;
}
</style>
</head>
<body id="user">
  <div>
    <?php require __DIR__.'/../navbar.php';?>
  </div>
    <?php if (0 !== $user_id) { ?>
  <div class="row">
    <div class="pull-right" id="add-box">
        <?php if (isset($User->activation_key) && '' != $User->activation_key) { ?>
      <button class="btn button-success" id="activation-button">Resend Activation Email</button>
        <?php }?>
      <a href="/admin/recruiter_profile?user_id=<?= $user_id ?>">
        <button class="btn button-success">Update Recruiter Profile</button>
      </a>
    </div>
  </div>
    <?php }?>
  <div class="row" id="user-info">
    <div class="col-sm-offset-1 col-sm-10">
        <?php if (0 !== $user_id) { ?>
      <h3><i class="greyed"><?php echo $user_name;?></i></h3>
        <?php
        echo $email;
        if ('Y' == $User->admin) { echo ' <b>ADMIN</b>'; 
        }
        ?>
      <br />
        <?php
        if (isset($User->organization_id) && 0 < (int) $User->organization_id) {
            echo "<a href=\"/organization/{$User->organization_id}\">Organization</a>";
        }
        ?>
      <br />
      Created <?php echo date('m/d/Y g:i a', strtotime($User->created));?>
      <br />
        <?php
        if (isset($User->stripe_id) && '' !== $User->stripe_id) {
            echo 'Credit card info on file.';
        } else {
            echo 'No credit card info on file.';
        }
        ?>
      <br />
      <h4><i class="greyed">Milestones Completed:</i></h4>
        <?php
        $query = "SELECT GROUP_CONCAT(milestone.`name`) milestones
                FROM user_milestone
                JOIN milestone ON milestone.id = milestone_id
                WHERE user_id = $user_id
                ORDER BY milestone.id";
        $result = execute_query($query);
        if ($row = $result->fetch_assoc()) {
            echo $row['milestones'];
        }
        ?>
      <br />
      <h4><i class="greyed">Tokens:</i></h4>
        <?php
        $query = "SELECT count(*) tokens
                FROM recruiting_token
                WHERE user_id = $user_id";
        $result = execute_query($query);
        if ($row = $result->fetch_assoc()) {
            echo $row['tokens'] . ' tokens created<br />';
        }
        $query = "SELECT count(*) token_views
                FROM recruiting_token, web_request
                WHERE recruiting_token.user_id = $user_id
                AND web_request.user_id IS NULL
                AND web_request.uri LIKE CONCAT('%/token/recruiting/',recruiting_token.long_id,'%')";
        $result = execute_query($query);
        if ($row = $result->fetch_assoc()) {
            echo $row['token_views'] . ' token views';
        }
        ?>
      <br />
      <h4><i class="greyed">Activity:</i></h4>
        <?php
        $query = "SELECT created
                FROM web_request
                WHERE user_id = $user_id
                ORDER BY id DESC
                LIMIT 1";
        $result = execute_query($query);
        if ($row = $result->fetch_assoc()) {
            echo 'Last active ' . $row['created'] . '<br />';
        }
        ?>
      <table class="table table-striped table-hover">
        <thead>
          <th>Recent Requested Endpoint</th>
          <th>IP Address</th>
          <th>User Agent</th>
          <th>Date</th>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT created, user_agent, remote_ip, uri
                      FROM web_request
                      WHERE user_id = $user_id
                      ORDER BY id DESC
                      LIMIT 10";
            $results = execute_query($sql);
            $rows = array();
            while ($row = $results->fetch_assoc()) { ?>
                <tr>
                  <td><?php echo $row['uri'];?></td>
                  <td><?php echo $row['remote_ip'];?></td>
                  <td><?php echo $row['user_agent'];?></td>
                  <td><?php echo $row['created'];?></td>
                </tr>
            <?php }?>
        </tbody>
      </table>
        <?php } else { ?>
        <h2>Invalid user</h2>
        <?php }?>
    </div>
  </div>
    <?php require __DIR__.'/../footer.php';?>
  <script>
  $(document).ready(function(){
    $('#activation-button').on('click', function (event) {
      event.preventDefault();
      $.post(
        '/ajax/resend_activation',
        {'id':'<?= $user_id ?>'},
        function() {
          $('#activation-button').remove();
        }
      );
    });
  });
  </script>
</body>
</html>
