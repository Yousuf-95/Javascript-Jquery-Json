<?php
require_once "pdo.php";
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

        $_SESSION['success'] = 'Record Added';
        header( 'Location: index.php' ) ;
        return;
  }
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
<p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="Cancel">
</p>

</div>
</body>
</html>
