<?php
// To create admin menu for Booking page
add_action("admin_menu", "addmenu_Booking");
function addmenu_Booking()
{
    add_menu_page("Booking", "Booking", "edit_posts", "admin_booking", "booking_Page", null, 99);

}
// Booking page function
function booking_Page()
{
	$msg='';
    global $wpdb;
	// For edit Booking
    if (isset($_GET['edit']))
    {

        if (isset($_POST['submit']))
        {
			$msg='Booking Updated Successfully.';
            global $wpdb;
			// update booking
            $wpdb->update('booking_list_vehicle', array(
                'fname' => $_POST['fname'],
                'lname' => $_POST['lname'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'vehicle' => $_POST['vehicle'],
                'vehicle_type' => $_POST['vehicle_type'],
                'price' => $_POST['price'],
                'status' => $_POST['status'],
                'message' => $_POST['message']
            ) , array(
                "id" => $_GET['id']
            ));
			// Send Email 
			if($_POST['status']=='Complete')
			{
					
					$from = get_option('admin_email');
					$message = "Congrats! Your booking request Status changed to ".$_POST['status'];
					$to = $_POST['email'];
					$subject = $_POST['status']." Booking Request";
					$headers = 'From: '. $from . "\r\n" .
					'Reply-To: ' . $from . "\r\n";
					$sent = wp_mail($to, $subject, strip_tags($message), $headers);
					
			}
			else
			{
					$from = get_option('admin_email');
					$message = "Your booking request Status changed to ".$_POST['status'];
					$to = $_POST['email'];
					$subject = $_POST['status']." Booking Request";
					$headers = 'From: '. $from . "\r\n" .
					'Reply-To: ' . $from . "\r\n";
					$sent = wp_mail($to, $subject, strip_tags($message), $headers);
			}
		$url = site_url();
		$durl=$url.'/wp-admin/admin.php?page=admin_booking&msg=1';
		if ( wp_redirect( $durl ) ) {
		exit; 
		
			}
		}
        $id = $_GET['id'];
        $results = $wpdb->get_results("SELECT * FROM booking_list_vehicle where id=" . $id);
?>
<div class="container">
  <h2>Edit Booking</h2>
		<?php
        if ($msg != '')
        {
?>
		<div class="alert alert-success ">
  
		<strong>Success!</strong> Settings Saved.
		</div>
		<?php
        }
?>
	
 
  
  <form method="POST">

  <label  >First name:</label><br>
  <input type="text" id="fname" name="fname" value="<?php echo $results[0]->fname; ?>"><br>
  <label >Last name:</label><br>
  <input type="text" id="lname" name="lname" value="<?php echo $results[0]->lname; ?>"><br>
   <label >Email:</label><br>
  <input type="email" id="email" name="email" value="<?php echo $results[0]->email; ?>"><br>
  
   <label >Phone:</label><br>
  <input type="text" id="phone" name="phone" value="<?php echo $results[0]->phone; ?>"><br>
   <label > Select Vehicle Type:</label><br>
 <select name="vehicle_type" id="vehicle_type" onChange="get_posts_vehicle()">
 <option><?php echo $results[0]->vehicle_type; ?></option>
 </select><br>
  <label >Select Vehicle:</label><br>
 <select name="vehicle" id="vehicle_id" onChange="get_rates_vehicle()">
 <option><?php echo $results[0]->vehicle; ?></option>
 </select><br>
  <label >Starting Price:</label><br>
  <input type="text" id="price" name="price" value="<?php echo $results[0]->price; ?>"><br>
  <label >Message:</label><br>
  <textarea name="message"><?php echo $results[0]->message; ?></textarea><br>
  <input id="ajaxUrl" type="hidden" value="'.$ajaxUrl.'">
   <select name="status" id="status" >
 <option>Select Status</option>
 <?php if (isset($results[0]->status))
        { ?> <option selected="selected"><?php echo $results[0]->status; ?></option><?php
        } ?>
 <option>Pending</option>
 <option>Approved</option>
 <option>Reject</option>
 <option>On the way</option>
 <option>Complete</option>
 </select><br>
  <input name="action" type="hidden" value="submit_form_booking">
  <input type="submit" name="submit" value="Submit" >
</form>
</div>

		<?php
    }
    else
    {
        $results = $wpdb->get_results("SELECT * FROM booking_list_vehicle"); // Query to fetch data from database table and storing in $results
  
// Below code is for listing bookings.  
?>
<div class="container">
  <h2>Booking</h2>
  <?php 
		if(isset($_GET['msg']))
		{
		?>
		<div class="alert alert-success ">
  
		<strong>Success!</strong> Booking Updated.
		</div>
		<?php 	
		}
	?>
  <table class="table" id="myTable" >
    <thead>
      <tr>
        <th>First name</th>
        <th>Last name</th>
        
        <th>Email</th>
        <th>Phone</th>
        <th> Vehicle Type</th>
        <th> Vehicle</th>
        <th> Price</th>
        <th> Message</th>
        <th> Action</th>
      
      </tr>
    </thead>
    <tbody>
<?php
        foreach ($results as $res)
        {
?>
	<tr>
        <td><?php echo $res->fname; ?></td>
        <td><?php echo $res->lname; ?></td>
        <td><?php echo $res->email; ?></td>
        <td><?php echo $res->phone; ?></td>
		<td><?php echo $res->vehicle_type; ?></td>
        <td><?php echo $res->vehicle; ?></td>
        
        <td><?php echo $res->price; ?></td>
        <td><?php echo $res->message; ?></td>
        <td><a href="<?php echo site_url() . '/wp-admin/admin.php?page=admin_booking&edit=1&id=' . $res->id; ?>"> Edit Booking</a></td>
      
      </tr>
	<?php

        }
    }
}

