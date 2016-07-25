<?php
use \Sizzle\Report;

if (!logged_in()) {
    header('Location: '.'/');
}

date_default_timezone_set('America/Chicago');

if (isset($_GET['reload']) && 'true' === $_GET['reload']) {
    unset($_SESSION['report']['inactiveOrganizations']);
}

define('TITLE', 'Inactive Organizations');
require __DIR__.'/../header.php';
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/s/dt/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.10,b-1.1.0,b-flash-1.1.0,b-html5-1.1.0,b-print-1.1.0/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="/css/datatables.min.css"/>
<style>
body {
  background-color: white;
}
#tokens-table {
  margin-top: 100px;
  color: black;
  font-size: 13px;
  margin-bottom: 50px;
}
</style>
</head>
<body id="visitors">
  <div>
    <?php require __DIR__.'/../navbar.php';?>
  </div>
  <div class="row">
    <div class="col-sm-offset-1 col-sm-10" id="tokens-table">
      <h2>Inactive Organizations</h2>
      <table class="table table-striped table-hover" id="responsive-table" hidden>
        <thead>
          <th>Organiztion</th>
          <th>Customer Since</th>
          <th>Payer</th>
          <th>Inactivity</th>
        </thead>
        <tbody>
            <?php
            if (isset($_SESSION['report']['inactiveOrganizations'])) {
                $orgs = $_SESSION['report']['inactiveOrganizations']['data'];
            } else {
                $orgs = (new Report())->inactiveOrganizations();
                $_SESSION['report']['inactiveOrganizations']['data'] = $orgs;
                $_SESSION['report']['inactiveOrganizations']['cached'] = time();
            }
            foreach ($orgs as $row) { ?>
                <tr>
                  <td>
                    <a href="/organization/<?=$row['id']?>" target=_blank>
                        <?=$row['name']?>
                    </a>
                  </td>
                  <td><?=date('m/d/Y', strtotime($row['created']))?></td>
                  <td>
                    <?php if (isset($row['paying_user'])) {?>
                        <a href="/user/<?=$row['paying_user']?>" target=_blank>
                          <?=$row['paying_user']?>
                        </a>
                    <?php } else { ?>
                        None
                    <?php }?>
                  </td>
                  <td style="color:<?=('Week' == $row['inactive'] ? 'orange' : 'red')?>">
                    Inactive more than a <?=$row['inactive']?>
                  </td>
                </tr>
            <?php }?>
        </tbody>
      </table>
      * Inactive means no logged in activity or non-user token views.
      Only paying organizations and organizations in their free month are included.
      <br />
      ** Report has a one week cache duration (<a href="<?=$_SERVER['REQUEST_URI']?>?reload=true">reload</a>).
    </div>
  </div>
  <?php require __DIR__.'/../footer.php';?>
  <script type="text/javascript" src="https://cdn.datatables.net/s/dt/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.10,b-1.1.0,b-flash-1.1.0,b-html5-1.1.0,b-print-1.1.0/datatables.min.js"></script>
  <script>
  $(document).ready(function() {
      var table = $('#responsive-table').DataTable({
          dom: 'B<"clear">lfrtip',
          buttons: [
              'copy', 'csv', 'excel', 'pdf','print'
          ]
      });
      $('#responsive-table').show();
  });
  </script>
</body>
</html>
