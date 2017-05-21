<?php
$user = $_COOKIE['login'];
if ($outcome == 'fail') {
    echo '
<!DOCTYPE html>
<html>
<head>
<title>Sorry</title>
        ';
include('../assets/head.php');
    echo "
</head>
<body>
<div>
    <div class='w-section section'>
      <div class='content-mobile-number'><img class='sad-carty' src='../images/sad-10.svg'>
        <div class='login-div alt'><img class='leaf' src='../images/water-12.svg'>
          <div class='sign-in-text'>SORRY</div>
          <div class='shift-info-text'>That booking doesn't work. Please check your details and try again.</div>
          <div class='w-clearfix backhome'>
            <a class='w-inline-block next alt' href= '../index.php?m=".$month."&d=".$day."'>
              <div>Back</div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
    ";
} else if ($outcome == 'success') {
    echo '
<!DOCTYPE html>
<html>
<head>
<title>Booking entered</title>
        ';
include('../assets/head.php');
    echo '
</head>
<body>
<div class="w-section section">
    <div class="content-mobile-number"><img class="carty" src="../images/happy-13.svg">
        <div class="login-div alt"><img class="leaf" src="../images/leaf-11.svg">
            <div class="sign-in-text">THANK YOU</div>
            <div class="shift-info-text">Your booking has been entered</div>
            <div class="backhome">
                <a class="w-inline-block next alt" href="../index.php?m='.$month.'&d='.$day.'">
                    <div>Back</div>
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
        ';
} elseif ($outcome == 'deleted') {
    echo '
<!DOCTYPE html>
<html>
<head>
<title>Booking updated</title>
        ';
include('../assets/head.php');
    echo '
</head>
<body>
<div class="w-section section">
    <div class="content-mobile-number"><img class="carty" src="../images/happy-13.svg">
        <div class="login-div alt"><img class="leaf" src="../images/leaf-11.svg">
            <div class="sign-in-text">DELETED</div>
            <div class="shift-info-text">A volunteer has been deleted</div>
            <div class="backhome">
                <a class="w-inline-block next alt" href="../index.php?m='.$month.'&d='.$day.'">
                    <div>Back</div>
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
    ';
}
?>