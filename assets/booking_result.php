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
