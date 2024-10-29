<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    if($_POST['eav_hidden'] == 'Y') {
		
	if ( ! isset( $_POST['save_admin_settings_nounce'] ) || ! wp_verify_nonce( $_POST['save_admin_settings_nounce'], 'save_admin_settings' ) ) {

      print 'Sorry, your nonce did not verify.';
      exit;
     }
	 else{
       
	   //Form data sent
        $slot_start = sanitize_text_field($_POST['eav_slot_start']);
        update_option('eav_slot_start', $slot_start);
         
        $slot_end = sanitize_text_field($_POST['eav_slot_end']);
        update_option('eav_slot_end', $slot_end);
         
        $slot_duration = intval($_POST['eav_slot_duration']);
        update_option('eav_slot_duration', $slot_duration);
         
        $slot_price = intval($_POST['eav_slot_price']);
        update_option('eav_slot_price', $slot_price);
		
		
		 $smtp_host = sanitize_text_field($_POST['eav_smtp_host']);
        update_option('eav_smtp_host', $smtp_host);
         
        $smtp_username = sanitize_text_field($_POST['eav_smtp_username']);
        update_option('eav_smtp_username', $smtp_username);
         
        $smtp_password = sanitize_text_field($_POST['eav_smtp_password']);
        update_option('eav_smtp_password', $smtp_password);
         
        $smtp_email = sanitize_email($_POST['eav_smtp_email']);
        update_option('eav_smtp_email', $smtp_email);     
		
		
		$smtp_port = sanitize_text_field($_POST['eav_smtp_port']);
        update_option('eav_smtp_port', $smtp_port);
		
		$paypal_setting = sanitize_text_field($_POST['eav_paypal_setting']);
        update_option('eav_paypal_setting', $paypal_setting);
         
        ?>
        <div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
        <?php
	 }
	} 
	
	else 
	   {
        //Normal page display
        $slot_start = get_option('eav_slot_start' ,'09:00:00');
        $slot_end = get_option('eav_slot_end' ,'21:00:00');
        $slot_duration = get_option('eav_slot_duration' , '60');
        $slot_price = get_option('eav_slot_price','20');
        $smtp_host = get_option('eav_smtp_host');
		$smtp_username= get_option('eav_smtp_username');
		$smtp_password = get_option('eav_smtp_password');
		$smtp_email =  get_option('eav_smtp_email');
		$smtp_port =  get_option('eav_smtp_port');
		$paypal_setting=  get_option('eav_paypal_setting','TRUE');
    }
	


?>

<div class="wrap">
    <?php    echo "<h2>" . 'Appointment scheduling and Booking Manager settings' . "</h2>"; ?>
     
    <form name="eav_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        
		<input type="hidden" name="eav_hidden" value="Y">
        <table>
		<?php    echo "<h4>" .'Appointment scheduling and Booking Manager Slot Settings'. "</h4>"; ?>
        <tr><td class='frist'><?php _e("Day start Time: " ); ?></td><td class='second'><input type="text" name="eav_slot_start" value="<?php echo esc_attr($slot_start); ?>" size="20"></td><td class='third'><?php _e(" Must be in Format : HH:MM:SS and In 24-hour clock" ); ?></td></tr>
        <tr><td class='frist'><?php _e("Day end Time: " ); ?></td><td class='second'><input type="text" name="eav_slot_end" value="<?php echo esc_attr($slot_end ); ?>" size="20"></td><td class='third'><?php _e(" Must be in Format : HH:MM:SS and In 24-hour clock" ); ?></td></tr>
        <tr><td class='frist'><?php _e("Slot Interval Duration: " ); ?></td><td class='second'><input type="text" name="eav_slot_duration" value="<?php echo esc_attr($slot_duration); ?>" size="20"></td><td class='third'><?php _e(" Must be in Minutes ex: 30 for 30 minutes interval" ); ?></td></tr>
        <tr><td class='frist'><?php _e("Price Per Slot: " ); ?></td><td class='second'><input type="text" name="eav_slot_price" value="<?php echo esc_attr($slot_price); ?>" size="20"></td></tr>
       </table>
	   
	   <?php echo "<h4>" .'Appointment scheduling and Booking Manager Paypal Pro Setting'. "</h4>"; ?>
       <table>
	   	 <tr><td class='frist'><?php _e("Set Paypal to Work: " ); ?></td><td class='second'><select name="eav_paypal_setting" > <option value="TRUE" <?php echo  $paypal_setting == 'TRUE' ? "selected":"" ;?> >Set Test</option><option value="FALSE" <?php echo $paypal_setting == "FALSE" ? "selected":"" ;?> >Set Live</option></select></td><td class='third'><?php _e("Select to switch b/w live-Paypal and test-Sandbox-Paypal" ); ?></td></tr>
       </table>
	   
	   <table>
	  <?php echo "<h4>" .'Appointment scheduling and Booking Manager Smtp Email Setting'. "</h4>"; ?>
	  <tr><td class='frist'><?php _e("SMTP Host: " ); ?></td><td class='second'><input type="text" name="eav_smtp_host" value="<?php echo esc_attr($smtp_host); ?>" size="20"></td><td class='third'><?php _e("SMTP Host Eg. smtp.gmail.com " ); ?></td></tr>
        <tr><td class='frist'><?php _e("SMTP Username: " ); ?></td><td class='second'><input type="text" name="eav_smtp_username" value="<?php echo esc_attr($smtp_username); ?>" size="20"></td><td class='third'><?php _e(" SMTP Username for configuration" ); ?></td></tr>
        <tr><td class='frist'><?php _e("SMTP Password: " ); ?></td><td class='second'><input type="text" name="eav_smtp_password" value="<?php echo esc_attr($smtp_password); ?>" size="20"></td><td class='third'><?php _e(" SMTP Password for configuration " ); ?></td></tr>
        <tr><td class='frist'><?php _e("SMTP Port: " ); ?></td><td class='second'><input type="text" name="eav_smtp_port" value="<?php echo esc_attr($smtp_port); ?>" size="20"></td><td class='third'><?php _e(" SMTP Port you want to use Eg. 25" ); ?></td></tr>
        <tr><td class='frist'><?php _e("From Email Address: " ); ?></td><td class='second'><input type="text" name="eav_smtp_email" value="<?php echo esc_attr($smtp_email); ?>" size="20"></td><td class='third'><?php _e(" Email through which mail are send to Scheduler and booker" ); ?></td></tr>
       <?php  wp_nonce_field( 'save_admin_settings', 'save_admin_settings_nounce' );?>
        <tr class="submit">
        <td class='frist'><input type="submit" name="Submit" value="<?php _e('Update Options' ) ?>" />
        </tr>
		</table>
    </form>
</div>