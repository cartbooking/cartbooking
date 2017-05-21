<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app.php';
require_once __DIR__ . '/db.php';

$shift_id = $_POST['shift'];
$date = $_POST['date'];
$month = date('F', strtotime($date));

if ($month == date('F')) {
    
    $month = "";
    
} else {
    
    $month = "n";
    
}

$recorded = 'n';
$day = date('j', strtotime($date));

//get shift details
$shift_info = "SELECT start_time, end_time FROM shifts WHERE id = ?";

$stmt = $con->prepare($shift_info);
  $stmt->bind_param('i', $shift_id);
  $stmt->execute();
  $stmt->bind_result($start_time, $end_time);
  $stmt->fetch();
$stmt->close();

//statements
$pioneer_phone_select = "SELECT id, gender FROM pioneers WHERE phone = ?";
$user_info = "SELECT gender, phone FROM pioneers WHERE id = ?";
$volunteer_info = "SELECT gender FROM pioneers WHERE id = ?";
$shift_volunteers = "SELECT id, overseer_id, pioneer_id, pioneer_b_id, confirmed FROM bookings WHERE shift_id = ? AND date = ?";
$booking_info = "SELECT id FROM bookings WHERE shift_id = ? AND date = ?";
$booking_delete = "DELETE FROM bookings WHERE id = ?";
$booking_update = "UPDATE bookings SET overseer_id = ?, pioneer_id = ?, pioneer_b_id = ?, confirmed = ?, full = ? WHERE id = ?";
$booking_insert = "INSERT INTO bookings " .
  "(shift_id, date, overseer_id, pioneer_id, pioneer_b_id, confirmed, full) VALUES " .
  "(?       , ?   , ?          , ?         , ?           , ?        , ?   )";

$volunteers = 0;

