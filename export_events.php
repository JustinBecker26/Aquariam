<?php
require "dbutil.php";
$db = DbUtil::loginConnection();
$stmt = $db->stmt_init();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $rows = array();
    
    if($stmt->prepare('SELECT event_name, group_size, schedule_date, schedule_time 
    FROM (Events NATURAL JOIN Has NATURAL JOIN Event_Schedule NATURAL JOIN Held_in)
    WHERE exhibit_name = ?') or die(mysqli_error($db))) {
        $stmt->bind_param("s", $_GET['name']);
        $stmt->execute();
        $stmt->bind_result($event_name, $group_size, $schedule_date, $schedule_time);
        while($stmt->fetch()) {
            $rows[] = [
                'event_name'=>$event_name,
                'group_size'=>$group_size,
                'schedule_date'=>$schedule_date,
                'schedule_time'=>$schedule_time
            ];
        }
    }
    
    $textToWrite = "";
    $textToWrite .= '[';
    foreach ($rows as $row) {
        $textToWrite .=  '{';
        foreach ($row as $k => $v) {
            $textToWrite .=  '"'.$k.'": "'.$v.'",';
        }
        $textToWrite = substr($textToWrite, 0, -1);
        $textToWrite .=  '},';
    }
    $textToWrite = substr($textToWrite, 0, -1);
    $textToWrite .=  ']';
    $file = "event-data.json";
    // // file_put_contents($file, $textToWrite);
    header("Content-type: text/plain");
    header("Content-Disposition: attachment; filename={$file}");
    // // readfile($file);
    echo $textToWrite;
    
    $stmt->close(); 
}
$db->close();
?>