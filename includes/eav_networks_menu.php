<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
function eav_main_activity() 
{    
  global $bp;
  $dispalyed_user_id=$bp->displayed_user->id;
  $current_user_id=get_current_user_id();
  
if($dispalyed_user_id==$current_user_id)
  {
	$month =date("m");
    $year = date("Y");
    $day = date("d");  
    echo'<div class="result_scheduling">';	
    eav_scheduling_screen_content($day ,$month,$year);
    echo'</div>';
   }
  
if($dispalyed_user_id!=$current_user_id)
 {
   $month =date("m");
   $year = date("Y");
   $day = date("d");      
   eav_booking_screen_content($day ,$month,$year);
  }
}
add_filter( 'bp_profile_header_meta', 'eav_main_activity', 999);


?>