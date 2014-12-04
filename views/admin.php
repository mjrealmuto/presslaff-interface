

<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<!-- TODO: Provide markup for your options page here. -->
	
	<div class="wrap">
		
	<?php

	if( ! file_exists( plugin_dir_path(__FILE__) . "../assets/presslaff.ini" ) )
		{
			echo "<div class='msg_err'>You have not created a presslaff.ini file yet.  Without this file the plugin will not function.</div>";
		}	
	?>

	<?php screen_icon('plugins'); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form id="change_nowplaying_options" method="post" action="/wp-admin/admin-ajax.php" >
		
	  <?php
	   
	  // Output the hidden fields, nonce, etc.
	  settings_fields('presslaff_options');
	   
	  // Output the settings sections.
	  do_settings_sections( $this->plugin_slug );
	
	  // Submit button.
	  submit_button('Save INI','primary','submit','true',array('id'=>'save_pressalff_ini'));
	   
	  ?>
	  <input type='hidden' name='action' value='config' />
	 </form>
	 
</div>
	
</div>
