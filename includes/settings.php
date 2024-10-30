<?php wp_head(); ?>
<?php
//Put global con outside
 global $wpdb; 
//If form is posted, do
if(isset($_POST['submit']) && !empty($_POST['submit'])) {
    $pid = "1";
 	$table = $wpdb->prefix . 'loopwi_adsplugins_settings';
    $wpdb->query($wpdb->prepare("UPDATE $table SET site_code='".sanitize_text_field($_POST['site_code'])."' WHERE pid=$pid"));    
}
//Check if the record is present, and get it out
$results = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}loopwi_adsplugins_settings WHERE pid = 1" );
 
	$site_code = $results->site_code; 
 
?>
<div class="form-v4">
	<div class="page-content">
		<div class="form-v4-content"> 
				
			 
			<form class="form-detail" method="POST">
				<h2><img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ); ?>images/logo.png"></h2> 

				<h2 style="line-height: normal;">Advert Shortcode</h2>
				<p>When this plugin has been activated, you will see the shortcode shown below. <br>Place this code any where you want the adverts to display. Many displays on your site means more income from adverts</p>
				 
				<?php if ( null != $results->site_code ) { ?>

					<h3 style="line-height: normal;">[Loopwi]</h3>

				<?php }else{ ?>

					<h3 style="line-height: normal; font-style: italic;">~ Activate site first ~ </h3>

				<?php } ?>

				 <?php if ( null != $results->site_code ) { ?>
				
				<div class="form-row">
					<label for="your_email">Site Code</label>
					<input type="text" style="font-weight: bold;" name="site_code" id="site_code" class="input-text" autocomplete="off" value="<?php echo esc_html($site_code); ?>">
				</div> 
			    
				 
				<div class="form-row-last"> 
					<input type="submit" name="submit" class="register" value="UPDATE">
				</div>

			   <?php } ?>
				 
			</form> 		
			                       
		</div>
	</div>
</div>
