<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
//payment	
function eav_booking_update_db_payment() {
if(isset($_POST['slots_booked'])) $slots_booked_id =  sanitize_text_field($_POST['slots_booked_id']);
if(isset($_POST['slots_booked'])) $slots_booked =  sanitize_text_field($_POST['slots_booked']);
if(isset($_POST['username'])) $name =  sanitize_text_field($_POST['username']);
if(isset($_POST['email'])) $email_bookie = sanitize_email( $_POST['email']);
if(isset($_POST['phone'])) $phone = sanitize_text_field( $_POST['phone']);
if(isset($_POST['booking_date'])) $booking_date = sanitize_text_field($_POST['booking_date']);
if(isset($_POST['cost_per_slot'])) $cost_per_slot = sanitize_text_field($_POST['cost_per_slot']);
if(isset($_POST['name_on_card'])) $name_on_card = sanitize_text_field($_POST['name_on_card']);
   
// explode name
   $nameArray = explode(' ',$name_on_card);
    $firstName = $nameArray[0];
    $lastName = $nameArray[1];
    $exp_date = sanitize_text_field($_POST['expiry_month'].$_POST['expiry_year']);
	$account_no =sanitize_text_field(str_replace(" ","",$_POST['card_number']));
	$cvv = sanitize_text_field($_POST['cvv']);
	
	$price_total=$_POST['total_hidden'];
   require_once('paypal/config.php');
 
 $request_params = array
                    (
                    'METHOD' => 'DoDirectPayment', 
                    'USER' => $api_username, 
                    'PWD' => $api_password, 
                    'SIGNATURE' => $api_signature, 
                    'VERSION' => $api_version, 
                    'PAYMENTACTION' => 'Sale',                   
                    'IPADDRESS' => sanitize_text_field($_SERVER['REMOTE_ADDR']),
                    'CREDITCARDTYPE' => $_POST['card_type'],
                    'ACCT' => $account_no ,                   
                    'EXPDATE' => $exp_date ,           
                    'CVV2' => $cvv ,
                    'FIRSTNAME' => $firstName,
                    'LASTNAME' =>  $lastName ,
                    'AMT' => $price_total, 
                    'CURRENCYCODE' => 'USD', 
                    'DESC' => 'Testing Payments Pro'
                    );
					
// Loop through $request_params array to generate the NVP string.
$nvp_string = '';
foreach($request_params as $var=>$val)
{
    $nvp_string .= '&'.$var.'='.urlencode($val);    
}

// Send NVP string to PayPal and store response
$curl = curl_init();
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $api_endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $nvp_string);
 
$result = curl_exec($curl);     

curl_close($curl);

$nvp_response_array = parse_str($result);


$result_array=NVPToArray($result);

$data_bind = array(
    "transaction_id" => $result_array['TRANSACTIONID'],
    "corelation_id"   => $result_array['CORRELATIONID'],
    "success"          => $result_array['ACK']
      );
	  
