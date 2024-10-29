
jQuery(document).ready(function($) {
	
	
	function cardFormValidate(){
    var cardValid = 0;
      
    //Card validation
    $('#card_number').validateCreditCard(function(result) {
        var cardType = (result.card_type == null)?'':result.card_type.name;
        if(cardType == 'Visa'){
            var backPosition = result.valid?'2px -163px, 260px -87px':'2px -163px, 260px -61px';
        }else if(cardType == 'MasterCard'){
            var backPosition = result.valid?'2px -247px, 260px -87px':'2px -247px, 260px -61px';
        }else if(cardType == 'Maestro'){
            var backPosition = result.valid?'2px -289px, 260px -87px':'2px -289px, 260px -61px';
        }else if(cardType == 'Discover'){
            var backPosition = result.valid?'2px -331px, 260px -87px':'2px -331px, 260px -61px';
        }else if(cardType == 'Amex'){
            var backPosition = result.valid?'2px -121px, 260px -87px':'2px -121px, 260px -61px';
        }else{
            var backPosition = result.valid?'2px -121px, 260px -87px':'2px -121px, 260px -61px';
        }
        $('#card_number').css("background-position", backPosition);
        if(result.valid){
            $("#card_type").val(cardType);
            $("#card_number").removeClass('required');
            cardValid = 1;
        }else{
            $("#card_type").val('');
            $("#card_number").addClass('required');
            cardValid = 0;
        }
    });
      
    //Form validation
    var cardName = $("#name_on_card").val();
    var expMonth = $("#expiry_month").val();
    var expYear = $("#expiry_year").val();
    var cvv = $("#cvv").val();
    var regName = /^[a-z ,.'-]+$/i;
    var regMonth = /^01|02|03|04|05|06|07|08|09|10|11|12$/;
    var regYear = /^2016|2017|2018|2019|2020|2021|2022|2023|2024|2025|2026|2027|2028|2029|2030|2031$/;
    var regCVV = /^[0-9]{3,3}$/;
    if (cardValid == 0) {
        $("#card_number").addClass('required');
        $("#card_number").focus();
        return false;
    }else if (!regMonth.test(expMonth)) {
        $("#card_number").removeClass('required');
        $("#expiry_month").addClass('required');
        $("#expiry_month").focus();
        return false;
    }else if (!regYear.test(expYear)) {
        $("#card_number").removeClass('required');
        $("#expiry_month").removeClass('required');
        $("#expiry_year").addClass('required');
        $("#expiry_year").focus();
        return false;
    }else if (!regCVV.test(cvv)) {
        $("#card_number").removeClass('required');
        $("#expiry_month").removeClass('required');
        $("#expiry_year").removeClass('required');
        $("#cvv").addClass('required');
        $("#cvv").focus();
        return false;
    }else if (!regName.test(cardName)) {
        $("#card_number").removeClass('required');
        $("#expiry_month").removeClass('required');
        $("#expiry_year").removeClass('required');
        $("#cvv").removeClass('required');
        $("#name_on_card").addClass('required');
        $("#name_on_card").focus();
        return false;
    }else{
        $("#card_number").removeClass('required');
        $("#expiry_month").removeClass('required');
        $("#expiry_year").removeClass('required');
        $("#cvv").removeClass('required');
        $("#name_on_card").removeClass('required');
        $('#cardSubmitBtn').prop('disabled', false);  
        return true;
    }
}
	
    //Card form validation on input fields
    $('#paymentForm_payment input[type=text]').live('keyup',function(){
      
		cardFormValidate();
    });
    
    //Submit card form
    $("#cardSubmitBtn").live('click',function(event){
		
		
      /* stop form from submitting normally */
      event.preventDefault();
		
		
		msg = '';
	
		if($("#name").val() == '')
		msg += 'Please enter a Name\r\n';

		if($("#email").val() == '')
		msg += 'Please enter an Email address\r\n';

		if($("#phone").val() == '')
		msg += 'Please enter a Phone number\r\n';	

		if(msg != '') {
			alert(msg);
			return false;
		}
		
        if (cardFormValidate()) {
            var formData = $('#paymentForm').serialize();
            $.ajax({
                type: 'POST',
                url: $('.ajax-url').val(),
                 data: { 
				    action  : 'ajax_act',        
                    'card_number'   : $('#card_number').val() ,
					  'card_type'   : $('#card_type').val() ,
				  'expiry_month'   : $('#expiry_month').val() ,
				   'expiry_year'   : $('#expiry_year').val() ,
				    'cvv'   : $('#cvv').val() ,
					 'name_on_card'   : $('#name_on_card').val() ,
                   'username'   : $('[name="bookie_username"]').val() , 
				   'email'   : $('#email').val() ,
				   'phone'   : $('#phone').val() ,
				   'total_hidden'   : $('#total_hidden').val() ,
				    'day_book'   : $('[name="day_book"]').val() ,
					'month_book'   : $('[name="month_book"]').val() ,
					 'year_book'   : $('[name="year_book"]').val() ,
					  'slots_booked_id'   : $('[name="slots_booked_id"]').val() ,
					  'slots_booked'   : $('[name="slots_booked"]').val() ,
					 'cost_per_slot'   : $('[name="cost_per_slot"]').val() ,
					  'booking_date'   : $('[name="booking_date"]').val() 
					 
                } ,
				
				beforeSend: function(){  
                    $("#cardSubmitBtn").val('Processing....');
                },
                success:function(data_bind){
				if(data_bind.success == 'Success')
				{
				  $('#paymentSection').slideUp('slow');
                  $('#orderInfo').slideDown('slow');
                  $('#orderInfo').html('<p>Slots Successfully Booked The payment Transaction id is: <span>#'+data_bind.transaction_id+'</span></p>');
					
				}
				else{
					$('#paymentSection').slideUp('slow');
                    $('#orderInfo').slideDown('slow');
                    $('#orderInfo').html('<p>Wrong card details given, please try again.</p>');
					
				}
					
                },
				error: function () {
                alert("error in booking posts");
        }
            });
        }
    });
	  
//Submit card form if price is 0
	  $(".classname").live('click',function(event){
	
	
      /* stop form from submitting normally */
      event.preventDefault();
	  
		msg = '';
	
		if($("#name").val() == '')
		msg += 'Please enter a Name\r\n';

		if($("#email").val() == '')
		msg += 'Please enter an Email address\r\n';

		if($("#phone").val() == '')
		msg += 'Please enter a Phone number\r\n';	

		if(msg != '') {
			alert(msg);
			return false;
		}
		
		$.ajax({
                type: 'POST',
                url: $('.ajax-url').val(),
                 data: { 
				    action  : 'ajax_act_no_pay',        
                    'username'   : $('[name="bookie_username"]').val() , 
				   'email'   : $('#email').val() ,
				   'phone'   : $('#phone').val() ,
				   'total_hidden'   : $('#total_hidden').val() ,
				    'day_book'   : $('[name="day_book"]').val() ,
					'month_book'   : $('[name="month_book"]').val() ,
					 'year_book'   : $('[name="year_book"]').val() ,
					  'slots_booked_id'   : $('[name="slots_booked_id"]').val() ,
					  'slots_booked'   : $('[name="slots_booked"]').val() ,
					 'cost_per_slot'   : $('[name="cost_per_slot"]').val() ,
					  'booking_date'   : $('[name="booking_date"]').val() 
                } ,
                success:function(response){
				
					alert(response);
                },
				error: function () {
           alert("error in booking posts");
        }
            });

	});
	

});