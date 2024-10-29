
jQuery(document).ready(function($) {
	
jQuery('.slot_scheduling').live('change', function() {
    if(this.checked) 
	{
		
		
		 $.ajax({
        url: $('.ajax-url').val(),
        type: 'POST',
        data: { action  : 'eav_sched',
                    'day': $(this).attr('day') , 
                    'month': $(this).attr('month') ,
                    'year': $(this).attr('year') ,					 
                    'slot_start'   : $(this).attr('slot_start') ,
					'date'   : $(this).attr('date') ,
					'u_id'   : $(this).attr('u_id') ,
                } ,
        success: function (response) {
          $(".result_scheduling").html('');
		  $(".result_scheduling").html(response);
	
		  		 
		  
        },
        error: function () {
            alert("Not schedulded Error in saving Schedulded post...");
        }
             }); 
      // checkbox is checked
    }
	else
	{
	$.ajax({
        url: $('.ajax-url').val(),
        type: 'POST',
        data: { action  : 'eav_nonsched',        
                    'post_id'   : $(this).attr('data-id') ,
					'day': $(this).attr('day') , 
                    'month': $(this).attr('month') ,
                    'year': $(this).attr('year') ,	
					
                } ,
        success: function (response) {
          $(".result_scheduling").html('');
		  $(".result_scheduling").html(response);
		 
		  
		  
        },
        error: function () {
            alert("Not schedulded Error in deleting Schedulded post...");
        }
             }); 
			 
	}
});

});