if ($result_array['ACK']=='Success'){
	
//if payment success
$explode_id_inter=explode('|', $slots_booked_id); 
$explode_id = eav_remove_empty($explode_id_inter);

if(count($explode_id)>0)
{
foreach($explode_id as $exlode_id_results)
{
	global $wpdb;
	$id=$exlode_id_results;
	$query = array(
        'ID' => $id,
        'post_status' => 'booked',
    );
    wp_update_post( $query, true );
} }

//email part
$slots_booked=explode('|', $slots_booked); 
$slots_booked_remove = eav_remove_empty($slots_booked);

foreach($slots_booked_remove as $i => $start) {	

$finish_time = strtotime($start) + get_option( 'eav_slot_duration' ) * 60; 
$timeslot= $start."-".date("H:i:s", $finish_time) ;
$slot_array[ ] = $timeslot;
}
$show_slots = implode(' ' ,$slot_array);

// email to bookie
global $bp;
$displayed_user=bp_get_displayed_user_username();
require 'PHPMailer/PHPMailerAutoload.php';
$mail = new PHPMailer;

$mail->isSMTP();                            // Set mailer to use SMTP
$mail->Host = get_option('eav_smtp_host');           // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                     // Enable SMTP authentication
$mail->Username = get_option('eav_smtp_username');    // SMTP username
$mail->Password = get_option('eav_smtp_password'); // SMTP password
$mail->SMTPSecure = 'tls';                  // Enable TLS encryption, `ssl` also accepted
$mail->Port = get_option('eav_smtp_port');                     // TCP port to connect to

$mail->setFrom(get_option('eav_smtp_email'));
$mail->addAddress($email_bookie);   // Add a recipient


$mail->isHTML(true);  // Set email format to HTML

$bodyContent = "Hello ".  $name." <br>"."Your slots are booked for  the user  ".$displayed_user."  <br><br> Details are as Follows :  <br>  Booking date".  $booking_date."  <br> slot timings are: ".  $show_slots."<br> <br>Payment details : <br> Transaction-id: ".$result_array['TRANSACTIONID']." <br> paid Amount: ".$price_total;

$mail->Subject = "Booking conformation";
$mail->Body    = $bodyContent;
$mail->send();


// email to scheduler
global $bp;
$user_id=$bp->displayed_user->id;
$email_scheduler=get_user_meta($user_id,'eav_user_email',true);
$displayed_user=bp_get_displayed_user_username();
$mail = new PHPMailer;

$mail->isSMTP();                            // Set mailer to use SMTP
$mail->Host = get_option('eav_smtp_host');           // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                     // Enable SMTP authentication
$mail->Username = get_option('eav_smtp_username');    // SMTP username
$mail->Password = get_option('eav_smtp_password'); // SMTP password
$mail->SMTPSecure = 'tls';                  // Enable TLS encryption, `ssl` also accepted
$mail->Port = get_option('eav_smtp_port');                     // TCP port to connect to

$mail->setFrom(get_option('eav_smtp_email'));
$mail->addAddress($email_scheduler);   // Add a recipient


$mail->isHTML(true);  // Set email format to HTML

$bodyContent = "Hello ".  $displayed_user .",<br>"."Your slots are booked by  the user".  $name."<br> <br> Details are as Follows : <br>Booking date".  $booking_date." <br> slot timings are".  $show_slots."<br><br>Bookie Details as follows : <br>Phone no :".  $phone."  <br>Email-id: ".  $email_bookie;

$mail->Subject = "Booking conformation";
$mail->Body    = $bodyContent;
$mail->send();

}
wp_send_json($data_bind);

}
		
function NVPToArray($NVPString)
{
    $proArray = array();
    while(strlen($NVPString))
    {
        // name
        $keypos= strpos($NVPString,'=');
        $keyval = substr($NVPString,0,$keypos);
        // value
        $valuepos = strpos($NVPString,'&') ? strpos($NVPString,'&'): strlen($NVPString);
        $valval = substr($NVPString,$keypos+1,$valuepos-$keypos-1);
        // decoding the respose
        $proArray[$keyval] = urldecode($valval);
        $NVPString = substr($NVPString,$valuepos+1,strlen($NVPString));
    }
    return $proArray;
}

