function get_posts_vehicle()
{
	jQuery.ajax({
         type : "post",
        
         url : document.getElementById("ajaxUrl").value,
         data : {action: "get_post_vehicle", cat_id : document.getElementById("vehicle_type").value},
         success: function(response) {
			
          jQuery('#vehicle_id').html(response);
         }
      });

}
function get_rates_vehicle()
{
	jQuery.ajax({
         type : "post",
        
         url : document.getElementById("ajaxUrl").value,
         data : {action: "get_rate_vehicle", post_id : document.getElementById("vehicle_id").value},
         success: function(response) {
			
          jQuery('#starting_price').html(response);
         }
      });
	
}

function save_data()
{
	var message=jQuery("textarea[name='message']").val();
	var vehicle_id=jQuery("#vehicle_id option:selected" ).text();
	var fname=jQuery("#fname").val();
	var lname=jQuery("#lname").val();
	var email=jQuery("#email").val();
	var phone=jQuery("#phone").val();
	var vehicle_type=jQuery("#vehicle_type option:selected" ).text();
	var starting_price=jQuery("#starting_price").html();
	
	jQuery.ajax({
         type : "post",
        
         url : document.getElementById("ajaxUrl").value,
         data : {action: "submit_form_booking", message : message,vehicle_id:vehicle_id,fname:fname,lname:lname,email:email,phone:phone,vehicle_type:vehicle_type,starting_price:starting_price},
         success: function(response) {
			alert('Booking have been submitted.');
          ///jQuery('#starting_price').html(response);
         }
      });
	
}