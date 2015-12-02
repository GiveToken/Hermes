<?php
require_once 'config.php';
if (!logged_in() || !is_admin()) {
    header('Location: '.$app_root);
}

define('TITLE', 'GiveToken.com - Admin');
require __DIR__.'/header.php';
?>
</head>
<body>
  <div id="content-wrapper" style="margin-bottom: 300px;">
    <?php require __DIR__.'/navbar.php';?>
    <h1 style="margin-top: 100px;">
      <a id="create-home-icon" title="Return to the Homepage" href="<?php echo $app_root ?>">GiveToken</a>
    </h1>
    <nav id="create-top-nav">
      <ul>
        <li>
          <a href="manage_users.php">Manage Users</a>
        </li>
        <li>
          <a href="manage_groups.php">Manage Groups</a>
        </li>
        <li>
          <a href="/admin/visitors">Website Vistors</a>
        </li>
        <li>
          <a href="/admin/add_city">Add Recruiting Token City</a>
        </li>
      </ul>
    </nav>
  </div>
  <?php require __DIR__.'/footer.php';?>
</body>
</html>
