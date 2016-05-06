<?php
use \Sizzle\Bacon\Database\{
    RecruitingToken,
    User
};

if (!logged_in()) {
    header('Location: '.'/');
}

// Get User list to choose from
$users = execute_query(
    "SELECT user.id, user.first_name, user.last_name, user.email_address
     FROM user
     WHERE id IN (SELECT user_id FROM recruiting_token)
     GROUP BY user.id
     ORDER BY user.email_address ASC"
)->fetch_all(MYSQLI_ASSOC);

define('TITLE', 'S!zzle - Update Recruiter Profile');
require __DIR__.'/../header.php';
?>
<style>
body {
  background-color: white;
}
#user-info {
  margin-top: 100px;
  color: black;
  text-align: left;
}
.greyed {
  background-color: lightgrey;
  font-style: normal;
}
</style>
</head>
<body id="recruiter">
  <div>
    <?php require __DIR__.'/../navbar.php';?>
  </div>
  <div class="row" id="user-info">
    <div class="col-sm-offset-1 col-sm-8">
      <h1>Send User a Token</h1>
      <form id="send-form">
        <div class="form-group" id="user-id">
          <?php if (count($users) > 1) { ?>
              <label for="user_id" class="col-sm-2 control-label">User</label>
              <select class="form-control" name="user_id">
                <option id="please-select">Please select the user</option>
                <?php foreach ($users as $user) {
                    echo "<option value=\"{$user['id']}\"";
                    echo isset($organization->paying_user) && $user['id'] == $organization->paying_user ? ' selected>' : '>';
                    echo "{$user['first_name']} {$user['last_name']}";
                    echo " ({$user['email_address']})";
                    echo "</option>";
                }?>
              </select>
          <?php }?>
        </div>
        <div class="form-group" id="token-id" hidden>
          <label for="token_id" class="col-sm-2 control-label">Token</label>
          <select class="form-control" name="token_id" required>

          </select>
        </div>
        <div class="form-group">
          <input
            type="text"
            class="form-control"
            name="subject"
            placeholder="Subject">
        </div>
        <div class="form-group">
          <input
            type="text"
            class="form-control"
            name="cc"
            placeholder="CC">
        </div>
        <div class="form-group">
          <textarea
            class="form-control"
            name="message"
            placeholder="Message for the User"
            rows="4"></textarea>
        </div>
        <button type="submit" class="btn btn-success" id="send-button">Send</button>
      </form>
    </div>
  </div>
  <?php require __DIR__.'/../footer.php';?>
  <script>
  $(document).ready(function(){
    $('#submit-transfer-token').hide();

    // Get token list to choose from
    $('#user-id').on('change', function() {
      $('#please-select').remove();
      $('#token-id').show();
      $.post(
        '/ajax/user/get_recruiting_tokens/'+$('#user-id select').val(),
        {},
        function (data) {
          html = '';
          $.each(data.data, function (key, value) {
            html += '<option value="'+value.id+'">';
            html += value.job_title+' ('+value.long_id+')';
            html +='</option>';
          });
          $('#token-id select').html(html);
        }
      );
    })

    // when form is submitted
    $('#send-button').on('click', function (event) {
      event.preventDefault();

      // save info in the database
      url = '/ajax/recruiting_token/send';
      $.post(url, $('form').serialize(), function(data) {
        if (data.success === 'true') {
          $('#send-form').html("<h1>Email Sent!</h1>");
          $('#send-form').css('margin-bottom','500px');
          window.scrollTo(0, 0);
        } else {
          message = (typeof data.data.error !== undefined) ? data.data.error : 'All fields required!';
          alert(message);
        }
      }, 'json');
    });
  });
  </script>
</body>
</html>
