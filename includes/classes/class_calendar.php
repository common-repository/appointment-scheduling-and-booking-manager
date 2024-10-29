<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class booking_diary {

public $time_slot	= 1; 

public $booking_frequency ; 			// The slot frequency per hour, expressed in minutes.  	

// Day Related Variables

public $day_format					= 2;				// Day format of the table header.  Possible values (1, 2, 3)   
															// 1 = Show First digit, eg: "M"
															// 2 = Show First 3 letters, eg: "Mon"
															// 3 = Full Day, eg: "Monday"
	
public $day_closed					= array("Saturday", "Sunday"); 	// If you don't want any 'closed' days, remove the day so it becomes: = array();
public $day_closed_text				= "CLOSED"; 		// If you don't want any any 'closed' remove the text so it becomes: = "";

// Cost Related Variables
public $cost_per_slot ;		// The cost per slot
public $cost_currency_tag			= "&dollar;";		// The currency tag in HTML such as &euro; &pound; &yen;


//  DO NOT EDIT BELOW THIS LINE

public $day_order	 				= array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
public $day, $month, $year, $selected_date, $back, $back_month, $back_year, $forward, $forward_month, $forward_year, $bookings, $count, $days, $is_slot_booked_today,$mark;
public $timeslots = array();

/*========================================================================================================================================================*/

public function __construct() { 
       $this->cost_per_slot=get_option( 'eav_slot_price' );
	  $this->booking_frequency=get_option( 'eav_slot_duration' );
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
    
	// $back and $forward are Unix Timestamps of the previous / next month, used to give the back arrow the correct month and year 
    $this->selected_date = $selected_date;       
    $this->back = $back;
    $this->back_month = date("m", $back);
    $this->back_year = date("Y", $back); // Minus one month back arrow
    
    $this->forward = $forward;
    $this->forward_month = date("m", $forward);
    $this->forward_year = date("Y", $forward); // Add one month forward arrow    
    
    // Make the booking array
    $this->make_booking_array($year, $month);
    
}


function make_booking_array($year, $month, $j = 0) {
// Get data from displayed user post......if current= displayed not shown.....	
  global $wpdb;
  global $bp;
  $dispalyed_user_id=$bp->displayed_user->id;
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
     foreach($results_shedule_post as $results_shedule_posts)
       {
  $post_array[ ]='"'.$results_shedule_posts['ID'].'"';
        }

  $in='('.implode(",", $post_array).')';
  $query3='SELECT * FROM wp_postmeta where meta_key= "_slot_start" and post_id IN'.$in;
  $results_schedule_timeslots= $wpdb->get_results($query3,ARRAY_A); 
  $this->timeslots=$results_schedule_timeslots;
  }
	
  $this->make_days_array($year, $month);    
            
  } // Close function

 
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
					"mark"=>'unavailable'
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
			"mark"=>'unavailable'
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
        <tr id='week'>
        <td align='left'><a href='?month=" . date("m", $this->back) . "&amp;year=" . date("Y", $this->back) . "'>&laquo;</a></td>
        <td colspan='5' id='center_date'>" . date("F, Y", $this->selected_date) . "</td>    
        <td align='right'><a href='?month=" . date("m", $this->forward) . "&amp;year=" . date("Y", $this->forward) . "'>&raquo;</a></td>
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
			echo "\r\n<td width='21' valign='top' class='closed' title='The Booking is n/a on this day'>" . $r['daynumber']  . "</td>";		
			 }
		 
		 // If the element is set as 'blank', insert blank day
		if($r['mark']=='unavailable') {		
			echo "\r\n<td width='21' valign='top' class='unavailable'></td>";	
			}
				
				
		// Now check the booking array $this->booking to see whether we have a booking on this day 				
		$current_day = $this->year . '-' . $this->month . '-' . sprintf("%02s", $r['daynumber']);

		if($r['mark']=='red') {
		echo "\r\n<td width='21' valign='top'>
		<p class='fully_booked' title='This day is fully booked'>" . 
		$r['daynumber'] . "</p></td>"; 

		                 } // Close if

		
		if($r['mark']=='green') {
		   echo "\r\n<td width='21' valign='top'>
			<p month='" .  $this->month . "'year='" .  $this->year . "' day='" . sprintf("%02s", $r['daynumber']) . "' class='green green_booking' id='green_booking' title='Please click to view bookings'>" . 
			$r['daynumber'] . "</p></td>";			
		
		}
		
		// The modulus function below ($j % 7 == 0) adds a <tr> tag to every seventh cell + 1;
			if($j % 7 == 0 && $i >1) {
			echo "\r\n</tr>\r\n<tr>"; // Use modulus to give us a <tr> after every seven <td> cells
		}		
		
	}		
		
	echo "</tr></table></div><!-- Close outer_calendar DIV -->";
	$this->basket();
		
	echo "</div><!-- Close LHS DIV -->";
   
   $this->booking_form();
	
} // Close function


