<?php
use \Sizzle\Bacon\Database\User;

if (!logged_in()) {
    header('Location: '.'/');
}

define('TITLE', 'S!zzle - Create Account');
require __DIR__.'/../header.php';
?>
<style>
body {
  background-color: white;
}
#create-account-form {
  margin-top: 100px;
  margin-bottom: 200px;
}
</style>
</head>
<body id="create-account-body">
  <div>
    <?php require __DIR__.'/../navbar.php';?>
  </div>
  <div class="row" id="create-account-form">
    <div class="col-sm-offset-3 col-sm-6">
      <h1>Create Account</h1>
      <form>
        <font color=red id="error-message"></font>
        <div class="form-group" id="old-user-id">
          <div class="form-group">
            <input type="text" class="form-control" id="signup_email" name="signup_email" placeholder="Email" required>
          </div>
          <div class="form-group">
            <input type="text" class="form-control" id="token_id" name="token_id" placeholder="Token ID" required>
          </div>
        </div>
        <button type="submit" class="btn btn-success" id="submit-create-account">
          Create Account
        </button>
      </form>
    </div>
  </div>
  <?php require __DIR__.'/../footer.php';?>
  <script>
  $(document).ready(function(){
    // when form is submitted
    $('#submit-create-account').on('click', function (event) {
      event.preventDefault();

      // save info in the database
      url = '/ajax/user/create';
      $.post(url, $('form').serialize(), function(data) {
        if (data.success === 'true') {
          $('#create-account-form').html("Activation Link:<br /> "+data.data.url);
          $('#create-account-form').css('margin-bottom','500px');
          window.scrollTo(0, 0);
        } else {
          var message = data.data.errors !== undefined ? data.data.errors : 'All fields required!';
          $('#error-message').html(message);
        }
      });
    });
  });
  </script>
</body>
</html>
