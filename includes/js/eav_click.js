jQuery(document).ready(function($) {
	
jQuery('p.green_booking').live( "click", function(event) {	

 $.ajax({
        url: $('.ajax-url').val(),
        type: 'POST',
        data: { action  : 'um_cb',        
                    'year'   : $(this).attr('year') ,
					'month'   : $(this).attr('month') ,
					'day'   : $(this).attr('day') 
                } ,
        success: function (response) {
          $(".result").html('');
		  $(".result").html(response);
		
        },
        error: function () {
            alert("Error in updating date...");
        }
    });  
   
   
});


jQuery('p.green_scheduling').live( "click", function(event) {	

$(this).css('background','black');
 $.ajax({
        url: $('.ajax-url').val(),
        type: 'POST',
        data: { action  : 'eav_cb',        
                    'year'   : $(this).attr('year') ,
					'month'   : $(this).attr('month') ,
					'day'   : $(this).attr('day') 
                } ,
        success: function (response) {
          $(".result_scheduling").html('');
		  $(".result_scheduling").html(response);
		  
        },
        error: function () {
            alert("Error in updating date...");
        }
    });  
   
   
});








/* jQuery('.forward').one( "click", function(event) {
alert($('.posting').val());

$.ajax({
        url: $('.ajax-url').val(),
        type: 'POST',
        data: { action  : 'u_cb',        
                    
                } ,
        success: function (response) {
            $("#lhs").html(response);
        },
        error: function () {
            alert("error");
        }
    });  
 


});	 */

}); 

//_eav_current_month