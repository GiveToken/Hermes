<?php
use \Sizzle\Bacon\Database\User;

if (!logged_in()) {
    header('Location: '.'/');
}

// Get User list to choose from
$from_users = execute_query(
    "SELECT user.id, user.first_name, user.last_name, user.email_address
     FROM user
     JOIN recruiting_token on user.id = recruiting_token.user_id
     GROUP BY user.id
     ORDER BY user.last_name, user.first_name, user.email_address"
)->fetch_all(MYSQLI_ASSOC);

// Get User list to choose to transfer to
$to_users = execute_query(
    "SELECT user.id, user.first_name, user.last_name, user.email_address
     FROM user
     GROUP BY user.id
     ORDER BY user.last_name, user.first_name, user.email_address"
)->fetch_all(MYSQLI_ASSOC);

define('TITLE', 'S!zzle - Transfer Token');
require __DIR__.'/../header.php';
?>
<style>
body {
  background-color: white;
}
#transfer-token-form {
  margin-top: 100px;
}
</style>
</head>
<body id="transfer-token-body">
  <div>
    <?php require __DIR__.'/../navbar.php';?>
  </div>
  <div class="row" id="transfer-token-form">
    <div class="col-sm-offset-3 col-sm-6">
      <h1>Transfer Token</h1>
      <form>
        <div class="form-group" id="old-user-id">
          <label for="old_user_id" class="col-sm-3 control-label">
            Current Owner
          </label>
          <select class="form-control" name="old_user_id" required>
            <option id="please-select">Please select a user</option>
            <?php foreach ($from_users as $user) {
                echo "<option value=\"{$user['id']}\">";
                echo "{$user['first_name']} {$user['last_name']}";
                echo " ({$user['email_address']})";
                echo "</option>";
            }?>
          </select>
        </div>
        <div class="form-group" id="token-id" hidden>
          <label for="token_id" class="col-sm-2 control-label">Token</label>
          <select class="form-control" name="token_id" required>

          </select>
        </div>
        <div class="form-group" id="new-user-id" hidden>
          <label for="new_user_id" class="col-sm-3 control-label">
            New Owner
          </label>
          <select class="form-control" name="new_user_id" required>
            <?php foreach ($to_users as $user) {
                echo "<option value=\"{$user['id']}\">";
                echo "{$user['first_name']} {$user['last_name']}";
                echo " ({$user['email_address']})";
                echo "</option>";
            }?>
          </select>
        </div>
        <button type="submit" class="btn btn-success" id="submit-transfer-token">
          Submit
        </button>
      </form>
    </div>
  </div>
  <?php require __DIR__.'/../footer.php';?>
  <script>
  $(document).ready(function(){
    $('#submit-transfer-token').hide();

    // Get token list to choose from
    $('#old-user-id').on('change', function() {
      $('#please-select').remove();
      $('#token-id').show();
      $('#new-user-id').show();
      $('#submit-transfer-token').show();
      $.post(
        '/ajax/user/get_recruiting_tokens/'+$('#old-user-id select').val(),
        {},
        function (data) {
          html = '';
          $.each(data.data, function (key, value) {
            html += '<option value="'+value.long_id+'">';
            html += value.job_title+' ('+value.long_id+')';
            html +='</option>';
          });
          $('#token-id select').html(html);
        }
      );
    })

    // when form is submitted
    $('#submit-transfer-token').on('click', function (event) {
      event.preventDefault();

      // save info in the database
      url = '/ajax/recruiting_token/transfer';
      $.post(url, $('form').serialize(), function(data) {
        if (data.success === 'true') {
          $('#transfer-token-form').html("<h1>Token Transfer Complete!</h1>");
          $('#transfer-token-form').css('margin-bottom','500px');
          window.scrollTo(0, 0);
        } else if (data.success === 'false') {
          alert(data.data.error);
        } else {
          alert('All fields required!');
        }
      });
    });
  });
  </script>
</body>
</html>
