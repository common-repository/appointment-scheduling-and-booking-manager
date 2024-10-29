<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class scheduling_diary {

public $time_slot	= 1; 
public $slot_starting_time ;
public $slot_end_time ;
public $slot_frequency  ;

// Day Related Variables

public $day_format					= 2;				// Day format of the table header.  Possible values (1, 2, 3)   
															 // 1 = Show First digit, eg: "M"
															// 2 = Show First 3 letters, eg: "Mon"
															// 3 = Full Day, eg: "Monday"
	
public $day_closed					= array("Saturday", "Sunday"); 	// If you don't want any 'closed' days, remove the day so it becomes: = array();
public $day_closed_text				= "CLOSED"; 		// If you don't want any any 'closed' remove the text so it becomes: = "";

// Cost Related Variables
public $cost_per_slot				= 20.50;			// The cost per slot
public $cost_currency_tag			= "&pound;";		// The currency tag in HTML such as &euro; &pound; &yen;


//  DO NOT EDIT BELOW THIS LINE

public $day_order	 				= array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
public $day, $month, $year, $selected_date, $back, $back_month, $back_year, $forward, $forward_month, $forward_year, $bookings, $count, $days, $is_slot_booked_today,$mark;
public $timeslots_schedulded = array(); 
public $timeslot_booked = array();



/*========================================================================================================================================================*/

public function __construct() { 
    $this->slot_starting_time =get_option( 'eav_slot_start','09:00:00');
	$this->slot_end_time =get_option( 'eav_slot_end' ,'21:00:00');
	$this->slot_frequency=get_option( 'eav_slot_duration'  , '60' );
}



function make_calendar($selected_date, $back, $forward, $day, $month, $year) {

    // convert day /month / year to 1 to 01...
	$day = sprintf("%02d", $day);
    $month = sprintf("%02d", $month);
	$year = sprintf("%02d", $year);

    // $day, $month and $year are the $_GET variables in the URL
    $this->day = $day;    
    $this->month = $month;
    $this->year = $year;
	
	// convert day /month / year to 1 to 01...
    
	// $back and $forward are Unix Timestamps of the previous / next month, used to give the back arrow the correct month and year 
    $this->selected_date = $selected_date;       
    $this->back = $back;
    $this->back_month = date("m", $back);
    $this->back_year = date("Y", $back); // Minus one month back arrow
    
    $this->forward = $forward;
    $this->forward_month = date("m", $forward);
    $this->forward_year = date("Y", $forward); // Add one month forward arrow    
    
    // Make the booking array
    $this->make_days_array($year, $month);
	
}


function make_slot_scheduled_array(){
  global $wpdb;
  global $bp;
  $dispalyed_user_id=get_current_user_id();
  $current_date=$this->day . "-" . $this->month . "-" . $this->year ;
  $current_date_string='"'.$current_date.'"';
  $query='SELECT * FROM wp_posts 
           INNER JOIN
           wp_postmeta 
           ON wp_posts.ID = wp_postmeta.post_id 
           where  post_author='.$dispalyed_user_id.' AND 
           post_type="eav_networks" AND post_status="schedule"
           AND wp_postmeta.meta_key="_date" AND meta_value='.$current_date_string;	
 $results_shedule_post = $wpdb->get_results($query,ARRAY_A); 

   if(count($results_shedule_post)>0)
     {
           foreach($results_shedule_post as $results_shedule_posts)
          {
   $post_array[ ]='"'.$results_shedule_posts['ID'].'"';
          }

   $in='('.implode(",", $post_array).')';
   $query3='SELECT * FROM wp_postmeta where meta_key= "_slot_start" and post_id IN'.$in;
   $results_schedule_timeslots= $wpdb->get_results($query3,ARRAY_A); 
   $this->timeslots_schedulded=$results_schedule_timeslots;
    }
}

function make_slot_booked_array(){
  global $wpdb;
  global $bp;
  $dispalyed_user_id=get_current_user_id();
  $current_date=$this->day . "-" . $this->month . "-" . $this->year ;
  $current_date_string='"'.$current_date.'"';	
  $query2='SELECT * FROM wp_posts 
          INNER JOIN
          wp_postmeta 
          ON wp_posts.ID = wp_postmeta.post_id 
          where  post_author='.$dispalyed_user_id.' AND 
          post_type="eav_networks" AND post_status="booked"
          AND wp_postmeta.meta_key="_date" AND meta_value='.$current_date_string;	
   $results_booked_post = $wpdb->get_results($query2,ARRAY_A); 
	
   if(count($results_booked_post)>0)
     {
           foreach($results_booked_post as $results_booked_posts)
          {
   $posted_array[ ]='"'.$results_booked_posts['ID'].'"';
          }

   $iner='('.implode(",", $posted_array).')';
   $query3='SELECT * FROM wp_postmeta where meta_key= "_slot_start" and post_id IN'.$iner;
   $results_booked_timeslots= $wpdb->get_results($query3,ARRAY_A); 
   $this->timeslot_booked=$results_booked_timeslots;
    }
}

function make_days_array($year, $month) { 

    // Calculate the number of days in the selected month                 
    $num_days_month = cal_days_in_month(CAL_GREGORIAN, $month, $year); 
    
    // Make $this->days array containing the Day Number and Day Number in the selected month	   
	
  for ($i = 1; $i <= $num_days_month; $i++) {
	  $i = sprintf("%02d", $i);
	
   // to make marks....
  global $wpdb;
  global $bp;
  $dispalyed_user_id=$bp->displayed_user->id;
  $current_date=$i. "-" . $this->month . "-" . $this->year ;
  $current_date_string='"'.$current_date.'"';
  $query='SELECT * FROM wp_posts 
          INNER JOIN
          wp_postmeta 
          ON wp_posts.ID = wp_postmeta.post_id 
          where  post_author='.$dispalyed_user_id.' AND 
          post_type="eav_networks" AND post_status="schedule"
          AND wp_postmeta.meta_key="_date" AND meta_value='.$current_date_string;	
  $results_shedule_post = $wpdb->get_results($query,ARRAY_A); 
  $query2='SELECT * FROM wp_posts 
           INNER JOIN
            wp_postmeta 
           ON wp_posts.ID = wp_postmeta.post_id 
           where  post_author='.$dispalyed_user_id.' AND 
           post_type="eav_networks" AND post_status="booked"
           AND wp_postmeta.meta_key="_date" AND meta_value='.$current_date_string;	
 $results_booked_post = $wpdb->get_results($query2,ARRAY_A); 
 if(count($results_shedule_post)>0)
   {
	$mark='green';
   }
  elseif((count($results_shedule_post )==0) && ((count($results_booked_post)>0) ))
   {
	$mark='red';
   }

  else
  {
  $mark="closed";
  }
	
		// Work out the Day Name ( Monday, Tuesday... ) from the $month and $year variables....Timestamp to not change for region to region...
        $d = mktime(0, 0, 0, $month, $i, $year); 
		
		// Create the array.....This array contains the clicked data day name as 1,2,3--- with dayname as mon, tue.. 
        $this->days[] = array("daynumber" => $i, "dayname" => date("l", $d),"mark"=>$mark); 		
    }   

	$this->make_blank_start($year, $month);
	$this->make_blank_end($year, $month);	

} // Close function

function create_time_range($start, $end, $interval = '30 mins', $format = '12') {
    $startTime = strtotime($start); 
    $endTime   = strtotime($end);
    $returnTimeFormat = ($format == '12')?'g:i:s A':'G:i:s';

    $current   = time(); 
    $addTime   = strtotime('+'.$interval, $current); 
    $diff      = $addTime - $current;

    $times = array(); 
    while ($startTime < $endTime) { 
        $times[] = date($returnTimeFormat, $startTime); 
        $startTime += $diff; 
    } 
    $times[] = date($returnTimeFormat, $startTime); 
    return $times; 
}

function make_blank_start($year, $month) {

	/*
	Calendar months start on different days
	Therefore there are often blank 'unavailable' days at the beginning of the month which are showed as a grey block
	The code below creates the blank days at the beginning of the month
	*/	
	
	// Get first record of the days array which will be the First Day in the month ( eg Wednesday ) and also for clicked..
	$first_day = $this->days[0]['dayname'];	$s = 0;
		
		// Loop through $day_order array ( Monday, Tuesday ... )
		foreach($this->day_order as $i => $r) {
		
			// Compare the $first_day to the Day Order
			if($first_day == $r && $s == 0) {
				
				$s = 1;  // Set flag to 1 stop further processing
				
			} elseif($s == 0) {

				$blank = array(
					"daynumber" => 'blank',
					"dayname" => 'blank',
					"mark"=>'unavailable',
					
				);
			
				// Prepend elements to the beginning of the $day array...This makes days array as if moth starts from wed then it add mon , tue as daynumber and dayname as blank....
				array_unshift($this->days, $blank);
			}
			
	} // Close foreach	

} // Close function
	

function make_blank_end($year, $month) {

	/*
	Calendar months start on different days
	Therefore there are often blank 'unavailable' days at the end of the month which are showed as a grey block
	The code below creates the blank days at the end of the month
	*/
	
	// Add blank elements to end of array if required Same as above .Only for last blank.
    $pad_end = 7 - (count($this->days) % 7);

    if ($pad_end < 7) {
	
		$blank = array(
			"daynumber" => 'blank',
			"dayname" => 'blank',
			"mark"=>'unavailable',
			
		);
	
        for ($i = 1; $i <= $pad_end; $i++) {							
			array_push($this->days, $blank);
		}
		
    } // Close if
		
	$this->calendar_top(); 

} // Close function
   
    
function calendar_top() {

	// This function creates the top of the table containg the date and the forward and back arrows 

	echo "
    <div id='lhs'><div id='outer_calendar'>
    <input type='hidden' name='ajax-url' class='ajax-url' value='".admin_url( 'admin-ajax.php' )."'> 

	<table border='0' cellpadding='0' cellspacing='0' id='calendar' class='clndr'>   
<input type='hidden' name='ajax-url' class='posting' value='".eav_networks()->plugin_url."eav_networks.php '> 

		  <input type='hidden' name='ajax-url' class='ajax-url' value='".eav_networks()->plugin_url."eav_networks.php '> 
	   <tr id='week'>
        <td align='left'><a style='float: left;' href='?month=" . date("m", $this->back) . "&amp;year=" . date("Y", $this->back) . "'>&laquo;</a></td>
        <td colspan='5' id='center_date'>" . date("F, Y", $this->selected_date) . "</td>    
        <td align='right'><a style='float: right;' href='?month=" . date("m", $this->forward) . "&amp;year=" . date("Y", $this->forward) . "'>&raquo;</p></td>
    </tr>
    <tr id='week_blue'>";
		
	/*
	Make the table header with the appropriate day of the week using the $day_format variable as user defined above
	Definition:
	
		1: Show First digit, eg: "M"
		2: Show First 3 letters, eg: "Mon"
		3: Full Day, eg: "Monday"		
		
	*/
	
	foreach($this->day_order as $r) {
	
		switch($this->day_format) {
		
			case(1): 	
				echo "<th>" . substr($r, 0, 1) . "</th>";					
			break;
			
			case(2):
				echo "<th>" . substr($r, 0, 3) . "</th>";			
			break;
			
			case(3): 	
				echo "<th>" . $r . "</th>";
			break;
			
		} // Close switch
	
	} // Close foreach

			
	echo "</tr>";   

	$this->make_cells();
    
} // Close function


function make_cells($table = '') {
	
	echo "<tr class='clndr-tr'>";
   
	foreach($this->days as $i => $r) { // Loop through the date array
      
		$j = $i + 1; $tag = 0;	 		

		// If the the current day is found in the day_closed array, bookings are not allowed on this day  
		if($r['mark']=='closed') {	
           echo "\r\n<td width='21' valign='top'>
			<p month='" .  $this->month . "' year='" .  $this->year . "' day= '" . sprintf("%02s", $r['daynumber']) . "' class='closed green_scheduling'  id='green_scheduling' title='The Scheduling is n/a on this day' >" . 
			$r['daynumber'] . "</a></td>";			
			
		}
		// If the element is set as 'blank', insert blank day
		if($r['mark']=='unavailable') {		
			echo "\r\n<td width='21' valign='top' class='unavailable'></td>";	
			
		}
						
		// Now check the booking array $this->booking to see whether we have a booking on this day 				
		$current_day = $this->year . '-' . $this->month . '-' . sprintf("%02s", $r['daynumber']);

		if($r['mark']=='red') {
		   		echo "\r\n<td width='21' valign='top'>
			<p month='" .  $this->month . "' year='" .  $this->year . "' day= '" . sprintf("%02s", $r['daynumber']) . "' class='fully_booked green_scheduling'  id='green_scheduling' title='All schedules are booked by user' >" . 
			$r['daynumber'] . "</a></td>";	
			

		                     } // Close if
		
		if($r['mark']=='green') {
		
			echo "\r\n<td width='21' valign='top'>
			<p month='" .  $this->month . "' year='" .  $this->year . "' day= '" . sprintf("%02s", $r['daynumber']) . "' class='green green_scheduling'  id='green_scheduling' title='schedule the appointments' >" . 
			$r['daynumber'] . "</a></td>";			
		
		}
		
		// The modulus function below ($j % 7 == 0) adds a <tr> tag to every seventh cell + 1;
			if($j % 7 == 0 && $i >1) {
			echo "\r\n</tr>\r\n<tr>"; // Use modulus to give us a <tr> after every seven <td> cells
		}		
		
	}		
		
	echo "</tr></table></div><!-- Close outer_calendar DIV -->";
	
	$this->make_settings();
	$this->makeslots();
} // Close function

function make_settings(){

$user_id=get_current_user_id();
$email=get_user_meta($user_id,'eav_user_email',true);
$paypal_signature=get_user_meta($user_id,'eav_user_paypal_sign' ,true);
$paypal_username=get_user_meta($user_id,'eav_user_paypal_username' ,true);
$paypal_password=get_user_meta($user_id,'eav_user_paypal_paswd' ,true);


echo"<form>
Email: <input type='text' name='comm_email' value=".$email.">
Paypal Username :<input type='text' name='paypal_username' value=".$paypal_username.">
Paypal Password :<input type='text' name='paypal_paswrd' value=".$paypal_password." >
Paypal Signature :<input type='text' name='paypal_signature' value=".$paypal_signature." >
".  wp_nonce_field( 'saving_settings', 'saving_settings_nounce' )
."<input type='submit' name='seetings_submit' value='Setting Saved' class='setting_saved'>
</form>";
}

	
function makeslots(){
	
   echo "
	<div id='outer_scheduling'><h2>Shedule the Slots</h2>

	<p>
	Schedule the slots for date <span> " . $this->day . "-" . $this->month . "-" . $this->year . "</span>
	</p>
	
	<table width='400' border='0' cellpadding='2' cellspacing='0' id='scheduling'>
		<tr class='scheduling_header'>
			<th width='150' align='left'>Start</th>
			<th width='150' align='left'>End</th>
			<th width='150' align='left'>Schedule</th>
			<th width='20' align='left'>Status</th>			
		</tr>
		";

		$current_date=$this->day . "-" . $this->month . "-" . $this->year ;
        $user_id = get_current_user_id();
		$this->make_slot_scheduled_array();
		$this->make_slot_booked_array();
		$slots_schedulde=$this->timeslots_schedulded; 
        $slots_booked=$this->timeslot_booked;
	    $slots= $this->create_time_range($this->slot_starting_time, $this->slot_end_time,$this->slot_frequency.'mins','24');

	 foreach($slots as $slot)
	  { 
    	  $tag=0; 
	    $finish_time = strtotime($slot) + $this->slot_frequency * 60;
		  echo '<tr class="scheduling_row"><td class="scheduling_start">'.$slot.'</td>
		        <td class="scheduling_end">'.date("H:i:s", $finish_time).'</td>';
		  foreach($slots_schedulde as $slots_schedulded)
		  {   
			  if($slot==$slots_schedulded['meta_value']){
				  $tag=1;
				  echo'<td class="scheduling_control"><input type="checkbox" value="yes" checked="yes" day="'.$this->day.'" month="'.$this->month.'" year="'.$this->year.'" class="slot_scheduling" data-refrence="'.$slot.'" data-id="'.$slots_schedulded['post_id']. '"></td>';
				  echo'<td class="scheduling_status"><p id="'.$slot.'">';
				  echo'schedulded';}
		  }
		  foreach($slots_booked as $slots_bookeded)
		  {
			if($slot==$slots_bookeded['meta_value']){
				$tag=1;
				echo'<td class="scheduling_control"></td>';
				echo'<td class="scheduling_status"><p id="'.$slot.'">';
				echo'booked';
				}  
		  }
		  if($tag==0)
		  {
		     echo'<td class="scheduling_control"><input u_id="'.$user_id.'" date="'.$current_date.'" day="'.$this->day.'" month="'.$this->month.'" year="'.$this->year.'" type="checkbox"  class="slot_scheduling" slot_start="'.$slot.'"></td>';
		     echo'<td class="scheduling_status"><p id="'.$slot.'">';
			 echo'Not schedulded Yet';  
			  
		  }
		  echo'</p></td></tr>';
	  }
	  echo'</table>';
	}
} // Close Class

?>