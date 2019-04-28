<?php
require_once "pdo.php";

function ValidatePos(){
  for($i=0; $i <= 9; $i++){
      if ( ! isset($_POST['year'.$i])) continue;
      if ( ! isset($_POST['desc'.$i])) continue;
      $year = $_POST['year'.$i];
      $desc = $_POST['desc'.$i];

      if(strlen($year) == 0 || strlen($desc) == 0){
        return "All values are required";
      }
      else if( ! is_numeric($year)){
        return "Position year must be numeric";
      }
      else {return true;}
  }
}

function ValidateEdu(){
  for($i=1 ; $i <= 9; $i++){
    if( ! isset($_POST['edu_year'.$i])) continue;
    if( ! isset($_POST['edu_school'.$i])) continue;
    $year = $_POST['edu_year'.$i];
    $school = $_POST['edu_school'.$i];
    if(strlen($year) ==0 || strlen($school) == 0 ){
      return"All values are required";
    }
    if(! is_numeric($year) ){
      return "Education year must be numeric";
    }
  }
  return true;
}

function FlashMessages(){
  if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
  }
  if ( isset($_SESSION['success']) ) {
      echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
      unset($_SESSION['success']);
  }
}


function loadPos($pdo, $profile_id){
  $stmt = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :prof ORDER BY rank');
  $stmt-> execute(array(
    ':prof' => $profile_id
  ));
  $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $positions;
}

function loadEdu($pdo,$profile_id){
  $stmt = $pdo->prepare('SELECT Education.year, Institution.name FROM Education JOIN Institution ON Education.institution_id = Institution.institution_id WHERE profile_id = :pid ORDER BY rank');
  $stmt->execute(array( ':pid' => $profile_id));
  $educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $educations;
}

function ValidateProfile() {
  if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
      return"All fields are required";
  }
  if (strpos($_POST['email'], '@') === false){
    return"Email address must contain @";
  }
  return true;
}
