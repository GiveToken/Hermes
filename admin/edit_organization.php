<?php
use \Sizzle\Bacon\Database\{
    Organization
};

if (!logged_in()) {
    header('Location: '.'/');
}

$organization_id = (int) ($_GET['id'] ?? null);
$organization = new Organization($organization_id);
if (0 < (int) ($_GET['paying_user'] ?? '')) {
    $organization->paying_user = (int) $_GET['paying_user'];
}
$organization->name = $_GET['name'] ?? $organization->name;
$organization->website = $_GET['website'] ?? $organization->website;

// Get User list to choose from
$from_users = execute_query(
    "SELECT user.id, user.first_name, user.last_name, user.email_address
     FROM user
     WHERE organization_id = '$organization_id'
     AND stripe_id IS NOT NULL
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
          <h1>Update Organization</h1>
          <form id="update-organization-form">
            <div class="form-group" id="user-id">
              <?php if (count($from_users) > 1) { ?>
                  <select class="form-control" name="paying_user">
                    <option id="please-select">Please select the paying user</option>
                    <?php foreach ($from_users as $user) {
                        echo "<option value=\"{$user['id']}\"";
                        echo isset($organization->paying_user) && $user['id'] == $organization->paying_user ? ' selected>' : '>';
                        echo "{$user['first_name']} {$user['last_name']}";
                        echo " ({$user['email_address']})";
                        echo "</option>";
                    }?>
                  </select>
              <?php }?>
            </div>
            <div class="form-group">
              <input
                type="hidden"
                id="id"
                name="id"
                value="<?php echo $organization->id ?? '';?>">
              <input
                type="text"
                class="form-control"
                name="name"
                placeholder="Name"
                value="<?php echo $organization->name ?? '';?>">
            </div>
            <div class="form-group">
              <input
                type="text"
                class="form-control"
                name="website"
                placeholder="Website"
                value="<?php echo $organization->website ?? '';?>">
            </div>
            <button type="submit" class="btn btn-success" id="submit-organization">Save</button>
          </form>
        </div>
  </div>
  <?php require __DIR__.'/../footer.php';?>
  <script>
  $(document).ready(function(){
    $('#submit-organization').on('click', function (event) {
      event.preventDefault();

      $('#update-organization-form').submit();
    });
  });
  </script>
</body>
</html>
