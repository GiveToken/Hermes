<?php
use \Sizzle\Bacon\Database\UserMilestone;

if (!logged_in()) {
    header('Location: '.'/');
}

define('TITLE', 'S!zzle - Stalled Customers');
require __DIR__.'/../header.php';
?>
<style>
body {
  background-color: white;
}
#customers {
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
    <div class="col-sm-offset-3 col-sm-6" id="customers">
      <h2>Stalled New Customers</h2>
      <table class="table table-striped table-hover">
        <thead>
          <th>Customer</th>
          <th>Last Activity</th>
          <th>Milestones Achieved</th>
        </thead>
        <tbody>
            <?php
            $customers = (new UserMilestone())->stalledCustomers();
            foreach ($customers as $customer) { ?>
                <tr>
                  <td align="left">
                    <?php
                    echo "<a href=\"/user/{$customer['id']}\">{$customer['first_name']} {$customer['last_name']} ({$customer['email_address']})</a>";
                    ?>
                  </td>
                  <td><?php echo $customer['last_active'];?></td>
                  <td><?php echo $customer['milestones'];?></td>
                </tr>
            <?php }?>
        </tbody>
      </table>
    </div>
  </div>
    <?php require __DIR__.'/../footer.php';?>
</body>
</html>
