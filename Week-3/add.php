<?php
require_once "pdo.php";
require_once "util.php";
session_start();

if (isset($_SESSION['name']) == false) {
die('ACCESS DENIED');
}
if(isset($_POST['cancel']) == true){
  header("Location: index.php");
  return;
}

if ( isset($_POST['first_name']) && isset($_POST['last_name'])
     && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {


    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = 'All fields are required';
        header("Location: add.php");
        return;
    }
    else if (strpos($_POST['email'], '@') == false) {
      $_SESSION['error'] = 'The mail must have an at (@) sign';
      header("Location: add.php");
      return;
    }
    else {
      $msg = ValidatePos();
      if(is_string($msg)){
        $_SESSION['error'] = $msg;
        header("Location: add.php");
        return;
      }
      $sql = 'INSERT INTO profile
        (user_id, first_name, last_name, email, headline, summary)
        VALUES ( :user_id, :firstn, :lastn, :email, :headline, :summary)';
      $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
          ':user_id' => $_SESSION['user_id'],
          ':firstn' => $_POST['first_name'],
          ':lastn' => $_POST['last_name'],
          ':email' => $_POST['email'],
          ':headline' => $_POST['headline'],
          ':summary' => $_POST['summary'])
        );

      $profile_id = $pdo->lastInsertId();

$rank=1;
for($i=1; $i <= 9; $i++){
  if( ! isset($_POST['year'.$i]) ) continue;
  if( ! isset($_POST['desc'.$i]) ) continue;
  $year = $_POST['year'.$i];
  $desc = $_POST['desc'.$i];

  $stmt = $pdo->prepare('INSERT INTO position
  (profile_id, rank, year, description)
  VALUES ( :pid, :rank, :year, :desc)');
  $stmt->execute(array(
    ':pid' => $profile_id,
    ':rank' => $rank,
    ':year' => $year,
    'desc' => $desc)
  );
  $rank++;
}
        $_SESSION['success'] = 'Record Added';
        header( 'Location: index.php' ) ;
        return;
  }
}
?>

<html>
<head>
  <title>Yousuf</title>
<script type="text/javascript" src="jquery.min.js"></script>
<?php require_once "head.php"; ?>
</head>
<body>
  <div class="container">
  <?php
FlashMessages();
?>
<h1>Adding Profile for <?php echo($_SESSION['name']) ?></h1>
<form method="post">
<p>First Name:
<input type="text" name="first_name"/></p>
<p>Last Name:
<input type="text" name="last_name"/></p>
<p>Email:
<input type="text" name="email"/></p>
<p>Headline:
<input type="text" name="headline"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"></textarea>
<p>Position: <input type="submit" id="addPos" value="+">
  <div id="position_fields"></div>
</p>
<p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
<script>
countpos = 0;
$(document).ready(function(){
  window.console && console.log('Document ready called');

  $('#addPos').click(function(event){
    event.preventDefault();

    if (countpos >= 9){
      alert("Maximum of nine position entries exceeded");
      return;
    }

    countpos++;
    window.console && console.log("Adding Position "+countpos);
    $('#position_fields').append(
      '<div id="position'+countpos+'"> \
       <p>Year: <input type="text" name="year'+countpos+'" value=""/> \
      <input type="button" value="-" onclick="$(\'#position'+countpos+'\').remove(); return false;"></p> \
      <textarea name="desc'+countpos+'" rows ="8" cols="80"></textarea> </div>');
  });
});
</script>

</div>
</body>
</html>
