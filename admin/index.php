<?php
if (!logged_in() || !is_admin()) {
    header('Location: '.'/');
}

define('TITLE', 'S!zzle - Admin');
require __DIR__.'/../header.php';
?>
</head>
<body>
  <div id="content-wrapper" style="margin-bottom: 300px; text-align:left; margin-left:50px;">
    <?php require __DIR__.'/../navbar.php';?>
    <h1 style="margin-top: 100px;">
      Admin Portal
    </h1>
    <nav id="create-top-nav">
      <ul>
        <li>
          <a href="/admin/visitors">Website Vistors</a>
        </li>
        <li>
          Organizations:
          <a href="/admin/organizations">All</a>
        </li>
        <li>
          Users:
          <a href="/admin/users">All</a> •
          <a href="/admin/stalled_new_customers">Stalled</a> •
          <a href="/admin/no_card_customers">Free Trial</a> •
          <a href="/admin/active_users">Recently Active</a>
        </li>
        <li>
          <a href="/admin/create_account">Create Account</a>
        </li>
        <li>
          Tokens:
          <a href="/admin/tokens">All</a> •
          <a href="/admin/send_token">Send</a> •
          <a href="/admin/transfer_token">Transfer Ownership</a>
        </li>
        <li>
          Cities:
          <a href="/admin/add_city">Add</a>
        </li>
      </ul>
    </nav>
  </div>
    <?php require __DIR__.'/../footer.php';?>
</body>
</html>