function booking_form() {

	echo "
	<div id='outer_booking'><h2>Available Slots</h2>

	<p>
	The following slots are available on <span> " . $this->day . "-" . $this->month . "-" . $this->year . "</span>
	</p>
	
	<table width='400' border='0' cellpadding='2' cellspacing='0' id='booking'>
		<tr id='week_blue'>
			<th width='150' align='left'>Start</th>
			<th width='150' align='left'>End</th>
			<th width='150' align='left'>Price</th>
			<th width='20' align='left'>Book</th>			
		</tr>
		";
				
		
		// Loop through the $slots array and create the booking table
		 $slots=$this->timeslots;
		
		foreach($slots as $i => $start) {	


			// Calculate finish time
			$finish_time = strtotime($start['meta_value']) + $this->booking_frequency * 60; 

			echo "
			<tr>\r\n
				<td>" . $start['meta_value']. "</td>\r\n
				<td>" . date("H:i:s", $finish_time) . "</td>\r\n
				<td>" . $this->cost_currency_tag . number_format($this->cost_per_slot, 2) . "</td>\r\n
				<td width='110'><input data-val='" . $start['meta_value'] . " - " . date("H:i:s", $finish_time) . "' class='fields' type='checkbox' data-id=".$start['post_id']."></td>
			</tr>";
		
		} // Close foreach			
	
		echo "</table></div><!-- Close outer_booking DIV -->";
		

} // Close function


function basket($selected_day = '') {

	echo "<div  id='card_payment' class='card-payment' style='display: none;'>
		<h2>Selected Slots</h2>
		
		<div id='selected_slots'></div>		
	
	
    <div id='paymentSection'>
    <form method='post' id='paymentForm'>
	       <div class='form-group'>
	               <label>Name</label>
					<input name='bookie_username' id='name' type='text' class='text_box'>
</div>
					<div class='form-group'>
					<label>Email</label>
					<input name='email' id='email' type='text' class='text_box'>	
</div>
					<div class='form-group'>
					<label>Phone</label>
					<input name='phone' id='phone' type='text' class='text_box'>	
</div>
					
				
						<div id='outer_price'>
							<div id='currency' style='float:left;width: 222px;'> Amount Need To Pay : " . $this->cost_currency_tag . "
							<div id='total'></div>
							</div>
							
							<input type='hidden' name='price_total' id='total_hidden'  value=''>
							<input type='hidden' name='action' value='ajax_act'>
						</div><br>
          
          <ul id='paymentForm_payment'>
		      <h4> Credit Card Details </h4>
              <input type='hidden' name='card_type' id='card_type' value=''/>
              <li>
                 
                  <input type='text' placeholder='1234 5678 9012 3456' id='card_number' name='card_number' class=''>
  
                  <small class='help'>This demo supports Visa, American Express, Maestro, MasterCard and Discover.</small>              </li>
  
              <li class='vertical'>
                  <ul style='margin-top:0px !important;margin-left:0em !important;width: 97% !important;'>
                      <li style='float:left!important'>
                          <label for='expiry_month'>Expiry month</label>
                          <input type='text' placeholder='MM' maxlength='5' id='expiry_month' name='expiry_month'>
                      </li>
                      <li style='float:left!important'>
                          <label for='expiry_year'>Expiry year</label>
                          <input type='text' placeholder='YYYY' maxlength='5' id='expiry_year' name='expiry_year'>
                      </li>
                      <li style='float:left!important'>
                          <label for='cvv'>CVV</label>
                          <input type='text' placeholder='123' maxlength='3' id='cvv' name='cvv'>
                      </li>
                  </ul>
              </li>
              <li>
                  <label for='name_on_card' style='color: #2D2D2D !important;
    font-weight: bold !important;'>Name on card</label>
                  <input style='width:94% !important; padding:0px 10px !important; margin-top:6px;' type='text' placeholder='xyz' id='name_on_card' name='name_on_card'>
              </li>
              <li><input type='button' name='card_submit' id='cardSubmitBtn' value='Proceed' class='payment-btn' disabled='true' ></li>

          </ul>
		  	    <input type='hidden' name='day_book' value='".$this->day."'>
                     <input type='hidden' name='month_book' value='".$this->month."'>	
                    <input type='hidden' name='year_book' value='".$this->year."'>					 
					<input type='hidden' name='slots_booked_id' id='slots_booked_id'>
					<input type='hidden' name='slots_booked' id='slots_booked'>
					<input type='hidden' name='cost_per_slot' id='cost_per_slot' value='" . $this->cost_per_slot . "'>
					<input type='hidden' name='booking_date' value='" . $this->day . '-' . $this->month. '-' .$this->year. "'>
		  <input type='submit' name='classname' value='Proceed' class='classname' >
      </form>
  </div>
    <div id='orderInfo' style='display: none;'></div>
</div>";

} // Close function

                 
} // Close Class

?>