if (!empty($_POST['add'])) {

    if ($_POST['add'] == "volunteer") {
        
        $volunteers++;
        $volunteer_id = $_POST['user'];
        
        $stmt = $con->prepare($user_info);
        $stmt->bind_param('i', $volunteer_id);
        $stmt->execute();
        $stmt->bind_result($volunteer_gender, $volunteer_phone);
        $stmt->fetch();
        $stmt->close();
        
        // test for conflicting shift
        $id = $volunteer_id;
        $existing_shift_test = "SELECT shift_id FROM bookings WHERE date = ? AND (overseer_id = ? OR pioneer_id = ? OR pioneer_b_id = ?)";
        $stmt = $con->prepare($existing_shift_test);
        $stmt->bind_param('siii', $date, $id, $id, $id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($existing_shift);
        while ($stmt->fetch()) {
            $stmt_existing = $con->prepare($shift_info);
            $stmt_existing->bind_param('i', $existing_shift);
            $stmt_existing->execute();
            $stmt_existing->store_result();
            $stmt_existing->bind_result($existing_start_time, $existing_end_time);
            while ($stmt_existing->fetch()) {
                if ( ($start_time == $existing_start_time) OR ($end_time == $existing_end_time) OR (($start_time < $existing_start_time) AND ($existing_start_time < $end_time)) OR (($start_time < $existing_end_time) AND ($existing_end_time < $end_time)) ){
                    $outcome = "fail";
                    require('display_result.php');
                    exit();
                }
            }
            $stmt_existing->free_result();
            $stmt_existing->close();
        }
        $stmt->free_result();
        $stmt->close();
       
    }

    if (!empty($_POST['0_volunteer'])) {

        $volunteer_0_phone = $_POST['0_volunteer'];
        $volunteer_0_phone = str_replace('+61', '0', $volunteer_0_phone);
        $volunteer_0_phone = str_replace(' ', '', $volunteer_0_phone);

        if ($volunteer_0_phone !== $volunteer_phone) {

            $volunteers++;

            $stmt = $con->prepare($pioneer_phone_select);
              $stmt->bind_param('s', $volunteer_0_phone);
              $stmt->execute();
              $stmt->bind_result($volunteer_0_id, $volunteer_0_gender);
              $stmt->fetch();
            $stmt->close();

            if (!$volunteer_0_id) {

                $outcome = "fail";
                require('display_result.php');
                exit();

            }

            // test for conflicting shift
            $id = $volunteer_0_id;
            $existing_shift_test = "SELECT shift_id FROM bookings WHERE date = ? AND (overseer_id = ? OR pioneer_id = ? OR pioneer_b_id = ?)";
            $stmt = $con->prepare($existing_shift_test);
            $stmt->bind_param('siii', $date, $id, $id, $id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($existing_shift);
            while ($stmt->fetch()) {
                $stmt_existing = $con->prepare($shift_info);
                $stmt_existing->bind_param('i', $existing_shift);
                $stmt_existing->execute();
                $stmt_existing->store_result();
                $stmt_existing->bind_result($existing_start_time, $existing_end_time);
                while ($stmt_existing->fetch()) {
                    if ( ($start_time == $existing_start_time) OR ($end_time == $existing_end_time) OR (($start_time < $existing_start_time) AND ($existing_start_time < $end_time)) OR (($start_time < $existing_end_time) AND ($existing_end_time < $end_time)) ){
                        $outcome = "fail";
                        require('display_result.php');
                        exit();
                    }
                }
                $stmt_existing->free_result();
                $stmt_existing->close();
            }
            $stmt->free_result();
            $stmt->close();

        }

    }
    
    if (!empty($_POST['1_volunteer'])) {
        
        $volunteer_1_phone = $_POST['1_volunteer'];
        $volunteer_1_phone = str_replace('+61', '0', $volunteer_1_phone);
        $volunteer_1_phone = str_replace(' ', '', $volunteer_1_phone);
        
        if ($volunteer_1_phone !== $volunteer_phone) {
            
            $volunteers++;
            
            $stmt = $con->prepare($pioneer_phone_select);
              $stmt->bind_param('s', $volunteer_1_phone);
              $stmt->execute();
              $stmt->bind_result($volunteer_1_id, $volunteer_1_gender);
              $stmt->fetch();
            $stmt->close();
            
            if (!$volunteer_1_id) {
                
                $outcome = "fail";
                require('display_result.php');
                exit();
                
            }
            
            // test for conflicting shift
            $id = $volunteer_1_id;
            $existing_shift_test = "SELECT shift_id FROM bookings WHERE date = ? AND (overseer_id = ? OR pioneer_id = ? OR pioneer_b_id = ?)";
            $stmt = $con->prepare($existing_shift_test);
            $stmt->bind_param('siii', $date, $id, $id, $id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($existing_shift);
            while ($stmt->fetch()) {
                $stmt_existing = $con->prepare($shift_info);
                $stmt_existing->bind_param('i', $existing_shift);
                $stmt_existing->execute();
                $stmt_existing->store_result();
                $stmt_existing->bind_result($existing_start_time, $existing_end_time);
                while ($stmt_existing->fetch()) {
                    if ( ($start_time == $existing_start_time) OR ($end_time == $existing_end_time) OR (($start_time < $existing_start_time) AND ($existing_start_time < $end_time)) OR (($start_time < $existing_end_time) AND ($existing_end_time < $end_time)) ){
                        $outcome = "fail";
                        require('display_result.php');
                        exit();
                    }
                }
                $stmt_existing->free_result();
                $stmt_existing->close();
            }
            $stmt->free_result();
            $stmt->close();
            
        }
        
    }

    // confirm spot is available
    $stmt = $con->prepare($shift_volunteers);
    $stmt->bind_param('is', $shift_id, $date);
    $stmt->execute();
    $stmt->bind_result($booking_id, $overseer_id, $pioneer_id, $pioneer_b_id, $confirmed);
    $stmt->fetch();
    $stmt->close();

    if ($booking_id) {
        $spots = 3;
        if ($overseer_id) {
            $spots--;
        }
        if ($pioneer_id) {
            $spots--;
        }
        if ($pioneer_b_id) {
            $spots--;
        }
        if ($volunteers > $spots) {
            $outcome = "fail";
            require('display_result.php');
            exit();
        }
    }

// position user(s) in shift
    if ($volunteer_gender) {
        if ((!$overseer_id or $overseer_id == 0) AND $volunteer_gender == 'm') {
            $overseer_id = $volunteer_id;
        } elseif (!$pioneer_id or $pioneer_id == 0) {
            $pioneer_id = $volunteer_id;
            $pioneer_gender = $volunteer_gender;
        } elseif (!$pioneer_b_id or $pioneer_b_id == 0) {
            $pioneer_b_id = $volunteer_id;
        } else {
            $outcome = "fail";
            require('display_result.php');
            exit();
        }
    }
    if ($volunteer_0_gender) {
        if ((!$overseer_id or $overseer_id == 0) AND $volunteer_0_gender == 'm') {
            $overseer_id = $volunteer_0_id;
        } elseif (!$pioneer_id or $pioneer_id == 0) {
            $pioneer_id = $volunteer_0_id;
            $pioneer_gender = $volunteer_0_gender;
        } elseif (!$pioneer_b_id or $pioneer_b_id == 0) {
            $pioneer_b_id = $volunteer_0_id;
        } else {
            $outcome = "fail";
            require('display_result.php');
            exit();
        }
    }
    if ($volunteer_1_gender) {
        if ((!$overseer_id or $overseer_id == 0) AND $volunteer_1_gender == 'm') {
            $overseer_id = $volunteer_1_id;
        } elseif (!$pioneer_id or $pioneer_id == 0) {
            $pioneer_id = $volunteer_1_id;
            $pioneer_gender = $volunteer_1_gender;
        } elseif (!$pioneer_b_id or $pioneer_b_id == 0) {
            $pioneer_b_id = $volunteer_1_id;
        } else {
            $outcome = "fail";
            require('display_result.php');
            exit();
        }
    }
    $confirmed = "";
    $full = "";
    $couple_check = "SELECT spouse_id FROM pioneers WHERE id = ?";
    if ($overseer_id > 0 AND $pioneer_b_id > 0) {
        $confirmed = 'y';
        $full = 'y';
    } elseif ($overseer_id > 0 AND $pioneer_gender == 'm') {
        $confirmed = 'y';
    } elseif ($overseer_id > 0 AND $pioneer_id > 0) {
        $stmt = $con->prepare($couple_check);
        $stmt->bind_param('i', $overseer_id);
        $stmt->execute();
        $stmt->bind_result($spouse_id);
        $stmt->fetch();
        $stmt->close();
        if ($pioneer_id == $spouse_id) {
            $confirmed = 'y';
        }
    }
    if (!$overseer_id) {
        $overseer_id = '';
    }
    if (!$pioneer_id) {
        $pioneer_id = '';
    }
    if (!$pioneer_b_id) {
        $pioneer_b_id = '';
    }
    
    if ($overseer_id > 0) {
        
        $cc = $overseer_id;
        
    }
    
    if ($pioneer_id > 0) {
        
        $cc0 = $pioneer_id;
    }
    
    if ($pioneer_b_id) {
        
        $cc1 = $pioneer_b_id;
        
    }
    
    if (!$booking_id) {
        
        $stmt = $con->prepare($booking_insert);
          $stmt->bind_param('isiiiss', $shift_id, $date, $overseer_id, $pioneer_id, $pioneer_b_id, $confirmed, $full);
          $stmt->execute();
        $stmt->close();
        $stmt=$con->prepare($booking_info);
          $stmt->bind_param('is', $shift_id, $date);
          $stmt->execute();
          $stmt->bind_result($booking_id);
          $stmt->fetch();
        $stmt->close();
        $type = 'new';
        
    } else {
        
        $stmt = $con->prepare($booking_update);
          $stmt->bind_param('iiissi', $overseer_id, $pioneer_id, $pioneer_b_id, $confirmed, $full, $booking_id);
          $stmt->execute();
        $stmt->close();
        $type = 'updated';
        
    }
    
    $outcome = "success";
    require('display_result.php');
    require('booking_mail.php');
    exit();

}

//delete overseer
if (isset($_POST['delete_overseer'])) {
    
    //get shift details
    $stmt = $con->prepare($shift_volunteers);
      $stmt->bind_param('is', $shift_id, $date);
      $stmt->execute();
      $stmt->bind_result($booking_id, $overseer_id, $pioneer_id, $pioneer_b_id, $confirmed);
      $stmt->fetch();
    $stmt->close();
    
    if ($overseer_id > 0) {
        
        $cc = $overseer_id;
        
    }
    
    if ($pioneer_id > 0) {
        
        $cc0 = $pioneer_id;
        
    }
    
    if ($pioneer_b_id) {
        
        $cc1 = $pioneer_b_id;
        
    }
    
    if (!$pioneer_id || $pioneer_id == 0) {
        
        $stmt = $con->prepare($booking_delete);
          $stmt->bind_param('i', $booking_id);
          $stmt->execute();
        $stmt->close();
        
        $outcome = "deleted";
        $confirmed = "deleted";
        $type = "deleted";
        require('booking_mail.php');
        require('display_result.php');
        exit();
        
    } elseif ($pioneer_id > 0) {
        
        $stmt = $con->prepare($volunteer_info);
          $stmt->bind_param('i', $pioneer_id);
          $stmt->execute();
          $stmt->bind_result($pioneer_gender);
          $stmt->fetch();
        $stmt->close();
        
        if ($pioneer_gender == 'm') {
            
            $overseer_id = $pioneer_id;
            
            if ($pioneer_b_id > 0) {
                
                $pioneer_id = $pioneer_b_id;
                $stmt = $con->prepare($volunteer_info);
                  $stmt->bind_param('i', $pioneer_id);
                  $stmt->execute();
                  $stmt->bind_result($pioneer_gender);
                  $stmt->fetch();
                $stmt->close();
                
                $pioneer_b_id = "";
                
            } else {
                
                $pioneer_id = "";
                $pioneer_gender = "";
                
            }
            
        } elseif ($pioneer_b_id > 0) {
            
             $stmt = $con->prepare($volunteer_info);
              $stmt->bind_param('i', $pioneer_b_id);
              $stmt->execute();
              $stmt->bind_result($pioneer_b_gender);
              $stmt->fetch();
            $stmt->close();
            
            if ($pioneer_b_gender == 'm') {
                
                $overseer_id = $pioneer_b_id;
                $pioneer_b_id = "";
                
            } else {
                
                $overseer_id = "";
                
            }
            
        } else {
            
            $overseer_id = "";
            
        }
        
    }

    $confirmed = "";
    $full = "";
    $couple_check = "SELECT spouse_id FROM pioneers WHERE id = ?";
    if ($overseer_id > 0 AND $pioneer_b_id > 0) {
        $confirmed = 'y';
        $full = 'y';
    } elseif ($overseer_id > 0 AND $pioneer_gender == 'm') {
        $confirmed = 'y';
    } elseif ($overseer_id > 0 AND $pioneer_id > 0) {
        $stmt = $con->prepare($couple_check);
        $stmt->bind_param('i', $overseer_id);
        $stmt->execute();
        $stmt->bind_result($spouse_id);
        $stmt->fetch();
        $stmt->close();
        if ($pioneer_id == $spouse_id) {
            $confirmed = 'y';
        }
    }
    if (!$overseer_id) {
        $overseer_id = '';
    }
    if (!$pioneer_id) {
        $pioneer_id = '';
    }
    if (!$pioneer_b_id) {
        $pioneer_b_id = '';
    }
    
    $stmt = $con->prepare($booking_update);
      $stmt->bind_param('iiissi', $overseer_id, $pioneer_id, $pioneer_b_id, $confirmed, $full, $booking_id);
      $stmt->execute();
    $stmt->close();
    
    $outcome = "deleted";
    $type = "edited";
    require('booking_mail.php');
    require('display_result.php');
    
}

//delete pioneer
if (isset($_POST['delete_pioneer'])) {
    
    //get shift details
    $stmt = $con->prepare($shift_volunteers);
      $stmt->bind_param('is', $shift_id, $date);
      $stmt->execute();
      $stmt->bind_result($booking_id, $overseer_id, $pioneer_id, $pioneer_b_id, $confirmed);
      $stmt->fetch();
    $stmt->close();
    
    if ($overseer_id > 0) {
        
        $cc = $overseer_id;
        
    }
    
    if ($pioneer_id > 0) {
        
        $cc0 = $pioneer_id;
        
    }
    
    if ($pioneer_b_id) {
        
        $cc1 = $pioneer_b_id;
        
    }
    
    if ((!$overseer_id || $overseer_id == 0) AND (!$pioneer_b_id || $pioneer_b_id == 0)) {
        
        $stmt = $con->prepare($booking_delete);
          $stmt->bind_param('i', $booking_id);
          $stmt->execute();
        $stmt->close();
        
        $outcome = "deleted";
        $confirmed = "deleted";
        $type = "deleted";
        require('booking_mail.php');
        require('display_result.php');
        exit();
        
    } elseif ($pioneer_b_id > 0) {
        
        $pioneer_id = $pioneer_b_id;
        $pioneer_b_id = "";
        $stmt = $con->prepare($volunteer_info);
          $stmt->bind_param('i', $pioneer_id);
          $stmt->execute();
          $stmt->bind_result($pioneer_gender);
          $stmt->fetch();
        $stmt->close();
        
    } else {
        
        $pioneer_id = "";
        
    }

    $confirmed = "";
    $full = "";
    $couple_check = "SELECT spouse_id FROM pioneers WHERE id = ?";
    if ($overseer_id > 0 AND $pioneer_b_id > 0) {
        $confirmed = 'y';
        $full = 'y';
    } elseif ($overseer_id > 0 AND $pioneer_gender == 'm') {
        $confirmed = 'y';
    } elseif ($overseer_id > 0 AND $pioneer_id > 0) {
        $stmt = $con->prepare($couple_check);
        $stmt->bind_param('i', $overseer_id);
        $stmt->execute();
        $stmt->bind_result($spouse_id);
        $stmt->fetch();
        $stmt->close();
        if ($pioneer_id == $spouse_id) {
            $confirmed = 'y';
        }
    }
    if (!$overseer_id) {
        $overseer_id = '';
    }
    if (!$pioneer_id) {
        $pioneer_id = '';
    }
    if (!$pioneer_b_id) {
        $pioneer_b_id = '';
    }
    
    $stmt = $con->prepare($booking_update);
      $stmt->bind_param('iiissi', $overseer_id, $pioneer_id, $pioneer_b_id, $confirmed, $full, $booking_id);
      $stmt->execute();
    $stmt->close();
    
    $outcome = "deleted";
    $type = "edited";
    require('booking_mail.php');
    require('display_result.php');
    
}

//delete pioneer_b
if (isset($_POST['delete_pioneer_b'])) {
    
    //get shift details
    $stmt = $con->prepare($shift_volunteers);
      $stmt->bind_param('is', $shift_id, $date);
      $stmt->execute();
      $stmt->bind_result($booking_id, $overseer_id, $pioneer_id, $pioneer_b_id, $confirmed);
      $stmt->fetch();
    $stmt->close();
    
    if ($overseer_id > 0) {
        
        $cc = $overseer_id;
        
    }
    
    if ($pioneer_id > 0) {
        
        $cc0 = $pioneer_id;
        
        $stmt = $con->prepare($volunteer_info);
          $stmt->bind_param('i', $pioneer_id);
          $stmt->execute();
          $stmt->bind_result($pioneer_gender);
          $stmt->fetch();
        $stmt->close();
        
    }
    
    if ($pioneer_b_id) {
        
        $cc1 = $pioneer_b_id;
        
    }
    
    $pioneer_b_id = "";
    $confirmed = "";
    $full = "";
    $couple_check = "SELECT spouse_id FROM pioneers WHERE id = ?";
    if ($overseer_id > 0 AND $pioneer_b_id > 0) {
        $confirmed = 'y';
        $full = 'y';
    } elseif ($overseer_id > 0 AND $pioneer_gender == 'm') {
        $confirmed = 'y';
    } elseif ($overseer_id > 0 AND $pioneer_id > 0) {
        $stmt = $con->prepare($couple_check);
        $stmt->bind_param('i', $overseer_id);
        $stmt->execute();
        $stmt->bind_result($spouse_id);
        $stmt->fetch();
        $stmt->close();
        if ($pioneer_id == $spouse_id) {
            $confirmed = 'y';
        }
    }
    if (!$overseer_id) {
        $overseer_id = '';
    }
    if (!$pioneer_id) {
        $pioneer_id = '';
    }
    if (!$pioneer_b_id) {
        $pioneer_b_id = '';
    }
    
    $stmt = $con->prepare($booking_update);
      $stmt->bind_param('iiissi', $overseer_id, $pioneer_id, $pioneer_b_id, $confirmed, $full, $booking_id);
      $stmt->execute();
    $stmt->close();
    
    $outcome = "deleted";
    $type = "edited";
    require('booking_mail.php');
    require('display_result.php');
    
}
