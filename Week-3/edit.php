<?php
require_once "pdo.php";
require_once "util.php";
session_start();

if (isset($_SESSION['name']) == false) {
die('ACCESS DENIED');
}

if( isset($_POST['cancel'])){
  header('Location: index.php');
  return;
}

if ( isset($_POST['first_name']) && isset($_POST['last_name'])
     && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) && isset($_POST['profile_id'])) {

       if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
           $_SESSION['error'] = 'All fields are required';
           header("Location: edit.php?profile_id=" . htmlentities($_REQUEST['profile_id']));
           return;
       }
       else if (strpos($_POST['email'], '@') === false){
         $_SESSION['error'] = 'The mail must have an at (@) sign';
         header("Location: edit.php?profile_id=" . htmlentities($_REQUEST['profile_id']));
         return;
       }
       else {
         $msg = ValidatePos();
         if(is_string($msg))
         {
           $_SESSION['error'] = $msg;
           header("Location : edit.php?profile_id=".htmlentities($_GET['profile_id']));
           return;
         }

    $sql = "UPDATE profile SET first_name = :firstn, last_name = :lastn,
            email = :email, headline = :headline, summary = :summary
            WHERE profile_id = :pid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':pid' => $_POST['profile_id'],
      ':firstn' => $_POST['first_name'],
      ':lastn' => $_POST['last_name'],
      ':email' => $_POST['email'],
      ':headline' => $_POST['headline'],
      ':summary' => $_POST['summary'])
    );

    $stmt = $pdo->prepare('DELETE FROM position where profile_id = :pid');
    $stmt->execute(array(
      'pid' => $_GET['profile_id'] ));

    $rank=1;
      for($i=1; $i <= 9; $i++){
        if( ! isset($_POST['year'.$i]) ) continue;
        if( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        $stmt = $pdo->prepare('INSERT INTO position
        (profile_id, rank, year, description)
        VALUES ( :pid, :rank, :year, :descr)');
        $stmt->execute(array(
          ':pid' => $_GET['profile_id'],
          ':rank' => $rank,
          ':year' => $year,
          'descr' => $desc)
        );
        $rank++;
      }

    $_SESSION['success'] = 'Record updated';
    header( 'Location: index.php' ) ;
    return;
  }
}
$position = loadPos($pdo, $_GET['profile_id']);

if(! isset($_GET['profile_id']))
{
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}
else{
$stmt = $pdo->prepare('SELECT * FROM profile WHERE profile_id = :pid');
$stmt->execute(array(
  ':pid' => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

$firstn = htmlentities($row['first_name']);
$lastn = htmlentities($row['last_name']);
$email = htmlentities($row['email']);
$headline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);
$pid = ($row['profile_id']);
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

<h1>Editing Profile for UMSI</h1>
<form method="post">
<p>First Name:
<input type="text" name="first_name" value="<?= $firstn ?>"/></p>
<p>Last Name:
<input type="text" name="last_name" value="<?= $lastn ?>"/></p>
<p>Email:
<input type="text" name="email" value="<?= $email ?>"/></p>
<p>Headline:
<input type="text" name="headline" value="<?= $headline ?>"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"><?= $summary ?></textarea>
<p>Position: <input type="submit" id="addPos" value="+">
  <div id="position_fields"></div>
</p>
<p>
<input type="hidden" name="profile_id" value="<?= $pid ?>"/>
<input type="submit" value="Save">
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
