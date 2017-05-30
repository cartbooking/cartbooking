<?php
use CartBooking\Application\ServiceLocator;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app.php';
/** @var CartBooking\Lib\Db\Db $con */
$domain = "../";
if ($type == 'new') {
    $type = 'entered';
} elseif ($type = 'updated') {
    $type = 'updated';
} elseif ($type = 'edited') {
    $type = 'edited';
} elseif ($type = 'deleted') {
    $type = 'deleted';
}

$subject = 'Shift '.$type.'';
if ($cc) {
    $recipient = $con->findPioneerEmailInformation($cc);
    $cc_first_name = $recipient->getName()->getFirstName();
    $cc_last_name = $recipient->getName()->getLastName();
    $cc_email = (string)$recipient->getEmail();
}
if ($cc0) {
    $recipient0 = $con->findPioneerEmailInformation($cc0);
    $cc0_first_name = $recipient0->getName()->getFirstName();
    $cc0_last_name = $recipient0->getName()->getLastName();
    $cc0_email = (string)$recipient0->getEmail();
}
if ($cc1) {
    $recipient1 = $con->findPioneerEmailInformation($cc1);
    $cc1_first_name = $recipient1->getName()->getFirstName();
    $cc1_last_name = $recipient1->getName()->getLastName();
    $cc1_email = (string)$recipient1->getEmail();
}
if ($cc) {
    if ($cc_email) {
        $to = $recipient->formatted();
        if ($cc0) {
            if ($cc0_email) {
                $to .= ', ' . $recipient0->formatted();
            }
        }
        if ($cc1) {
            if ($cc1_email) {
                $to .= ', '.$recipient1->formatted();
            }
        }
    }
} elseif ($cc0) {
    if ($cc0_email) {
        $to .= '' . $recipient0->formatted();
    }
    if ($cc1) {
        if ($cc1_email) {
            $to .= ', ' . $recipient1->formatted();
        }
    }
}
//get booking information
$location_info = "SELECT location_id from shifts WHERE id = ?";
$volunteer_info = "SELECT first_name, last_name, phone FROM pioneers WHERE id = ?";
$location_name = "SELECT name FROM locations WHERE id = ?";
$booking_details = "SELECT overseer_id, pioneer_id, pioneer_b_id FROM bookings WHERE id = ?";
$stmt = $con->prepare($location_info);
  $stmt->bind_param('i', $shift_id);
  $stmt->execute();
  $stmt->bind_result($location_id);
  $stmt->fetch();
$stmt->close();
$stmt = $con->prepare($location_name);
  $stmt->bind_param('i', $location_id);
  $stmt->execute();
  $stmt->bind_result($location_name);
  $stmt->fetch();
$stmt->close();
$stmt = $con->prepare($booking_details);
  $stmt->bind_param('i', $booking_id);
  $stmt->execute();
  $stmt->bind_result($overseer_id, $pioneer_id, $pioneer_b_id);
  $stmt->fetch();
$stmt->close();
if ($overseer_id > 0) {
    $stmt = $con->prepare($volunteer_info);
      $stmt->bind_param('i', $overseer_id);
      $stmt->execute();
      $stmt->bind_result($overseer_first_name, $overseer_last_name, $overseer_phone);
      $stmt->fetch();
    $stmt->close();
    $overseer = ''.$overseer_first_name.' '.$overseer_last_name.' (m: '.$overseer_phone.')<br>';
} else {
    $overseer = '';
}
if ($pioneer_id > 0) {
    $stmt = $con->prepare($volunteer_info);
      $stmt->bind_param('i', $pioneer_id);
      $stmt->execute();
      $stmt->bind_result($pioneer_first_name, $pioneer_last_name, $pioneer_phone);
      $stmt->fetch();
    $stmt->close();
    $participants = ''.$pioneer_first_name.' '.$pioneer_last_name.' (m: '.$pioneer_phone.')';
}
if ($pioneer_b_id > 0) {
    $stmt = $con->prepare($volunteer_info);
      $stmt->bind_param('i', $pioneer_b_id);
      $stmt->execute();
      $stmt->bind_result($pioneer_b_first_name, $pioneer_b_last_name, $pioneer_b_phone);
      $stmt->fetch();
    $stmt->close();
    $participants .= '<br>'.$pioneer_b_first_name.' '.$pioneer_b_last_name.' (m: '.$pioneer_b_phone.')';
}
if (!$participants) {
    $participants = '';
}
// the message
$date = date('F jS', strtotime($date));
$start_time = date('g:ia', strtotime($start_time));
if ($confirmed == 'y') {
    $status = 'Confirmed';
} elseif ($confirmed == 'deleted') {
    $status = 'Deleted';
} else {
    $status = 'Unconfirmed';
}
$html = '<html>
            <table width="350" style="border: 1px solid #ededed; padding: 10px;">
                <tr>
                    <td style="font-size: 14px; color: #4a4a4a;">
                        <p>Dear Volunteer</p>
                        <p>Your booking:</p>
                        <p style="font-size: 16px;">'.$location_name.' - '.$date.', '.$start_time.'</p>
                        <p>has been '.$type.' at <a href="./">zion.dev</a></p>
                        <p>'.$overseer.''.$participants.'<br>Status: '.$status.'</p>
                    </td>
                </tr>
            </table>
         </html>';
$email = ServiceLocator::getEmailMessage();
$email->setTo(['serroba@gmail.com']);
$email->setBody($html);
$email->setSubject($subject);

ServiceLocator::getMailer()->send($email);
