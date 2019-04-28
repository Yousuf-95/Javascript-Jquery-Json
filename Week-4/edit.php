<?php
require_once "pdo.php";
require_once "util.php";
session_start();

if (isset($_SESSION['name']) == false) {
die('ACCESS DENIED');
}

if( isset($_POST['cancel']) == true){
  header('Location: index.php');
  return;
}

if ( isset($_POST['first_name']) && isset($_POST['last_name'])
     && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) && isset($_POST['profile_id'])) {

    $msg = ValidateProfile();
    if(is_string($msg))
         {
           $_SESSION['error'] = $msg;
           header("Location : edit.php?profile_id=".htmlentities($_REQUEST['profile_id']));
           return;
         }

         $msg = ValidatePos();
              if(is_string($msg))
              {
                $_SESSION['error'] = $msg;
                header("Location : edit.php?profile_id=".htmlentities($_REQUEST['profile_id']));
                return;
              }

              $msg = ValidateEdu();
         if(is_string($msg))
         {
           $_SESSION['error'] = $msg;
           header("Location : edit.php?profile_id=".htmlentities($_REQUEST['profile_id']));
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
               'pid' => $_REQUEST['profile_id'] ));

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

               $institution_id = array();
               for ($k=1; $k <= 9 ; $k++)	{
                   $stmt = $pdo->prepare('SELECT * FROM Institution WHERE name = :name');
                   $stmt->execute(array(':name' => $_POST['edu_school'.$k]));
                   $row = $stmt->fetch(PDO::FETCH_ASSOC);

                   if ($row !== false) {
                     $institution_id[$k] = $row['institution_id'];
                   } else {
                     if(strlen($_POST['edu_school'.$k]) < 1) continue;
                     $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES (:name)');
                     $stmt->execute(array(':name' => $_POST['edu_school'.$k]));

                     $institution_id[$k] = $pdo->lastInsertId();
                   }
                 }

               $stmt = $pdo->prepare('DELETE FROM education where profile_id = :pid');
               $stmt->execute(array(
                 'pid' => $_REQUEST['profile_id'] ));

                 $rank=1;
                   for($i=1; $i <= 9; $i++){
                     if( ! isset($_POST['edu_year'.$i]) ) continue;
                     if( ! isset($_POST['edu_school'.$i]) ) continue;
                     $edu_year = $_POST['edu_year'.$i];
                     $edu_school = $_POST['edu_school'.$i];

                     $stmt = $pdo->prepare('INSERT INTO education
                     (profile_id, institution_id, rank, year)
                     VALUES ( :pid, :inst, :rank, :year)');
                     $stmt->execute(array(
                       ':pid' => $_GET['profile_id'],
                       ':inst' => $institution_id[$i],
                       ':rank' => $rank,
                       ':year' => $_POST['edu_year'.$i])
                     );
                     $rank++;
                   }


             $_SESSION['success'] = 'Record updated';
             header( 'Location: index.php' ) ;
             return;
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

$schools = loadEdu($pdo, $_REQUEST['profile_id']);
$positions = loadPos($pdo, $_REQUEST['profile_id']);

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
<?php
$countEdu = 0;
echo('<p>Education: <input type="submit" id="addEdu" value="+">'."\n");
echo('<div id ="edu_fields">'."\n");
if ( count($schools)>0){
  foreach( $schools as $school ){
    $countEdu++;
    echo('<div id="edu'.$countEdu.'">');
    echo '<p>Year: <input type="text" name="edu_year'.$countEdu.'" value="'.$school['year'].'"/>
    <input type="button" value="-" onclick="$(\'#edu'.$countEdu.'\').remove(); return false;"/></p>
    <p>School: <input type="text" size="75" name="edu_school'.$countEdu.'" class="school" value="'.htmlentities($school['name']).'"/>';
    echo "\n</div>\n";
  }
}
echo("</div></p>\n");

$countPos = 0;
echo('<p>Position: <input type="submit" id="addPos" value="+">'."\n");
echo('<div id ="position_fields">'."\n");
if ( count($positions)>0){
  foreach( $positions as $position ){
    $countPos++;
    echo('<div id="position'.$countPos.'">');
    echo '<p>Year: <input type="text" name="year'.$countPos.'" value="'.$position['year'].'"/>
    <input type="button" value="-" onclick="$(\'#position'.$countPos.'\').remove(); return false;"/></p>';
    echo '<textarea name="desc'.$countPos.'" rows="8" cols="80">'."\n";
    echo htmlentities($position['description'])."\n";
    echo "\n</textarea>\n</div>\n";
  }
}
 echo("</div></p>\n");
?>
<p>
<input type="hidden" name="profile_id" value="<?= $pid ?>"/>
<input type="submit" value="Save">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
<script>
countPos = <?= $countPos ?>;
countEdu = <?= $countEdu ?>;

$(document).ready(function(){
    window.console && console.log('Document ready called');

    $('#addPos').click(function(event){
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);

        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });

    $('#addEdu').click(function(event){
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of nine education entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding education "+countEdu);
        var source  = $("#edu-template").html();
        $('#edu_fields').append(source.replace(/@COUNT@/g,countEdu));
        $('.school').autocomplete({
            source: "school.php"
        });

    });

    $('.school').autocomplete({
        source: "school.php"
    });

});
</script>
<script id="edu-template" type="text">
<div id="edu@COUNT@">
    <p>Year: <input type="text" name="edu_year@COUNT@" value="" />
    <input type="button" value="-" onclick="$('#edu@COUNT@').remove();return false;"><br>
    <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value="" />
    </p>
  </div>
</script>




</div>
</body>
</html>
