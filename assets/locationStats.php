<html>
<head>
    <title>Stats by location</title>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            margin-bottom: 15px;
        }
        .fullwidth {
            float: clear;
            width: 100vw;
            padding: 10px;
            padding-bottom: 0;
            margin-bottom: 10px;
            border-top: 1px solid #ccc;
        }
        .quarterWidth {
            display: inline-block;
            width: 22%;
            margin-left: 2%;
            overflow: auto;
        }
        .month {
            width: 100%;
            padding-top: 5px;
            margin: 0px;
        }
        .label {
            float: left;
            padding-left: 15px;
            width: 40%;
            border-right: 1px solid #ccc;
            text-align: left;
        }
        .stat {
            float: left;
            width: 15%;
            text-align: right;
        }
    
    </style>
</head>
    <body>
<?php


$locName = "SELECT id, name FROM locations WHERE id = ?";
$locShifts = "SELECT id FROM shifts WHERE location_id = ? ORDER BY location_id";
$locBookings = "SELECT date, placements, videos, requests FROM bookings WHERE shift_id = ? ORDER BY date";

$n = 1;

while ($n < 6) {
    
    $augShifts = 0;
    $augPlacements = 0;
    $augVideos = 0;
    $augRequests = 0;
    $sepShifts = 0;
    $sepPlacements = 0;
    $sepVideos = 0;
    $sepRequests = 0;
    $octShifts = 0;
    $octPlacements = 0;
    $octVideos = 0;
    $octRequests = 0;
    $novShifts = 0;
    $novPlacements = 0;
    $novVideos = 0;
    $novRequests = 0;
    
    $stmtName = $con->prepare($locName);
    $stmtName->bind_param('i', $n);
    $stmtName->execute();
    $stmtName->store_result();
    $stmtName->bind_result($loc_id, $loc_name);
    
    while ($stmtName->fetch()) {
        
        $name = $loc_name;
        
        $stmtShift = $con->prepare($locShifts);
        $stmtShift->bind_param('i', $loc_id);
        $stmtShift->execute();
        $stmtShift->store_result();
        $stmtShift->bind_result($shift_id);
        
        while ($stmtShift->fetch()) {
            
            $stmtBookings = $con->prepare($locBookings);
            $stmtBookings->bind_param('i', $shift_id);
            $stmtBookings->execute();
            $stmtBookings->store_result();
            $stmtBookings->bind_result($date, $placements, $videos, $requests);
            
            while ($stmtBookings->fetch()) {
                
                if ($date >= "2016-08-01" and $date <= "2016-08-31") {
                    
                    $augShifts = $augShifts + 1;
                    $augPlacements = $augPlacements + $placements;
                    $augVideos = $augVideos + $videos;
                    $augRequests = $augRequests + $requests;
                    
                } elseif ($date >= "2016-09-01" and $date <= "2016-09-30") {
                    
                    $sepShifts = $sepShifts + 1;
                    $sepPlacements = $sepPlacements + $placements;
                    $sepVideos = $sepVideos + $videos;
                    $sepRequests = $sepRequests + $requests;
                    
                } elseif ($date >= "2016-10-01" and $date <= "2016-10-31") {
                    
                    $octShifts = $octShifts + 1;
                    $octPlacements = $octPlacements + $placements;
                    $octVideos = $octVideos + $videos;
                    $octRequests = $octRequests + $requests;
                    
                } elseif ($date >= "2016-11-01" and $date <= "2016-11-30") {
                    
                    $novShifts = $novShifts + 1;
                    $novPlacements = $novPlacements + $placements;
                    $novVideos = $novVideos + $videos;
                    $novRequests = $novRequests + $requests;
                    
                }
                
            }
            
            $stmtBookings->free_result();
            $stmtBookings->close();
            
        }
        
        $stmtShift->free_result();
        $stmtShift->close();
        
    }
    
    $stmtName->free_result();
    $stmtName->close();
    
    echo "
        <div class='fullWidth'>
            $loc_name
        </div>
        <div class='quarterWidth'>
            <div class='month'>
                AUGUST<br>
                <div class='label'>
                    Shifts
                </div>
                <div class='stat'>
                    $augShifts
                </div>
                <div class='label'>
                    Placements
                </div>
                <div class='stat'>
                    $augPlacements
                </div>
                <div class='label'>
                    Videos
                </div>
                <div class='stat'>
                    $augVideos
                </div>
                <div class='label'>
                    Requests
                </div>
                <div class='stat'>
                    $augRequests
                </div>
            </div>
        </div>
        <div class='quarterWidth'>
            <div class='month'>
                SEPTEMBER<br>
                <div class='label'>
                    Shifts
                </div>
                <div class='stat'>
                    $sepShifts
                </div>
                <div class='label'>
                    Placements
                </div>
                <div class='stat'>
                    $sepPlacements
                </div>
                <div class='label'>
                    Videos
                </div>
                <div class='stat'>
                    $sepVideos
                </div>
                <div class='label'>
                    Requests
                </div>
                <div class='stat'>
                    $sepRequests
                </div>
            </div>
        </div>
        <div class='quarterWidth'>
            <div class='month'>
                OCTOBER<br>
                <div class='label'>
                    Shifts
                </div>
                <div class='stat'>
                    $octShifts
                </div>
                <div class='label'>
                    Placements
                </div>
                <div class='stat'>
                    $octPlacements
                </div>
                <div class='label'>
                    Videos
                </div>
                <div class='stat'>
                    $octVideos
                </div>
                <div class='label'>
                    Requests
                </div>
                <div class='stat'>
                    $octRequests
                </div>
            </div>
        </div>
        <div class='quarterWidth'>
            <div class='month'>
                NOVEMBER<br>
                <div class='label'>
                    Shifts
                </div>
                <div class='stat'>
                    $novShifts
                </div>
                <div class='label'>
                    Placements
                </div>
                <div class='stat'>
                    $novPlacements
                </div>
                <div class='label'>
                    Videos
                </div>
                <div class='stat'>
                    $novVideos
                </div>
                <div class='label'>
                    Requests
                </div>
                <div class='stat'>
                    $novRequests
                </div>
            </div>
        </div>
        ";
    
    $n++;
    
    
}

?>
    </body>
</html>
