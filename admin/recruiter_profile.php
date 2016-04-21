<?php
use \Sizzle\Bacon\Database\{
    Organization,
    User
};

if (!logged_in()) {
    header('Location: '.'/');
}

$user_id = (int) ($_GET['user_id'] ?? null);
$recruiter = new User($user_id);
if ($user_id > 0) {
  $recruiter->first_name = $_GET['first_name'] ?? $recruiter->first_name;
  $recruiter->last_name = $_GET['last_name'] ?? $recruiter->last_name;
  $recruiter->position = $_GET['position'] ?? $recruiter->position;
  $recruiter->linkedin = $_GET['linkedin'] ?? $recruiter->linkedin;
  $recruiter->about = $_GET['about'] ?? $recruiter->about;
  $recruiter->face_image = $_GET['image_file'] ?? $recruiter->face_image;
  $recruiter->save();
}
$organization = new Organization($recruiter->organization_id ?? null);

// Get User list to choose from
$from_users = execute_query(
    "SELECT user.id, user.first_name, user.last_name, user.email_address
     FROM user
     ".($user_id > 0 ? "WHERE id = '$user_id'" : '')."
     GROUP BY user.id
     ORDER BY user.last_name, user.first_name, user.email_address"
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
          <h1>Update Recruiter Profile</h1>
          <form id="update-recruiter-profile-form">
            <div class="form-group" id="user-id">
              <?php if (count($from_users) > 1) { ?>
                  <select class="form-control" name="user_id" required>
                    <option id="please-select">Please select a user</option>
                    <?php foreach ($from_users as $user) {
                        echo "<option value=\"{$user['id']}\">";
                        echo "{$user['first_name']} {$user['last_name']}";
                        echo " ({$user['email_address']})";
                        echo "</option>";
                    }?>
                  </select>
              <?php } else if (count($from_users) == 1){
                echo "<a href=\"/user/{$user_id}\">";
                echo "{$from_users[0]['first_name']} {$from_users[0]['last_name']}";
                echo " ({$from_users[0]['email_address']})";?>
                </a>
                <input
                  type="hidden"
                  name="user_id"
                  value="<?php echo $recruiter->id ?? '';?>">
              <?php }?>
            </div>
            <div class="form-group">
              <input type="file" id="exampleInputFile" name="exampleInputFile">
              <input
                type="hidden"
                id="image_file"
                name="image_file"
                value="<?php echo $recruiter->face_image ?? '';?>">
              <p class="help-block">(ignore the file chooser unless you want to upload a new one)</p>
            </div>
            <div class="form-group form-inline">
              <input
                type="text"
                class="form-control"
                name="first_name"
                placeholder="First Name"
                value="<?php echo $recruiter->first_name ?? '';?>">
              <input
                type="text"
                class="form-control"
                name="last_name"
                placeholder="Last Name"
                value="<?php echo $recruiter->last_name ?? '';?>">
              <input
                type="text"
                class="form-control"
                name="position"
                placeholder="Title"
                value="<?php echo $recruiter->position ?? '';?>">
              <input
                type="text"
                class="form-control"
                name="employer"
                placeholder="Recruiting Organization"
                value="<?php echo $organization->name ?? '';?>">
            </div>
            <div class="form-group">
              <input
                type="text"
                class="form-control"
                name="linkedin"
                placeholder="LinkedIn Link"
                value="<?php echo $recruiter->linkedin ?? '';?>">
            </div>
            <div class="form-group">
              <input
                type="text"
                class="form-control"
                name="website"
                placeholder="Recruiting Organization Website"
                value="<?php echo $organization->website ?? '';?>">
            </div>
            <div class="form-group">
              <textarea
                class="form-control"
                name="about"
                placeholder="Full Bio"
                rows="4"><?php echo $recruiter->about ?? '';?></textarea>
            </div>
            <button type="submit" class="btn btn-success" id="submit-recruiter-profile">Save</button>
          </form>
          <a href="/admin/edit_organization?id=<?= $organization->id ?>">
            <button class="btn pull-right" id="edit-org-button">Edit Organization</button>
          </a>
        </div>
  </div>
  <?php require __DIR__.'/../footer.php';?>
  <script>
  $(document).ready(function(){
    $('#submit-recruiter-profile').on('click', function (event) {
      event.preventDefault();

      //save image
      if ($('#exampleInputFile')[0].files[0] !== undefined) {
        var file = $('#exampleInputFile')[0].files[0];
        var reader  = new FileReader();
        reader.fileName = file.name;
        reader.onloadend = function () {
          var xhr = new XMLHttpRequest();
          if (xhr.upload) {
            xhr.open("POST", "/upload", true);
            xhr.setRequestHeader("X-FILENAME", file.name);
            xhr.send(reader.result);
          }
        };
        reader.readAsDataURL(file);
        $('#image_file').val(file.name);
      }

      $('#update-recruiter-profile-form').submit();
    });
  });
  </script>
</body>
</html>
