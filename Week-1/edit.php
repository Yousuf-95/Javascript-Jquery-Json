<?php
require_once "pdo.php";
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
    $sql = "UPDATE profile SET first_name = :firstn, last_name = :lastn,
            email = :email, headline = :headline, summary = :summary
            WHERE profile_id = :pid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':firstn' => $_POST['first_name'],
      ':lastn' => $_POST['last_name'],
      ':email' => $_POST['email'],
      ':headline' => $_POST['headline'],
      ':summary' => $_POST['summary'],
      ':pid' => $_POST['profile_id'])
    );

    $_SESSION['success'] = 'Record updated';
    header( 'Location: index.php' ) ;
    return;
  }
}

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
</head>
<body>
<?php
if ( isset($_SESSION['error']) ) {
  echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
  unset($_SESSION['error']);
}
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
<p>
<input type="hidden" name="profile_id" value="<?= $pid ?>"/>
<input type="submit" value="Save">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>

</div>
</body>
</html>
