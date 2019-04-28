<?php
require_once "pdo.php";


$stmt = $pdo->query("SELECT * FROM profile");
echo('<table border="1">'."\n");

  echo("<tr>");
  echo("<th>Name</th>");
  echo("<th>Headline</th>");
  echo("</tr>");

while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    echo "<tr><td>";
    echo(htmlentities($row['first_name']));
    echo("</td><td>");
    echo(htmlentities($row['headline']));
    echo("</td></tr>\n");
  }
  ?>

<!DOCTYPE html>
<html>
<head>
<title>Yousuf</title>
</head>
<body>
<div class="container">
<h1>Yousuf's resume registry</h1>
<a href="login.php">Please log in</a>
<br/><br/>
</div>
</body>
</html>
