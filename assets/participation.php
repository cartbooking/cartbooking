<!DOCTYPE html>
<html>
    <head>
        <title>Participation</title>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js"></script>
        <script>
            WebFont.load({
              google: {
                families: ["Roboto:100,100italic,300,300italic,regular,italic,500,500italic,700,700italic","Roboto Slab:100,300,regular,700"]
              }
            });
        </script>
        <link rel="shortcut icon" type="image/x-icon" href="../images/metro-favicon.png">
        <link rel="apple-touch-icon" href="../images/metropolitan.png">
        <link rel="stylesheet" type="text/css" href="../css/normalize.css">
        <style type="text/css">
            * {
                box-sizing: border-box; 
            }
            body {
                font-family: Roboto, sans-serif; color: #333; font-size: 14px; line-height: 20px; font-weight: 400; -webkit-font-smoothing: antialiased; padding-bottom: 10px; max-width: 600px; margin-left: auto; margin-right: auto; background-color: #efefef; overflow: auto;
            }
            a {
                text-decoration: none;
            }
            .name-heading  {
                width: 45%; height: 40px; padding-top: 5px; padding-left: 5px; font-size: 20px; float: left; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipses;
            }
            .name {
                width: 45%; height: 30px; padding-top: 5px; padding-left: 5px; background-color: #fafafa; border-bottom: 1px solid #efefef; float: left; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipses; color: black;
            }
            .chart {
                margin-right: 7px; margin-left: 7px; font-size: 14px; padding-top: 4px; padding-bottom: 3px; text-align: center; border-radius: 4px; border: 1px solid #eb5424; color: #eb5424;
            }
            .month-heading {
                width: 11%; height: 40px; padding-top: 10px; font-size: 18px; float: left; display: block; text-align: center;
            }
            .month {
                width: 11%; height: 30px; padding-top: 5px; background-color: #fafafa; border-bottom: 1px solid #efefef; float: left; display: block; text-align: center;
            }
            .top {
                position: fixed; z-index: 3; width: 100%; height: 40px; background-color: #efefef; max-width: 600px; margin-left: auto; margin-right: auto;
            }
            .month-hidden {
                width: 11%; height: 40px; padding-top: 10px; font-size: 18px; float: left; display: block; text-align: center; color: #efefef;
            }
            .block {
                width: 100%; height: 40px; display: block; max-width: 600px;
            }
            .space {
                width: 100%; height: 30px; padding-top: 5px; padding-left: 2px; font-size: 20px; color: #fff; background-color: #a0a0a0; float: left; display: block; max-width: 600px;
            }
            .hidden {
                width: 100%; height: 100%; display: none; position: absolute; z-index: 2; padding: 15px;
            }
            .display {
                width: 100%; height: 100%; position: fixed; z-index: 2; background-color: #fafafa; padding: 15px; max-width: 600px; margin-left: auto; margin-right: auto;
            }
            .graphic {
                height: 85%; margin-left: auto; margin-right: auto; border: 1px solid #efefef; padding-left: 20%; padding-right: 20%; padding-top: 15px; padding-bottom: 15px; position: relative; text-align: center;
            }
        </style>
    </head>
    <body>
<?php
$pioneer_details = "SELECT id, first_name, last_name from pioneers WHERE inactive != ? and inactive != ? ORDER BY last_name, first_name";
$month_bookings = "SELECT id FROM bookings WHERE (overseer_id = ? OR pioneer_id = ? OR pioneer_b_id = ?) AND date >= ? AND date < ?";
$month = date('n');
$year = date('Y');
if ($month >= 3) {
    $month = $month - 2;
} else {
    $month = $month + 10;
    $year = $year - 1;
}
?>
        <div class="top" id="top">
            <div class="name-heading">
                <a href = "#" onclick="myFunction();">
                    <div class="chart" id="button" >
                        Show Chart
                    </div>
                </a>
            </div>
            <div class="month-heading" id="one">
<?php
echo date('M', strtotime('1-'.$month.'-'.$year.''));
$month++;
if ($month > 12) {
    $month = 1;
    $year++;
}
?>
            </div>
            <div class="month-heading" id="two">
<?php
echo date('M', strtotime('1-'.$month.'-'.$year.''));
$month++;
if ($month > 12) {
    $month = 1;
    $year++;
}
?>
            </div>
            <div class="month-heading" id="three">
<?php
echo date('M', strtotime('1-'.$month.'-'.$year.''));
$month++;
if ($month > 12) {
    $month = 1;
    $year++;
}
?>
            </div>
            <div class="month-heading" id="four">
<?php
echo date('M', strtotime('1-'.$month.'-'.$year.''));
?>
            </div>
            <div class="month-heading" id="five">
                TOT
            </div>
        </div>
        <div class="block">
        </div>
