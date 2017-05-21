<?php
echo '
    <a href="../">HOME</a>
    ';
if (isset($_POST['login_button'])) {
    require_once __DIR__ . '/db.php';
    $records = 0;
    $select_experiences = "SELECT date, overseer_id, comments FROM bookings WHERE date < ? and experience = ? ORDER BY date";
    $archive_experiences = "INSERT INTO experiences " .
      "(date, overseer_id, experience) VALUES " .
      "(?   , ?          , ?         )";
    $this_month = date('n');
    $this_year = date('Y');
    if ($this_month < 3) {
        $archive_month = $this_month + 10;
        $archive_year = $this_year - 1;
    } else {
        $archive_month = $this_month - 2;
        $archive_year = $this_year;
    }
    $archive_date = date('Y-m-d', strtotime('1-'.$archive_month.'-'.$archive_year.''));
    $yes = "y";
    $stmt_select_experiences = $con->prepare($select_experiences);
      $stmt_select_experiences->bind_param('ss', $archive_date, $yes);
      $stmt_select_experiences->execute();
      $stmt_select_experiences->store_result();
      $stmt_select_experiences->bind_result($date, $overseer_id, $experience);
      while ($stmt_select_experiences->fetch()) {
          if ($experience) {
              $stmt_archive_experiences = $con->prepare($archive_experiences);
                $stmt_archive_experiences->bind_param('sis', $date, $overseer_id, $experience);
                $stmt_archive_experiences->execute();
              $stmt_archive_experiences->close();
          }
      }
      $stmt_select_experiences->free_result();
    $stmt_select_experiences->close();
    $select_records = "SELECT date, confirmed, placements, videos, requests FROM bookings WHERE date < ? ORDER BY date DESC";
    $archive_records = "INSERT INTO bookings_archive " .
      "(month, year, confirmed, unconfirmed, placements, videos, requests) VALUES " .
      "(?    , ?   , ?        , ?          , ?         , ?     , ?       )";
    $x = 0;
    $y = $x + 1;
    $stmt_select_records = $con->prepare($select_records);
      $stmt_select_records->bind_param('s', $archive_date);
      $stmt_select_records->execute();
      $stmt_select_records->store_result();
      $stmt_select_records->bind_result($date, $confirmed, $placements, $videos, $requests);
      while ($stmt_select_records->fetch()) {
          $month = date('n', strtotime($date));
          if ($month !== $current_month) {
              if ($x == $y) {
                  $stmt_archive_records = $con->prepare($archive_records);
                    $stmt_archive_records->bind_param('ssiiiii', $month_name, $year, $month_confirmed, $month_unconfirmed, $month_placements, $month_videos, $month_requests);
                    $stmt_archive_records->execute();
                  $stmt_archive_records->close();
                  $y++;
              }
              $month_name = date('F', strtotime($date));
              $year = date('Y', strtotime($date));
              $month_placements = $placements;
              $month_videos = $videos;
              $month_requests = $requests;
              $month_confirmed = 0;
              $month_unconfirmed = 0;
              if ($confirmed == "y") {
                  $month_confirmed++;
              } else {
                  $month_unconfirmed++;
              }
              if ($date) {
                  $records++;
              }
              $current_month = $month;
              $x++;
          } else {
              $month_placements = $month_placements + $placements;
              $month_videos = $month_videos + $videos;
              $month_requests = $month_requests + $requests;
              if ($confirmed == "y") {
                  $month_confirmed++;
              } else {
                  $month_unconfirmed++;
              }
              $records++;
          }
      }
    $stmt_select_records->close();
    $stmt_archive_records = $con->prepare($archive_records);
      $stmt_archive_records->bind_param('ssiiiii', $month_name, $year, $month_confirmed, $month_unconfirmed, $month_placements, $month_videos, $month_requests);
      $stmt_archive_records->execute();
    $stmt_archive_records->close();
    $delete_records = "DELETE FROM bookings WHERE date < ?";
    $stmt_purge = $con->prepare($delete_records);
      $stmt_purge->bind_param('s', $archive_date);
      $stmt_purge->execute();
    $stmt_purge->close();
}
echo '
    <br> Records Archived: '.$records.'
    ';
?>
