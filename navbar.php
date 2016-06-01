<!-- STICKY NAVIGATION (Animation removed)-->
<div class="navbar navbar-inverse bs-docs-nav navbar-fixed-top sticky-navigation">
  <div class="container">
    <div class="navbar-header">

      <!-- LOGO ON STICKY NAV BAR -->
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#kane-navigation">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      </button>

      <?php if (ENVIRONMENT != 'production') { ?>
        <h3 class="pull-right" style="color:red;">DEVELOPMENT</h3>
      <?php }?>

      <a class="navbar-brand" href="<?php echo '/' ?>">
        <img src="/images/sizzle-logo.png" alt="">
      </a>

    </div>

    <!-- NAVIGATION LINKS -->
    <div class="navbar-collapse collapse" id="kane-navigation">
      <ul class="nav navbar-nav navbar-right main-navigation">
        <?php if (logged_in()) { ?>
            <li><a href="http://blog.gosizzle.io" class="external sizzle-nav-choice">Blog</a></li>
            <li><a href="/tokens" class="external sizzle-nav-choice">My Tokens</a></li>
            <li><a href="/admin" class="external sizzle-nav-choice">Admin</a></li>
            <li><a href="javascript:void(0)" class="sizzle-nav-choice" id="logout-button" onclick="logout();">Logout</a></li>
        <?php } else { ?>
            <li><a href="javascript:void(0)" onclick="loginOpen()" class="sizzle-nav-choice">Login</a></li>
        <?php }?>
      </ul>
    </div>
  </div> <!-- /END CONTAINER -->
</div> <!-- /END STICKY NAVIGATION -->
