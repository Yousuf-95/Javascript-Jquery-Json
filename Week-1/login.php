<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';

if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
      $_SESSION['error'] = "Email and password are required";
      header("Location: login.php");
      return;
    }
    else if(strpos($_POST['email'], '@') == false){
      $_SESSION['error'] = "Email must have an at-sign (@)";
      header("Location: login.php");
      return;
    }
    else {
      $check = hash('md5', $salt.$_POST['pass']);
      $stmt = $pdo->prepare('SELECT user_id, name FROM users
      WHERE email = :em AND password = :pw');
      $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ( $row !== false ) {
          $_SESSION['name'] = $row['name'];
          $_SESSION['user_id'] = $row['user_id'];
          header("Location: index.php");
          return;
        } else {
            error_log("Login fail ".$_POST['email']." $check");
            $_SESSION['error'] = "Incorrect password";
            header("Location: login.php");
            return;
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Yousuf</title>
</head>
<body>
<h1>Please Log In</h1>
<?php
if ( isset($_SESSION['error']) ) {
  echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
  unset($_SESSION['error']);
}
?>
<form method="POST">
<p><label for="nam">User Name</label>
<input type="text" name="email" id="nam"><br/><p>
<p><label for="pass">Password</label>
<input type="password" name="pass" id="pass"></p>
<input type="submit" onclick="return doValidate(); return false" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
For a password hint, view source and find a password hint
in the HTML comments.
<!-- Hint: The password is php123 -->
</p>

<script type="text/javascript">
function doValidate() {

console.log('Validating...');
try {
pw = document.getElementById('pass').value;
nam = document.getElementById('nam').value;
console.log("Validating name="+nam);
console.log("Validating pw="+pw);

if (pw == null || pw == "") {
alert("Both fields must be filled out");
return false;
}
if(nam == null || nam == ""){
  alert("E-mail must not be empty")
return false;
}

return true;

} catch(e) {

return false;

}

return false;

}
</script>
</body>
</html>
