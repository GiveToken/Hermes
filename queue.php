<?php
use \Sizzle\Bacon\Database\TokenQueue;

date_default_timezone_set('America/Chicago');

if (!logged_in()) {
  login_then_redirect_back_here();
}
$user_id = $_SESSION['user_id'] ?? '';
$count = (int) ($_GET['count'] ?? 0);

define('TITLE', 'S!zzle - Queue');
require __DIR__.'/header.php';
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/s/dt/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.10,b-1.1.0,b-flash-1.1.0,b-html5-1.1.0,b-print-1.1.0/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="/css/datatables.min.css"/>

</head>
<body id="token-listing">
  <div>
    <?php require __DIR__.'/navbar.php';?>
  </div>
  <div class="row" id="datatable-div">
    <div class="col-sm-offset-2 col-sm-8">
      <h2>Incoming Job Queue</h2>
      <table id="responsive-table" class="table table-striped table-hover">
        <thead>
          <th>Email</th>
          <th>Subject</th>
          <th>Source</th>
          <th>Created</th>
          <th>Work</th>
        </thead>
        <tbody>
            <?php
            $jobs = (new TokenQueue())->getUnworked($count);
            foreach ($jobs as $job) {
                echo '<tr>';
                echo "<td align=left>";
                echo $job['email_address'];
                echo "</td>";
                echo "<td align=left>";
                echo $job['subject'];
                echo "</td>";
                echo "<td align=left>";
                echo $job['source'];
                echo "</td>";
                echo "<td>".date('m/d/Y g:i a', strtotime($job['created']))."</td>";
                echo "<td>";
                echo "<a id=\"job-{$job['id']}\" class=\"mark-worked\">Work it!</a>";
                echo "</td>";
                echo '</tr>';
            }?>
        </tbody>
      </table>
    </div>
  </div>
    <?php require __DIR__.'/footer.php';?>
  <script type="text/javascript" src="https://cdn.datatables.net/s/dt/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.10,b-1.1.0,b-flash-1.1.0,b-html5-1.1.0,b-print-1.1.0/datatables.min.js"></script>
  <script>
  $(document).ready(function() {
      var table = $('#responsive-table').DataTable({
          dom: 'B<"clear">lfrtip',
          buttons: [
              'copy', 'csv', 'excel', 'pdf','print'
          ]
      });

      $('.mark-worked').click(function(e) {
        e.preventDefault();
        console.log($(this)[0].id)
        alert('this will open the creator tool')
      })
  });
  </script>
</body>
</html>
