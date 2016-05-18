<?php
use \Sizzle\Bacon\Database\{
    Experiment,
    ExperimentNote
};

if (!logged_in()) {
    header('Location: '.'/');
}

date_default_timezone_set('America/Chicago');

// collect id
$endpoint_parts = explode('/', $_SERVER['REQUEST_URI']);
if (isset($endpoint_parts[2]) && (int) $endpoint_parts[2] > 0) {
    $experiment_id = (int) $endpoint_parts[2];
    $experiment = new Experiment($experiment_id);
} else {
    $experiment_id = 0;
}

define('TITLE', 'S!zzle - Experiment Info');
require __DIR__.'/header.php';
?>
<style>
body {
  background-color: white;
}
#org-info {
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
<body id="experiment-info">
  <div>
    <?php require __DIR__.'/navbar.php';?>
  </div>
  <!-- Modal -->
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Add Note</h4>
        </div>
        <div class="modal-body">
          <textarea class="form-control" rows="5" id="new-note-text"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="save-note">Save Note</button>
        </div>
      </div>
    </div>
  </div>
  <div class="row" id="org-info">
    <div class="col-sm-offset-1 col-sm-10">
        <?php if (0 !== $experiment_id) { ?>
        <h3><i class="greyed"><?php echo $experiment->title;?></i></h3>
        Created <?php echo date('m/d/Y g:i a', strtotime($experiment->created));?>
        <br />
        <h4><i class="greyed">Activity:</i></h4>
        <button class="btn" id="add-note-button" data-toggle="modal" data-target="#myModal">Add Note</button>
        <br /><br />
        <?php
        $notes = (new ExperimentNote())->getAll($experiment->id);
        foreach ($notes as $note) {
            echo "{$note['created']} - ";
            echo "<a href=\"/user/{$note['user_id']}\">{$note['user']}</a><br />";
            echo "<pre>{$note['note']}</pre></br>";
        }
        ?>
        <br />
        <?php } else { ?>
        <h2>Invalid Experiment</h2>
        <?php }?>
    </div>
  </div>
  <?php require __DIR__.'/footer.php';?>
  <script>
    $(document).ready(function() {
        $('#save-note').click(function(event){
          event.preventDefault();
          $.post(
            '/ajax/experiment/add_note',
            {
              'note': $('#new-note-text').val(),
              'experiment': '<?=$experiment->id?>'
            },
            function(){
              window.location.href = '<?=$_SERVER['REQUEST_URI']?>'
            }
          )
        })
    });
  </script>
</body>
</html>
