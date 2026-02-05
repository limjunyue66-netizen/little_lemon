<?php
date_default_timezone_set('Asia/Kuala_Lumpur'); // Set your timezone

// 1. Generate a random reservation time between 10:00 and 21:00
$openHour = 10;
$closeHour = 21;

$randomHour = rand($openHour, $closeHour);
$randomMinute = rand(0, 59);

$reservationTime = sprintf("%02d:%02d:00", $randomHour, $randomMinute);
echo "Random reservation time: " . $reservationTime . "\n";

// 2. Check if a given time is during open hours (10:00 - 22:00)
function isOpen($time) {
    $open = strtotime("10:00");
    $close = strtotime("22:00");
    $check = strtotime($time);

    return ($check >= $open && $check <= $close);
}

$timeToCheck = "20:30:00";
if (isOpen($timeToCheck)) {
    echo "$timeToCheck is during open hours\n";
} else {
    echo "$timeToCheck is outside open hours\n";
}

// 3. Calculate estimated end dining time (assuming 2 hours dining time)
$startTime = new DateTime("19:30:00");
$startTime->add(new DateInterval('PT2H'));
echo "Estimated dining end time: " . $startTime->format('H:i:s') . "\n";

?>
