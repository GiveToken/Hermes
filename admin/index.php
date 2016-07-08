<?php
if (!logged_in()) {
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
          <!-- <i><a href="/queue?count=100">Incoming Job Queue</a></i>-->
        </li>
        <br/>
        <li>
          Cities:
          <a href="/admin/city">Edit</a>
        </li>
        <li>
          Github:
          <a href="https://www.github.com/GiveToken/Bacon">Bacon</a> •
          <a href="https://www.github.com/GiveToken/Giftbox">Giftbox</a> •
          <a href="https://www.github.com/GiveToken/Hermes">Hermes</a>
        </li>
        <li>
          Experiments:
          <a href="/experiments">All</a>
        </li>
        <li>
          Organizations:
          <a href="/admin/organizations">All</a> •
          <a href="/report/org_growth">Active Growth</a> •
          <a href="/report/inactive_organizations">Inactive</a>
        </li>
        <li>
          Reports:
          <a href="/report/response_rate">Response Rate</a> •
          <a href="/report/usage_growth">Usage Growth</a>
        </li>
        <li>
          Tokens:
          <a href="/admin/tokens">Listing</a> •
          <a href="<?=APP_URL?>create_recruiting">Create</a> •
          <a href="/admin/send_token">Send</a> •
          <a href="/admin/transfer_token">Transfer Ownership</a> •
          <a href="/report/token_breakdown">Stats</a>
        </li>
        <li>
          Users:
          <a href="/admin/users">All</a> •
          <a href="/report/user_growth">Active Growth</a> •
          <a href="/admin/create_account">Create Account</a> •
          <a href="/admin/stalled_new_customers">Stalled</a> •
          <a href="/admin/no_card_customers">Free Trial</a> •
          <a href="/admin/active_users">Recently Active</a>
        </li>
        <li>
          <a href="/admin/visitors">Website Vistors</a> •
          <a href="/bot_list">Bots</a>
        </li>
      </ul>
    </nav>
    <img src="/images/sizzle_mascot_cant_wait_to_sizzle.jpg" />
  </div>
  <div>

  </div>
    <?php require __DIR__.'/../footer.php';?>
</body>
</html>