//no payment	
function eav_booking_update_db_no_payment()
{
if(isset($_POST['username'])) $name =  sanitize_text_field($_POST['username']);
if(isset($_POST['slots_booked'])) $slots_booked_id =  sanitize_text_field($_POST['slots_booked_id']);
if(isset($_POST['slots_booked'])) $slots_booked =  sanitize_text_field($_POST['slots_booked']);
if(isset($_POST['email'])) $email = sanitize_email( $_POST['email']);
if(isset($_POST['phone'])) $phone = sanitize_text_field( $_POST['phone']);
if(isset($_POST['booking_date'])) $booking_date = sanitize_text_field($_POST['booking_date']);
if(isset($_POST['cost_per_slot'])) $cost_per_slot = sanitize_text_field($_POST['cost_per_slot']);
 
$explode_id_inter=explode('|', $slots_booked_id); 
$explode_id = eav_remove_empty($explode_id_inter);

if(count($explode_id)>0)
{
foreach($explode_id as $exlode_id_results)
{
	global $wpdb;
	$id=$exlode_id_results;
	$query = array(
        'ID' => $id,
        'post_status' => 'booked',
    );
    wp_update_post( $query, true );
} }

//email part
$slots_booked=explode('|', $slots_booked); 
$slots_booked_remove = eav_remove_empty($slots_booked);

foreach($slots_booked_remove as $i => $start) {	

$finish_time = strtotime($start) + get_option( 'eav_slot_duration' ) * 60; 
$timeslot= $start."-".date("H:i:s", $finish_time) ;
$slot_array[ ] = $timeslot;
}
$show_slots = implode(' ' ,$slot_array);

// email to bookie
global $bp;
$displayed_user=bp_get_displayed_user_username();
require 'PHPMailer/PHPMailerAutoload.php';
$mail = new PHPMailer;

$mail->isSMTP();                            // Set mailer to use SMTP
$mail->Host = get_option('eav_smtp_host');           // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                     // Enable SMTP authentication
$mail->Username = get_option('eav_smtp_username');    // SMTP username
$mail->Password = get_option('eav_smtp_password'); // SMTP password
$mail->SMTPSecure = 'tls';                  // Enable TLS encryption, `ssl` also accepted
$mail->Port = get_option('eav_smtp_port');                     // TCP port to connect to

$mail->setFrom(get_option('eav_smtp_email'));
$mail->addAddress($email);   // Add a recipient


$mail->isHTML(true);  // Set email format to HTML

$bodyContent = "Hello ".  $name." <br>"."Your slots are booked for  the user".  $displayed_user."  <br> Details are as Follows :  <br>  Booking date : ".  $booking_date."  <br> slot timings are : ".  $show_slots;

$mail->Subject = "Booking conformation";
$mail->Body    = $bodyContent;

if(!$mail->send()) {
    echo '<span class="error">Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo.'</span>';
} else {
    echo '<span class="succes">Message has been sent</span>';
}


// email to scheduler
global $bp;
$user_id=$bp->displayed_user->id;
$email_scheduler=get_user_meta($user_id,'eav_user_email',true);
$displayed_user=bp_get_displayed_user_username();
$mail = new PHPMailer;

$mail->isSMTP();                            // Set mailer to use SMTP
$mail->Host = get_option('eav_smtp_host');           // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                     // Enable SMTP authentication
$mail->Username = get_option('eav_smtp_username');    // SMTP username
$mail->Password = get_option('eav_smtp_password'); // SMTP password
$mail->SMTPSecure = 'tls';                  // Enable TLS encryption, `ssl` also accepted
$mail->Port = get_option('eav_smtp_port');                     // TCP port to connect to

$mail->setFrom(get_option('eav_smtp_email'));
$mail->addAddress($email_scheduler);   // Add a recipient


$mail->isHTML(true);  // Set email format to HTML

$bodyContent = "Hello ".  $displayed_user."<br>"."Your slots are booked by  the user".  $name."<br> Details are as Follows : <br>Booking date".  $booking_date." <br> slot timings are".  $show_slots."<br><br>Bookie Details as follows : <br>Phone no :".  $phone."  <br>Email-id: ".  $email;

$mail->Subject = "Booking conformation";
$mail->Body    = $bodyContent;

if(!$mail->send()) {
    echo '<span class="error">Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo.'</span>';
} else {
    echo '<span class="succes">Message has been sent</span>';
}

}

function eav_remove_empty($array) {
  return array_filter($array, '_eav_remove_empty_internal');
}

function _eav_remove_empty_internal($value) {
  return !empty($value) || $value === 0;
}
	
?>