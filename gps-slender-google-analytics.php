<?php
/*
Plugin Name: Slender Google Analytics
Plugin URI: http://wordpress.org/#
Description: This plugin simply adds GA tracking to your site. It loads the tracking script in the header as recommended by google and that's it - no extra bulk.
Author: Jordan Bracy
Version: 1.0.0
Author URI: gps.thebracybunch.com
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Add an options page
add_action( 'admin_menu', 'slender_ga_menu' );

function slender_ga_menu() {
	add_options_page( 'Slender Google Analytics Settings', 'Slender Google Analytics Settings', 'administrator', 'slender_ga', 'slender_ga_settings_page' );
}

// display the page
function slender_ga_settings_page() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$ga_id = get_option('slender_ga_id');
	$track_admin = get_option('slender_ga_track_admin');

  if ( isset($_POST['ga_id']) || isset($_POST['ga_track_admin']) ) {
      update_option('slender_ga_id', esc_attr( $_POST['ga_id'] ) );
			update_option('slender_ga_track_admin', esc_attr( $_POST['ga_track_admin'] ) );
			?>
				<div class="updated"><p><strong>Settings Saved</strong></p></div>
			<?
      $id_value = esc_attr( $_POST['ga_id'] );
			$track_admin = esc_attr( $_POST['ga_track_admin'] );
  }

		?>
	<h1>Slender Google Analytics Settings</h1>
  <form method="post">
    <label for="ga_id">Enter your Google Analytics ID here: </label><br />
    <input type="text" name="ga_id" id="ga_id" value="<? echo ( isset($id_value) ) ? $id_value : $ga_id; ?>"><br />
		<label for="ga_track_admin">Do you want to track visits of logged in users?</label><br />
		<input type="checkbox" name="ga_track_admin" value="yes" <?if ($track_admin == 'yes') {?> checked <? } ?> >Yes, keep track of what my peeps are doing!<br />
		<input type="submit" value="Save" class="button button-primary button-large">
  </form>
<?
}

// add the ga script to header
function slender_ga_script() {
		$ga_id = get_option("slender_ga_id");
		$track_admin = get_option('slender_ga_track_admin');

	//if we want to track admins just add it
	if ($track_admin == 'yes'){
			?>
			<script type="text/javascript">
	      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

	      ga('create', '<? echo $ga_id; ?>', 'auto');
	      ga('send', 'pageview');
	    </script>
			<?
		}

		//otherwise check if the current user is an admin and act accordingly
		else {
			$is_admin = (current_user_can( 'administrator' ) ? 'true' : 'false');

			if ( !$is_admin || !is_user_logged_in() ) {
				?>
				<script type="text/javascript">
					(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
					})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

					ga('create', '<? echo $ga_id; ?>', 'auto');
					ga('send', 'pageview');
				</script>
				<?
			}
		}
}
add_action('wp_head', 'slender_ga_script');
?>
