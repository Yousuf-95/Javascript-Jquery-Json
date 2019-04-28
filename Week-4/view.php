<?php
require_once "pdo.php";
require_once "util.php";
session_start();

if( ! isset($_GET['profile_id'])){
  $_SESSION['error'] = "Missing profile_id";
  header("Location: index.php");
  return;
}
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

  $schools = loadEdu($pdo, $_REQUEST['profile_id']);
  $positions = loadPos($pdo, $_REQUEST['profile_id']);
?>

<!DOCTYPE html>
<html>
<head>
<title>Yousuf</title>
<?php require_once "head.php"; ?>
</head>
<body>
<div class="container">
<h1>Profile Information</h1>

<p>First Name: <?= $firstn ?></p>
<p>Last Name: <?= $lastn ?></p>
<p>Email: <?= $email ?> </p>
<p>Headline: <?= $headline ?> </p>
<p>Summary: <?= $summary ?> </p>
<?php
if(count($schools) > 0)
{
echo('<p>Education:</p>');
echo ("<ul>");
foreach( $schools as $school ){
  echo("<li>");
  echo(htmlentities($school['name'])." | ".$school['year']);
}
}
echo("</ul>");

if(count($positions) > 0)
{
echo('<p>Positions:</p>');
echo ("<ul>");
foreach( $positions as $position ){
  echo("<li>");
  echo(htmlentities($position['description'])." | ".$position['year']);
}
}
echo("</ul>");

?>
</div>
</body>
</html>
