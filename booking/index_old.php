<?php
use CartBooking\Application\Calendar;
use CartBooking\Application\ServiceLocator;
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app.php';
$user = $_COOKIE['login'];
if (!$user) {
    header('Location:login.php');
    exit();
}
$publisher = \CartBooking\Application\ServiceLocator::getPioneerRepository()->findById($user);
$first_name = $publisher->getFirstName();
$last_name = $publisher->getLastName();
$gender = $publisher->getGender();
$phone = $publisher->getPhone();
$inactive = $publisher->isInactive();
if ($inactive === "d") {
    header('Location:login.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <?php
    include __DIR__ . '/assets/head.php';
    if (isset($_GET['m'])) {
        $month = $_GET['m'];
        if ($month == "n") {
            $month = date('F', (strtotime('first day of next month')));
            $year = date('Y', (strtotime('first day of next month')));
            if (isset($_GET['d'])) {
                $highlighted = $_GET['d'];
            } else {
                $highlighted = 1;
            }
        } else {
            $month = date('F');
            $year = date('Y');
            if (isset($_GET['d'])) {
                $highlighted = $_GET['d'];
            } else {
                $highlighted = date('j', strtotime('today'));
            }
        }
    } else {
        $month = date('F');
        $year = date('Y');
        if (isset($_GET['d'])) {
            $highlighted = $_GET['d'];
        } else {
            $highlighted = date('j', strtotime('today'));
        }
    }
    $days = date('t', strtotime('1st '.$month.' '.$year.''));
    echo '
	<title>'.$month.' Calendar</title>
	';
    ?>
</head>
<body>
<div class="w-nav nav-global" data-collapse="all" data-animation="default" data-duration="400" data-contain="1">
    <div class="w-container">
        <nav class="w-nav-menu menu-background" role="navigation">
            <a class="w-nav-link nav-link" href="/placements">Record placements</a>
            <a class="w-nav-link nav-link" href="/map">Maps</a>
            <a class="w-nav-link nav-link" href="/statistics">Statistics</a>
            <a class="w-nav-link nav-link" href="/experiences">Experiences</a>
            <a class="w-nav-link nav-link" href="../logout.php">Sign out</a>
        </nav>
        <div class="w-nav-button menu-bt">
            <div class="w-icon-nav-menu"></div>
        </div>
        <?php
        if ($month == date('F')) {
            $new_month = "n";
        } else {
            $new_month = "";
        }
        if ($month == date('F')) {
            echo '
        <img class="arrow-back" width="20" src="images/arrow-right-02.svg">
        <a class="arrow-month" href="index.php?m='.$new_month.'">'.$month.'</a>
        ';
        } else {
            echo '
        <a class="arrow-month" href="index.php?m='.$new_month.'">'.$month.'</a>
        <img class="arrow-back" width="20" src="images/arrow-left-02.svg">
        ';
        }
        ?>
    </div>
</div>
<!--  CALENDAR -->
<div class="mobile-section non">
    <div class="w-clearfix content-cal dt">
        <div class="calendar-date">
            <div class="wekk-day-dt">S</div>
        </div>
        <div class="calendar-date">
            <div class="wekk-day-dt">M</div>
        </div>
        <div class="calendar-date">
            <div class="wekk-day-dt">T</div>
        </div>
        <div class="calendar-date">
            <div class="wekk-day-dt">W</div>
        </div>
        <div class="calendar-date">
            <div class="wekk-day-dt">T</div>
        </div>
        <div class="calendar-date">
            <div class="wekk-day-dt">F</div>
        </div>
        <div class="calendar-date">
            <div class="wekk-day-dt">S</div>
        </div>
    </div>
    <?php
    $result = 0;
    $firstDayOfTheMonth = (int)date('w', strtotime('1st '.$month.' '.$year.''));
    while ($result <= $days) {
        ?>
        <div class="w-clearfix content-cal dt">
            <?php
            for ($i = 0; $i < 7; ++$i) {
                if ($result === 0 && $firstDayOfTheMonth === $i) {
                    $result = 1;
                }
                ?>
                <div class="calendar-date">
                    <?php
                    if ($result > 0) {
                        $date = date('Y-m-d', (strtotime("$result $month $year")));
                        $output_class = Calendar::generateOutputClass($highlighted, $result, $month);
                        $rand = 10;
                        echo '
        
            <a id="'.$rand.''.$result.'" href="/?select_date='.strtotime("$result $month $year").'" class="'.$output_class.'" data-ix="pull-menu">
                '.$result.'
            </a>
      ';
                        $my_shift = 0;
                        $reminder = 0;
                        $completed = 0;
                        foreach (ServiceLocator::getBookingRepository()->findByPublisherIdAndDate($user, new DateTimeImmutable($date)) as $booking) {
                            if ($booking->getShiftId()) {
                                $my_shift++;
                                if (!$booking->isRecorded() && $booking->isConfirmed() && ($booking->getDate()->getTimestamp() < strtotime('today'))) {
                                    $reminder++;
                                } elseif ($booking->isRecorded()) {
                                    $completed++;
                                }
                            }
                        }
                        if ($reminder > 0) {
                            echo '
            <div class="myshift placements-reminder"></div>
            ';
                        } elseif ($my_shift > $completed) {
                            echo '
            <div class="myshift"></div>
            ';
                        } elseif ($my_shift) {
                            echo '
            <div class="myshift placements-recorded"></div>
            ';
                        }
                        $result++;
                        $output_class = null;
                    }
                    ?>
                </div>

                <?php
            }
            ?>
        </div>
        <?php
    }
    ?>
</div>
<!--END CALENDAR-->
<div class="w-section content-section no-par" id="content">
    <!--    CARDS  -->
    <div class="w-container" id="cards-data">
        <?php
        if (isset($_GET['select_date'])) {
            $select_date = $_GET['select_date'];
        } else {
            $select_date = strtotime(''.$month.' '.$highlighted.', '.$year.'');
        }
        $date = date('Y-m-d', $select_date);
        $display_date = date('j F', $select_date);
        $day_of_week = date('w', $select_date);
        $today = date('Y-m-d', strtotime('today'));
        ?>
        <h1 class="date-show"><span class="date-color"><?= date('j', $select_date); ?> </span><?= date('F', $select_date); ?></h1>
        <div class="div-movile-cards-container">
            <?php
            $card = 0;
            foreach (\CartBooking\Application\ServiceLocator::getLocationRepository()->findAll() as $location) {
                //this is where shifts can be filtered
                ?>
                <div class="acc-sectionwrapper" id="<?=$location->getId()?>">
                    <div class="acc-header">
                        <div class="locatin-heading">
                            <div><?=$location->getName()?></div>
                        </div>
                        <?php
                        foreach (\CartBooking\Application\ServiceLocator::getShiftRepository()->findByDayAndLocation($day_of_week, $location->getId()) as $shift) {
                            $overseer_id = null;
                            $overseer_first_name = null;
                            $overseer_last_name = null;
                            $overseer_phone = null;
                            $pioneer_id = null;
                            $pioneer_first_name = null;
                            $pioneer_last_name = null;
                            $pioneer_gender = null;
                            $pioneer_phone = null;
                            $pioneer_b_id = null;
                            $pioneer_b_first_name = null;
                            $pioneer_b_last_name = null;
                            $pioneer_b_gender = null;
                            $pioneer_b_phone = null;
                            $my_booking = null;
                            $booking = \CartBooking\Application\ServiceLocator::getBookingRepository()->findByShiftAndDate($shift->getId(), new DateTimeImmutable($date));
                            $overseer_id = $pioneer_id = $pioneer_b_id = $confirmed = $recorded = null;
                            $start_time = $shift->getStartTime();
                            $shift_id = $shift->getId();
                            if ($booking !== null || $date >= $today) {
                                if ($booking !== null) {
                                    $overseer_id = $booking->getOverseerId();
                                    $pioneer_id = $booking->getPioneerId();
                                    $pioneer_b_id = $booking->getPioneerBId();
                                    $confirmed = $booking->isConfirmed();
                                    $recorded = $booking->isRecorded();
                                }
                                $admin_phone = "0417400009";
                                $available = 0;
                                $time = date('g:ia', strtotime($start_time));
                                $cancel_time = strtotime($date . ", " . $time . " -24 hours");
                                $var = 20;
                                $imgVar = 30;
                                $my_booking = 'n';
                                echo '
<div class="div-time">
    <a class="w-clearfix w-inline-block user-link" data-ix="accordion" onclick="accordion('.$var.''.$shift_id.'); changeImage('.$imgVar.''.$shift_id.')";>
        <div class="tim-div">
          <div class="time-of-day">'.$time.'</div>
        </div>
        <div class="user-div">
';
                                if ($overseer_id > 0) {
                                    $overseer = \CartBooking\Application\ServiceLocator::getPioneerRepository()->findById($overseer_id);
                                    $overseer_first_name = $overseer->getFirstName();
                                    $overseer_last_name = $overseer->getLastName();
                                    $overseer_gender = $overseer->getGender();
                                    $overseer_phone = $overseer->getPhone();
                                    if ($overseer_id == $user) {
                                        $my_booking = 'y';
                                        echo '
            <div class="my-shift-dot"></div>
            ';
                                    }
                                    echo '
            <img class="male" width="32" src="/images/male-02.svg">
        ';
                                } else {
                                    echo '
            <img class="empty" width="32" src="/images/empty-02.svg">
        ';
                                }
                                echo '
        </div>
        <div class="user-div">
    ';
                                if ($pioneer_id > 0) {
                                    $pioneer = \CartBooking\Application\ServiceLocator::getPioneerRepository()->findById($pioneer_id);
                                    $pioneer_first_name = $pioneer->getFirstName();
                                    $pioneer_last_name = $pioneer->getLastName();
                                    $pioneer_gender = $pioneer->getGender();
                                    $pioneer_phone = $pioneer->getPhone();
                                    if ($pioneer_id == $user) {
                                        $my_booking = 'y';
                                        echo '
            <div class="my-shift-dot"></div>
            ';
                                    }
                                    if ($pioneer_gender == 'm') {
                                        echo '
            <img class="male" width="32" src="/images/male-02.svg">
            ';
                                    } else {
                                        echo '
            <img class="female" width="32" src="/images/female-02.svg">
            ';
                                    }
                                } else {
                                    echo '
            <img class="empty" width="32" src="/images/empty-02.svg">
        ';
                                }
                                echo '
        </div>
        <div class="user-div">
    ';
                                if ($pioneer_b_id > 0) {
                                    $pioneer = \CartBooking\Application\ServiceLocator::getPioneerRepository()->findById($pioneer_b_id);
                                    $pioneer_b_first_name = $pioneer->getFirstName();
                                    $pioneer_b_last_name = $pioneer->getLastName();
                                    $pioneer_b_gender = $pioneer->getGender();
                                    $pioneer_b_phone = $pioneer->getPhone();
                                    if ($pioneer_b_id == $user) {
                                        $my_booking = 'y';
                                        echo '
            <div class="my-shift-dot"></div>
            ';
                                    }
                                    if ($pioneer_b_gender === 'm') {
                                        echo '
            <img class="male" width="32" src="/images/male-02.svg">
            ';
                                    } else {
                                        echo '
            <img class="female" width="32" src="/images/female-02.svg">
            ';
                                    }
                                } else {
                                    echo '
            <img class="empty" width="32" src="/images/empty-02.svg">
        ';
                                }
                                echo '
        </div>
        <div class="user-div">
    ';
                                if ($confirmed) {
                                    echo '
            <img id="'.$imgVar.''.$shift_id.'" width="32" alt="confirmed" src="/images/confirmed-02.svg">
        ';
                                } else {
                                    echo '
            <img id="'.$imgVar.''.$shift_id.'" width="32" alt="unconfirmed" src="/images/unconfirmed-02.svg">
        ';
                                }
                                echo '
        </div>
    </a>
    ';

                                $filled = 0;
                                echo '
    <div id="'.$var.''.$shift_id.'" class="w-form form-confirm">
        <div class="heightWrapper" id = "heightWrapper'.$var.''.$shift_id.'">
            <form class="form" method="post" action="assets/booking_result.php" id="form'.$shift_id.'" name="'.$shift_id.'">
    ';
                                if ($overseer_id > 0) {
                                    echo '
                <div class="user-details">
                    <div class="user-name">'.$overseer_first_name.' '.$overseer_last_name.'</div>
                    <div class="mobile"><a href="tel:'.$overseer_phone.'">'.$overseer_phone.'</a></div>
        ';
                                    if ((($user == 1 or $user == 2) and (strtotime($date . ', ' . $time) >= time())) or ($my_booking == 'y' and (time() < $cancel_time))) {
                                        echo '
                    <input type="hidden" name="shift" value="'.$shift_id.'" />
                    <input type="hidden" name="date" value="'.$date.'" />
                    <button class="w-inline-block delete-shift" type="submit" name="delete_overseer" value="delete_overseer">
                        <div>Delete</div>
                    </button>
            ';
                                    } elseif (time() < strtotime($date . ', ' . $time) and time() >= $cancel_time and $overseer_id == $user and (date('H:m') < "22:00" and date('H:m') > "07:00")) {
                                        echo '
                    <a href="tel:'.$admin_phone.'">
                        <div class="w-inline-block delete-shift"> 
                            Call to change
                        </div>
                    </a>
            ';
                                    } elseif (time() < strtotime($date . ', ' . $time) and time() >= $cancel_time and $overseer_id == $user and (date('H:m') > "22:00" or date('H:m') < "07:00")) {
                                        echo '
                    <a href="mailto:support@zion.dev?cc=sydmwp@gmail.com&subject=Shift%20Change%20Needed&body=%0A%0A%2A%2A%2A%2AType%20your%20message%20above%20this%20line%2A%2A%2A%2A%0AShift%20ID:%20'.$shift_id.'%0ADate:%20'.$date.'%0AUser%20ID:%20'.$user.'">
                        <div class="w-inline-block delete-shift">
                            Email to change
                        </div>
                    </a>
            ';
                                    }
                                    echo '
                </div>
        ';
                                    $filled++;
                                } else {
                                    $available++;
                                }
                                if ($pioneer_id > 0) {
                                    echo '
                <div class="user-details">
                    <div class="user-name">'.$pioneer_first_name.' '.$pioneer_last_name.'</div>
                    <div class="mobile"><a href="tel:'.$pioneer_phone.'">'.$pioneer_phone.'</a></div>
        ';
                                    if ((($user == 1 or $user == 2) and (strtotime($date . ',' . $time) >= time())) or ($my_booking == 'y' and (time() < $cancel_time))) {
                                        echo '
                    <input type="hidden" name="shift" value="'.$shift_id.'" />
                    <input type="hidden" name="date" value="'.$date.'" />
                    <button class="w-inline-block delete-shift" type="submit" name="delete_pioneer" value="delete_pioneer">
                        <div>Delete</div>
                    </button>
            ';
                                    } elseif (time() < strtotime($date . ', ' . $time) and time() >= $cancel_time and $pioneer_id == $user and (date('H:m') < "22:00" and date('H:m') > "07:00")) {
                                        echo '
                    <a href="tel:'.$admin_phone.'">
                        <div class="w-inline-block delete-shift"> 
                            Call to change
                        </div>
                    </a>
            ';
                                    } elseif (time() < strtotime($date . ', ' . $time) and time() >= $cancel_time and $pioneer_id == $user and (date('H:m') > "22:00" or date('H:m') < "07:00")) {
                                        echo '
                    <a href="mailto:support@zion.dev?cc=sydmwp@gmail.com&subject=Shift%20Change%20Needed&body=%0A%0A%2A%2A%2A%2AType%20your%20message%20above%20this%20line%2A%2A%2A%2A%0AShift%20ID:%20'.$shift_id.'%0ADate:%20'.$date.'%0AUser%20ID:%20'.$user.'">
                        <div class="w-inline-block delete-shift">
                            Email to change
                        </div>
                    </a>
            ';
                                    }
                                    echo '
                </div>
        ';
                                    $filled++;
                                } else {
                                    $available++;
                                }
                                if ($pioneer_b_id > 0) {
                                    echo '
                <div class="user-details">
                    <div class="user-name">'.$pioneer_b_first_name.' '.$pioneer_b_last_name.'</div>
                    <div class="mobile"><a href="tel:'.$pioneer_b_phone.'">'.$pioneer_b_phone.'</a></div>
        ';
                                    if ((($user == 1 or $user == 2) and (strtotime($date . ',' . $time) >= time())) or ($my_booking == 'y' and (time() < $cancel_time))) {
                                        echo '
                    <input type="hidden" name="shift" value="'.$shift_id.'" />
                    <input type="hidden" name="date" value="'.$date.'" />
                    <button class="w-inline-block delete-shift" type="submit" name="delete_pioneer_b" value="delete_pioneer_b">
                        <div>Delete</div>
                    </button>
            ';
                                    } elseif (time() < strtotime($date . ', ' . $time) and time() >= $cancel_time and $pioneer_b_id == $user and (date('H:m') < "22:00" and date('H:m') > "07:00")) {
                                        echo '
                    <a href="tel:'.$admin_phone.'">
                        <div class="w-inline-block delete-shift"> 
                            Call to change
                        </div>
                    </a>
            ';
                                    } elseif (time() < strtotime($date . ', ' . $time) and time() >= $cancel_time and $pioneer_b_id == $user and (date('H:m') > "22:00" or date('H:m') < "07:00")) {
                                        echo '
                    <a href="mailto:support@zion.dev?cc=sydmwp@gmail.com&subject=Shift%20Change%20Needed&body=%0A%0A%2A%2A%2A%2AType%20your%20message%20above%20this%20line%2A%2A%2A%2A%0AShift%20ID:%20'.$shift_id.'%0ADate:%20'.$date.'%0AUser%20ID:%20'.$user.'">
                        <div class="w-inline-block delete-shift">
                            Email to change
                        </div>
                    </a>
            ';
                                    }
                                    echo '
                </div>
        ';
                                    $filled++;
                                } else {
                                    $available++;
                                }

                                $spots = 0;
                                if (strtotime($date . ',' . $time) >= time()) {
                                    while ($spots < $available) {
                                        if (($spots + 1) < $available) {
                                            echo '
                    <div class="text-field-div">
                        <div class="label">Add another person?</div>
                        <input class="w-input mobile-text-filled" type="tel" placeholder="Enter mobile number" name="'.$spots.'_volunteer">
                    </div>
                ';
                                            $spots++;
                                        } elseif (($spots + 1) == $available) {
                                            if (($my_booking == 'y') or ($filled > 0 and ($user == 1 or $user == 2))) {
                                                echo '
                    <div class="text-field-div">
                        <div class="label">Add another person?</div>
                        <input class="w-input mobile-text-filled" type="tel" placeholder="Enter mobile number" name="'.$spots.'_volunteer">
                    </div>
                    <input type="hidden" name="shift" value="'.$shift_id.'" />
                    <input type="hidden" name="date" value="'.$date.'" />
                    <button class="w-button submit-mobile-number proceed" type="submit" name="add" value="add">Add</button>
                ';
                                            } else {
                                                echo '
                    <input type="hidden" name="shift" value="'.$shift_id.'" />
                    <input type="hidden" name="date" value="'.$date.'" />
                    <input type="hidden" name="user" value="'.$user.'" />
                    <button class="w-button submit-mobile-number proceed" type="submit" name="add" value="volunteer">Volunteer</button>
                    ';
                                            }
                                            $spots++;
                                        }
                                    }
                                } elseif ($my_booking == 'y' && $confirmed && !$recorded && (strtotime($date . ',' . $time) <= time())) {
                                    $placements = '../placements';
                                    ?>
                                    <a href="/placements" class="w-button submit-mobile-number report" type="submit">Record Placements</a>
                                    <?php
                                }
                                echo '
            </form>
        </div>
    </div>
</div>
    ';
                                $card++;
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
            if ($card < 1) {
                ?>
                <div class="acc-sectionwrapper">
                    <div class="acc-header">
                        <div class="locatin-heading">
                            <div>No bookings</div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <!--    END CARDS -->
</div>
<script>
    function accordion(str) {
        var grow = document.getElementById(str);
        if (grow.clientHeight == "0") {
            var wrapper = document.getElementById("heightWrapper"+str);
            grow.style.height = wrapper.clientHeight + "px";
            grow.style.borderBottom = "1px solid #ededed";
        } else {
            grow.style.height = 0;
            grow.style.borderBottom = 0;
        }
    };
    function changeImage(str) {
        if (document.getElementById(str).src === "./images/close-02.svg") {
            if (document.getElementById(str).alt === "y") {
                document.getElementById(str).src = "./images/confirmed-02.svg";
            } else {
                document.getElementById(str).src = "./images/unconfirmed-02.svg";
            }
        } else {
            document.getElementById(str).src = "./images/close-02.svg";
        }
    };
</script>
</body>
</html>