<?php
$inactive = "y";
$deactivated = "d";
$participants = 0;
$red = 0;
$yellow = 0;
$green = 0;
$stmt_pioneer = $con->prepare($pioneer_details);
  $stmt_pioneer->bind_param('ss', $inactive, $deactivated);
  $stmt_pioneer->execute();
  $stmt_pioneer->store_result();
  $stmt_pioneer->bind_result($id, $first_name, $last_name);
  $sort = '';
  while ($stmt_pioneer->fetch()) {
      $participants++;
      $month = date('n');
      $year = date('Y');
      if ($month >= 3) {
          $month = $month - 2;
      } else {
          $month = $month + 10;
          $year = $year - 1;
      }
      $alpha = substr($last_name, 0, 1);
      if (strcasecmp($alpha, $sort) !== 0) {
          echo '
            <div class="space">
                '.$alpha.'
            </div>
            ';
          $sort = $alpha;
      }
      echo '
        <div class="name">
            '.$first_name.' '.$last_name.'
        </div>
        ';
      $total = 0;
      $n = 1;
      while ($n < 5) {
          $bookings = 0;
          if ($month < 12) {
              $next_month = $month + 1;
              $next_year = $year;
          } else {
              $next_month = 1;
              $next_year = $year + 1;
          }
          $start_date = date('Y-m-d', strtotime('1-'.$month.'-'.$year.''));
          $end_date = date('Y-m-d', strtotime('1-'.$next_month.'-'.$next_year.''));
          $stmt_bookings=$con->prepare($month_bookings);
            $stmt_bookings->bind_param('iiiss', $id, $id, $id, $start_date, $end_date);
            $stmt_bookings->execute();
            while ($stmt_bookings->fetch()) {
                $bookings++;
            }
          $stmt_bookings->close();
          if ($bookings == 0) {
              $textcolour = "#eb5424";
          } else {
              $textcolour = "black";
          }
          echo '
            <div class="month" style="color: '.$textcolour.';">
                '.$bookings.'
            </div>
            ';
          $total = $bookings + $total;
          $n++;
          $month = $next_month;
          $year = $next_year;
      }
      if ($total == 0) {
          $bgcolour = "#eb5424";
          $textcolour = "white";
          $red++;
      } elseif ($total < 5) {
          $bgcolour = "#ffe80f";
          $textcolour = "black";
          $yellow++;
      } else {
          $bgcolour = "#0bce88";
          $textcolour = "black";
          $green++;
      }
      echo '
        <div class="month" style="background-color: '.$bgcolour.'; color: '.$textcolour.';">
            '.$total.'
        </div>
        ';
  }
  $stmt_pioneer->free_result();
$stmt_pioneer->close();
$low = $red / $participants * 100;
$low = 100 - $low;
$mid = $yellow / $participants * 100;
$mid = 100 - $mid;
$high = $green / $participants * 100;
$high = 100 - $high;
?>
        <div class="hidden" id="hideShow">
            <div style="width: 100%; height: 30px; text-align: center; margin-top: 35px; font-size: 18px;">
<?php
echo "
                Participants ($participants)
    ";
?>
            </div>
            <div class="graphic">
<?php
echo '
                <div style="width: 33.33%; float: left; height: 100%; background-color: #eb5424;">
                    <div style="width: 100%; height: '.$low.'%; background-color: #fafafa; padding-top: 50px;">
                        Zero<br>'.$red.'
                    </div>
                </div>
                <div style="width: 33.33%; float: left; height: 100%; background-color: #ffe80f;">
                    <div style="width: 100%; height: '.$mid.'%; background-color: #fafafa; padding-top: 50px;">
                        < 5<br>'.$yellow.'
                    </div>
                </div>
                <div style="width: 33.33%; float: left; height: 100%; background-color: #0bce88;">
                    <div style="width: 100%; height: '.$high.'%; background-color: #fafafa; padding-top: 50px;">
                        5+<br>'.$green.'
                    </div>
                </div>
    ';
?>
            </div>

        </div>
        <div style="clear:both"></div>
        <script>
        function myFunction() {
//            alert("onclick works");
            if (document.getElementById("hideShow").className == "hidden") {
                document.getElementById("hideShow").className = "display";
                document.getElementById("button").innerHTML = "Close Chart";
                document.getElementById("one").className = "month-hidden";
                document.getElementById("two").className = "month-hidden";
                document.getElementById("three").className = "month-hidden";
                document.getElementById("four").className = "month-hidden";
                document.getElementById("five").className = "month-hidden";
            } else {
                document.getElementById("hideShow").className = "hidden";
                document.getElementById("button").innerHTML = "Show Chart";
                document.getElementById("one").className = "month-heading";
                document.getElementById("two").className = "month-heading";
                document.getElementById("three").className = "month-heading";
                document.getElementById("four").className = "month-heading";
                document.getElementById("five").className = "month-heading";
            }
        };  
        </script>
    </body>
</html>
