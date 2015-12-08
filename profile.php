<?php
require_once 'config.php';

$message = null;
$first_name = null;
$last_name = null;
$email = null;
$user_id = null;

if (!logged_in()) {
    header('Location: '.$app_root);
}
define('TITLE', 'GiveToken.com - Profile');
require __DIR__.'/header.php';
?>

<!-- REACT -->
<script src="/js/react.js"></script>
<script src="/js/JSXTransformer.js"></script>

</head>

<body id="profile-page">

<!-- =========================
     HEADER
============================== -->
<header class="header" data-stellar-background-ratio="0.5">
  <div>
    <?php require __DIR__.'/navbar.php';?>
  </div>
  <div id="account-profile">
  </div>
</header>
<!-- /END HEADER -->

<!-- =========================
     ACCOUNT PROFILE
============================== -->

<script type="text/javascript" src="https://crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/md5.js"></script>
<script type="text/javascript" src="/app/models/model.js?v=<?php echo VERSION;?>"></script>
<script type="text/jsx" src="/app/account/AccountStore.js?v=<?php echo VERSION;?>"></script>

<!-- React Components -->
<script type="text/jsx" src="/app/account/profile.js?v=<?php echo VERSION;?>"></script>
<script type="text/jsx" src="/app/account/activities.js?v=<?php echo VERSION;?>"></script>
<script type="text/jsx" src="/app/account/tokens.js?v=<?php echo VERSION;?>"></script>
<script type="text/jsx" src="/app/account/token_analytics.js?v=<?php echo VERSION;?>"></script>
<script type="text/jsx" src="/app/account/viewers.js?v=<?php echo VERSION;?>"></script>
<script type="text/jsx" src="/app/account/viewer_edit.js?v=<?php echo VERSION;?>"></script>
<script type="text/jsx" src="/app/account/info.js?v=<?php echo VERSION;?>"></script>
<script type="text/jsx" src="/app/account/users.js?v=<?php echo VERSION;?>"></script>
<script type="text/jsx" src="/app/account/user_edit.js?v=<?php echo VERSION;?>"></script>
<script type="text/jsx" src="/app/account/user_remove.js?v=<?php echo VERSION;?>"></script>
<script type="text/jsx" src="/app/account/index.js?v=<?php echo VERSION;?>"></script>


<section class="profile" id="account-profile"></section>
<script type="text/jsx">
  React.render(<Account model={Model} />, document.getElementById('account-profile'));
</script>

<?php require __DIR__.'/footer.php';?>
</body>
</html>
