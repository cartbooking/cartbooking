<?php
require_once __DIR__ . '/../vendor/autoload.php';
use CartBooking\Application\ServiceLocator;

echo '
    <a href="../">HOME</a>
    ';
if (isset($_POST['login_button'])) {
    $domain = 'zion.dev';
    $from = 'Metropolitan Witnessing <sydney@zion.dev>';
    $subject = "New 'Report Placements' form";
    $html = "<html>
    <table width='500' style='font-size: 14px; color: #4a4a4a;'>
        <tr>
            <td>
                <p style='font-size: 16px;'>Dear brothers</p>
                <p>In preparation for the new way of reporting, we have updated the placements form on the website. You are now asked to record the following items:</p>
                <ul>
                    <li>Placements - total books, brochures, magazines in hard copy or electronic form</li>
                    <li>Video showings</li>
                    <li>Study requests</li>
                </ul
                <p>Please bear in mind that all shift placements should be recorded, even when placements are zero.</p>
                <p>Thanks for your ongoing support of the program</p>
                <p>Christian love,<br><br><br>Sydney Metropolitan Witnessing</p>
            </td>
        </tr>
    </table>
</html>";
    foreach (ServiceLocator::getPioneerRepository()->findByGender('m') as $pioneer) {
        $to = $pioneer->getFirstName().' '.$pioneer->getLastName().' <'.$pioneer->getEmail().'>';
        $email = ServiceLocator::getEmailMessage();
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setBody($html);

        $result = ServiceLocator::getMailer()->send($email);
        ServiceLocator::getLogger()->info('Email Result: ' . $result);
    }
}
