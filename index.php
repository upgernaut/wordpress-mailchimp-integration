<?php
/*
Plugin Name: Custom Mailchimp Integration
Plugin URI: 
Description: This plugin is for integrating Mailchimp into website
Author: Aram
Version: 1.0
Author URI: 
*/

add_action( 'get_footer', 'vr_blog_hook' );

function vr_blog_hook($content) {
	// echo "<!-- grdon"; 
	// var_dump(in_category('blog'));
	// echo "-->";
	
	if((in_category('blog') && is_single())) {		
		// Get the form view
		ob_start();
		include( plugin_dir_path( __FILE__ ) . 'inc/blog-subscription-form.php');
		include( plugin_dir_path( __FILE__ ) . 'inc/blog-subsription-modal.php');
		$form = ob_get_clean();

		echo $form;
	// } else if(is_front_page() && isset($_GET['fb_key'])) {
		/* show the subscription only if home and isset fb_key */
		// ob_start();
		// include( plugin_dir_path( __FILE__ ) . 'inc/blog-subscription-form.php');
		// include( plugin_dir_path( __FILE__ ) . 'inc/blog-subsription-modal.php');
		// $form = ob_get_clean();
		// echo $form;
	} else if(is_front_page()) {
		// Get the form view
		ob_start();
		include( plugin_dir_path( __FILE__ ) . 'inc/blog-subscription-form.php');
		include( plugin_dir_path( __FILE__ ) . 'inc/blog-subsription-modal.php');
		$form = ob_get_clean();

		echo $form;
	} else {
		return $content;	
	}
}

function include_vr_blog_js()
{
	// Styles for Vertical Response
	wp_enqueue_style( 'gettreated-vr-style', plugins_url( '/css/gettreated-vr-style.css', __FILE__ ) );
	
    // Register the script like this for a plugin:
	wp_register_script( 'gettreated-jquery', 'https://code.jquery.com/jquery-latest.js?pl' );	
    wp_register_script( 'gettreated-vr-script', plugins_url( '/js/gettreated-vr-script.js', __FILE__ ) );
	
	wp_enqueue_script( 'gettreated-jquery', false, false, false, true );
	wp_enqueue_script( 'gettreated-vr-script', false, array( 'gettreated-jquery' ), false, true );	 

}
add_action( 'wp_enqueue_scripts', 'include_vr_blog_js' );

function addSubscriptionOnBlog() {
	if(isset($_POST['gettreated-mailchimp-subscription-blog'])) {
		
		if(isset($_POST['contact']) && !empty($_POST['contact'])) {

			$email = $_POST['contact']['email_address'];
			
			require_once (plugin_dir_path( __FILE__ ) . 'inc/MCAPI.class.php');
			require_once (plugin_dir_path( __FILE__ ) . 'inc/config.inc.php'); //contains apikey

			$api = new MCAPI($apikey);

			$listId = 'bf6c7f1ebe'; // ID of 'Followers' list

			$merge_vars = array('FNAME'=>'', 'LNAME'=>'', 
							  'GROUPINGS'=>array(
									array('name'=>'Followers', 'groups'=>'Blog'),
									)
								);

			// By default this sends a confirmation email - you will not see new members
			// until the link contained in it is clicked!
			// remove 'true' if you do not want to send the confirmation email to client
			$retval = $api->listSubscribe( $listId, $email, $merge_vars, 'html', true );

			if ($api->errorCode){
				// echo "Unable to load listSubscribe()!\n";
				// echo "\tCode=".$api->errorCode."\n";
				// echo "\tMsg=".$api->errorMessage."\n";
				
				echo 'error';
			} else {
				//echo "Subscribed - look for the confirmation email!\n";
				echo 'success';
			}	
			
		}
		
		die;
	}
}
add_action( 'init', 'addSubscriptionOnBlog' );

/**
 *
 * Add client to mailchimp on get estimate
 *
 */

function addSubscriptionOnGetEstimate() {

		if($_POST && $_GET['act'] && $_GET['act'] == 'requestConsultation' && $_POST['subscriptionGt'] == '1')
		{
			$clientGroups = array();

			foreach($_POST['gecustom']['Procedures'] as $single_procedure) {

				$procInfo = get_page_by_title($single_procedure,'object', 'procedure');

				$term = get_the_terms( $procInfo->ID, 'procedures_tax' ); 

				
				// Get the procedure type and add into groups array
				if($term[0]->slug == 'dental-procedures') {

					$clientGroups[] = 'Dental';

				} elseif ($term[0]->slug == 'plastic-surguries') {

					$clientGroups[] = 'Plastic';

				}

				// Get the procedure specific procedures and add into client groups
				if($procInfo->post_title == 'Liposuction') {
		
					$clientGroups[] = 'Liposuction';					

				} elseif($procInfo->post_title == 'Rhinoplasty') {

					$clientGroups[] = 'Rhinoplasty';					

				}
			}


			//Require mailchimp API
			require_once (plugin_dir_path( __FILE__ ) . 'inc/MCAPI.class.php');

			require_once (plugin_dir_path( __FILE__ ) . 'inc/config.inc.php'); //contains apikey

			$clientGroups = implode(',',$clientGroups);
			$email = $_POST['gecustom']['Email'];
			$firstName = $_POST['bname'];
			$age = $_POST['gecustom']['Age'];
			$country = $_POST['gecustom']['Country'];
			$city = $_POST['gecustom']['City'];
			$gender = $_POST['gecustom']['Sex'];
			$language = $_POST['gecustom']['Language correspondence'];
			$phone = $_POST['gecustom']['Phone'];

			$api = new MCAPI($apikey);

			$listId = '62621413fc'; // ID of 'Potential clients' list

			$merge_vars = array(
				'FNAME'=>$firstName, 
				'LNAME'=>'', 
				'GROUPINGS'=>array(
					array('name'=>'Procedure Type', 'groups'=>$clientGroups),
				),
				'MMERGE3'=>$age,
				'MMERGE4'=>$country,
				'MMERGE5'=>$city,
				'MMERGE7'=>$gender,
				'MMERGE8'=>$language,
				'MMERGE9'=>$phone,
			);

			$retval = $api->listSubscribe( $listId, $email, $merge_vars, 'html', true );

			if ($api->errorCode){
				echo 'error';
			} else {
				echo 'success';
			}					

		}
	
}
add_action( 'init', 'addSubscriptionOnGetEstimate' );