<?php
if (logged_in()) {
    header('Location: /admin');
}
define('TITLE', 'Hermes');
include __DIR__.'/header.php';
?>
<meta property="og:video" content="https://www.youtube.com/watch?v=uHzRX-8jC3s" />
<meta property="og:site_name" content="S!zzle" />
<meta property="og:title" content="S!zzle" />

</head>
<body>

  <?php include __DIR__.'/footer.php';?>
  <script>
  /**
   * Registers click on hidden file input.
   *
   * @param {String} identifier The jQuery identifier to click
   */
  function fireHiddenFileInput(identifier) {
     $(identifier).trigger('click');
  }
  $( document ).ready(function() {
    $("#login-dialog").modal();
  });
  </script>
</body>
</html>
