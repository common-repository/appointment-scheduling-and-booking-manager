var check_array = [ ];
var check_array2= [ ];

jQuery(document).ready(function($) {

jQuery('.fields').live('click', function() {


	
		dataval = $(this).data('val');
		dataid= $(this).data('id');
		
	
		// Show the Selected Slots box if someone selects a slot
		if($("#card_payment").css("display") == 'none') { 
			$("#card_payment").css("display", "block");
		}

		if(jQuery.inArray(dataval, check_array) == -1) {
			check_array.push(dataval);
		} else {
			// Remove clicked value from the array
			check_array.splice($.inArray(dataval, check_array) ,1);	
		}
		
		if(jQuery.inArray(dataid, check_array2) == -1) {
			check_array2.push(dataid);
		} else {
			// Remove clicked value from the array
			check_array2.splice($.inArray(dataid, check_array2) ,1);	
		}
		
		slots=''; hidden=''; basket = 0, hidden_id='';
		
		cost_per_slot = $("#cost_per_slot").val();
		//cost_per_slot = parseFloat(cost_per_slot).toFixed(2)

		for (i=0; i< check_array.length; i++) {
			slots += check_array[i] + '\r\n';
			hidden += check_array[i].substring(0, 8) + '|';
			basket = (basket + parseFloat(cost_per_slot));
		}
		
		for (i=0; i< check_array2.length; i++) {
			
			hidden_id += check_array2[i] + '|';
			
		}
		
		// Populate the Selected Slots section
		$("#selected_slots").html(slots);
		
	// Populate the Selected Slots  id section
		$("#slots_booked_id").val(hidden_id);
		
		// Update hidden slots_booked form element with booked slots
		$("#slots_booked").val(hidden);		

		// Update basket total box
		basket = basket.toFixed(2);
		if(this.checked){
		if(basket>0){
		$(".classname").css("display", "none");	
		}
		else if(basket<=0){
		$("#paymentForm_payment").css("display", "none");		
		}
		}
		$("#total").html(basket);	
		$("#total_hidden").val(basket);

		// Hide the basket section if a user un-checks all the slots
		if(check_array.length == 0)
		$("#card_payment").css("display", "none");
		
	});
	
	jQuery('.setting_saved').live('click', function(event) {

	
      /* stop form from submitting normally */
      event.preventDefault();

			$.ajax({
                type: 'POST',
                url: $('.ajax-url').val(),
                 data: { 
				    action  : 'ajax_setting_act',     
                    'saving_settings_nounce'	 : $('[name="saving_settings_nounce"]').val() ,		
                    'comm_email'   : $('[name="comm_email"]').val()  ,
		            'paypal_username'   : $('[name="paypal_username"]').val() ,
					'paypal_paswrd'   : $('[name="paypal_paswrd"]').val() ,
					'paypal_signature'   : $('[name="paypal_signature"]').val() 
                } ,
                success:function(data){
			     alert('Your settings are succesfully saved');
                },
				error: function () {
            alert("Your settings not try again...");
        }
            });

	});

	
});




