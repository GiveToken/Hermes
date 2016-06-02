<?php if (!logged_in()) { ?>
  <!-- MODALS -->
  <div class="modal fade" id="login-dialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class ="modal-header" style="border-bottom: 0px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div id="login-alert-placeholder"></div>
          <form id="login-form">
            <input type="hidden" name="login_type" value="EMAIL">
            <input class="dialog-input large-input" id="login_email" name="login_email" type="text" placeholder="Email address" size="25">
            <input class="dialog-input large-input" id="password" name="password" type="password" placeholder="Password" size="25">
          </form>
          <div type="button" class="btn-lg btn-primary dialog-button-center" onclick="loginEmail()" style="border: 1px solid #e5e5e5; margin-top: 15px;margin-right: 20px; margin-left: 20px; text-align: center;">
            Log In
          </div>
        </div>
      </div>
    </div>
  </div>
<?php }?>
<!-- =========================
     FOOTER
============================== -->
<footer id="contact" class="deep-dark-bg mt20">

<div class="container">
  <!-- LOGO -->
  <img src="/images/sizzle-logo.png" alt="LOGO">

  <!-- COPYRIGHT TEXT -->
  <p class="copyright">
    &copy;2016 GoSizzle.io, GiveToken.com &amp; Giftly Inc., All Rights Reserved.
  </p>

</div>
<!-- /END CONTAINER -->

</footer>
<!-- /END FOOTER -->


<!-- =========================
     SCRIPTS
============================== -->
<script src="/js/dist/sizzle.min.js?v=<?php echo VERSION;?>"></script>
<?php if (!logged_in()) {
  /** TODO Move these into marketing.min build */
?>
  <script src="/js/login.min.js?v=<?php echo VERSION;?>"></script>
<?php }?>
