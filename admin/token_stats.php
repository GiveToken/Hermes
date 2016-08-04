<?php
use \Sizzle\Bacon\Database\RecruitingTokenResponse;

if (!logged_in()) {
    header('Location: '.'/');
}

date_default_timezone_set('America/Chicago');

define('TITLE', 'S!zzle - Token Stats');
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
      <h2>TOKENS</h2>
      <table class="table table-striped table-hover" id="responsive-table" hidden>
        <thead>
          <th>Hits</th>
          <th>Yes</th>
          <th>No</th>
          <th>Maybe</th>
          <th>Token</th>
          <th>Author</th>
          <th>Created</th>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT COUNT(DISTINCT web_request.id) as hits,
                    COALESCE(resp.yeses, 0) AS yeses,
                    COALESCE(resp.nos, 0) AS nos,
                    COALESCE(resp.maybes, 0) AS maybes,
                    recruiting_token.job_title, recruiting_token.long_id, recruiting_token.created,
                    user.email_address, user.first_name, user.last_name, user.id user_id
                    FROM user, recruiting_token
                    LEFT JOIN
                    (SELECT id, SUBSTRING_INDEX(SUBSTR(uri, LOCATE('recruiting/', uri)+11), '?', 1) long_id
                     FROM web_request
                     WHERE uri LIKE '/token/recruiting/%'
                     AND web_request.user_id IS NULL
                    ) AS web_request
                    ON web_request.long_id = recruiting_token.long_id
                    LEFT JOIN
                    (SELECT SUM(IF(response = 'Yes', 1, 0)) AS yeses,
                     SUM(IF(response = 'No', 1, 0)) AS nos,
                     SUM(IF(response = 'Maybe', 1, 0)) AS maybes,
                     recruiting_token_id
                     FROM recruiting_token_response
                     GROUP BY recruiting_token_response.recruiting_token_id
                    ) AS resp
                    ON resp.recruiting_token_id = recruiting_token.id
                    WHERE user.id = recruiting_token.user_id
                    GROUP BY recruiting_token.id
                    ORDER BY hits DESC, yeses DESC, job_title";
            $results = execute_query($sql);
            $rows = array();
            while ($row = $results->fetch_assoc()) { ?>
                <tr>
                  <td><?php echo $row['hits'];?></td>
                  <td><?php echo $row['yeses'];?></td>
                  <td><?php echo $row['nos'];?></td>
                  <td><?php echo $row['maybes'];?></td>
                  <td>
                    <a href="<?=APP_URL?>token/recruiting/<?php echo $row['long_id'];?>" target=_blank>
                        <?php echo $row['job_title'];?>
                    </a>
                    <a href="<?=APP_URL?>create_recruiting?id=<?php echo $row['long_id'];?>" target=_blank>
                      <span class="glyphicon glyphicon-pencil pull-right"></span>
                    </a>
                  </td>
                  <td>
                    <?php
                    echo "<a href=\"/user/{$row['user_id']}\">{$row['first_name']} {$row['last_name']}";
                    echo " ({$row['email_address']})</a>";
                    ?>
                  </td>
                  <td><?php echo date('m/d/Y g:i a', strtotime($row['created']));?></td>
                </tr>
            <?php }?>
        </tbody>
      </table>
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
