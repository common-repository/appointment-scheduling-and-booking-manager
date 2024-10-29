<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
function eav_booking_screen_content($day,$month,$year) { 

include('classes/class_calendar.php');

$calendar = new booking_diary();

echo'<div class="result" >';
if(isset($_GET['month'])) $month = intval($_GET['month']); 

if(isset($_GET['year'])) $year = intval($_GET['year']); 

if(isset($_GET['day'])) $day = intval($_GET['day']); 
// Unix Timestamp of the date a user has clicked on
$selected_date = mktime(0, 0, 0, $month, 01, $year); 

// Unix Timestamp of the previous month which is used to give the back arrow the correct month and year 
$back = strtotime("-1 month", $selected_date); 

// Unix Timestamp of the next month which is used to give the forward arrow the correct month and year 
$forward = strtotime("+1 month", $selected_date);
       
// Call calendar function
$calendar->make_calendar($selected_date, $back, $forward, $day, $month, $year);

echo'</div>';

if(sanitize_text_field($_POST['action'])== 'um_cb')
{
wp_die();
}
}


 ?>


