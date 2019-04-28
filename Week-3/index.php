<?php
require_once "pdo.php";
require_once "util.php";
session_start();

if(!isset($_SESSION['name'])) {
  header("Location: view.php");
  return;
}
?>

<html>
<head>
<title>Yousuf</title>
<?php require_once "head.php"; ?>
</head>
<body>
  <div class="container">
  <h1> Yousuf's Resume Registry </h1>
  <form method = "post">
<?php
FlashMessages();

echo('<table border="1">'."\n");
echo("<tr>");
echo("<th>Name</th>");
echo("<th>Headline</th>");
echo("<th>Action</th>");
echo("</tr>");
$stmt = $pdo->query("SELECT * FROM profile");
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr><td>";
    echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name']." ".$row['last_name']).'</a> ');
    echo("</td><td>");
    echo(htmlentities($row['headline']));
    echo("</td><td>");
    echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> ');
    echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
    echo("</td></tr>\n");
}
?>
</table>
</form>
<br>
<a href="add.php">Add New Entry</a> |
<a href="logout.php">Logout</a>
</div>
</body>
</html>
