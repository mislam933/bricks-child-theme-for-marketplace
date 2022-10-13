<?php 
/**
 * Register/enqueue custom scripts and styles
 */
add_action( 'wp_enqueue_scripts', function() {
	// Enqueue your files on the canvas & frontend, not the builder panel. Otherwise custom CSS might affect builder)
	if ( ! bricks_is_builder_main() ) {
		wp_enqueue_style( 'bricks-child', get_stylesheet_uri(), ['bricks-frontend'], filemtime( get_stylesheet_directory() . '/style.css' ) );
	}
  wp_enqueue_script( 'bricks-child-public-js', get_stylesheet_directory_uri() . '/assets/js/public-script.js', ['jquery'], '', true );
  wp_localize_script( 'bricks-child-public-js', 'exbp_ajax_object',
    array( 
      'ajaxurl' => admin_url( 'admin-ajax.php' ),
    )
  );
} );

/**
 * Register custom elements
 */
add_action( 'init', function() {
  $element_files = [
    __DIR__ . '/elements/title.php',
  ];

  foreach ( $element_files as $file ) {
    \Bricks\Elements::register_element( $file );
  }
}, 11 );

/**
 * Add text strings to builder
 */
add_filter( 'bricks/builder/i18n', function( $i18n ) {
  // For element category 'custom'
  $i18n['custom'] = esc_html__( 'Custom', 'bricks' );

  return $i18n;
} );

function exbp_current_user_has_membership(){

  //General code example for checking if a current user is pending or not.

  global $current_user;

  //check to see if the user has any membership level
  if( pmpro_hasMembershipLevel() ) {

    // Get the current user ID and their level ID for the PMPro Approvals meta.
    $user_id = $current_user->ID;
    $level = pmpro_getMembershipLevelForUser( $user_id );
    $level_id = $level->id;

    //get the user meta.
    $approval_status = get_user_meta( $user_id, 'pmpro_approval_'.$level_id, true );

    //check the user meta.
    if( $approval_status['status'] !== 'pending' || $approval_status['status'] !== 'denied' ){
    //show content here, assume they are approved or don't require approval if the user doesn't have pending or denied. 
      return true;
    }else{
    //show a restricted message for users.
      return false;
    }
  }
}

function exbp_membership_status(){
  global $current_user;

  //check to see if the user has any membership level
  if( pmpro_hasMembershipLevel() ) {

    // Get the current user ID and their level ID for the PMPro Approvals meta.
    $user_id = $current_user->ID;
    $level = pmpro_getMembershipLevelForUser( $user_id );
    
    //get the user meta.
    if(!empty($level) && !empty($level->enddate)){
      $enddate = $level->enddate; //date(get_option('date_format'), $level->enddate);
      $now = strtotime("now");
      if ($now >= $level->enddate) {
        return "expired";
      }else{
        return "activate";
      }
    }
  }
}

function exbp_download_teplate_file(){
  $download_url = '';
  $post_id = ($_POST['post_id']) ? $_POST['post_id'] : '';
  $user_has_membership = exbp_current_user_has_membership();
  $exbp_membership_status = exbp_membership_status();
  if ($user_has_membership && $exbp_membership_status == 'activate') {
    $download_url = get_field('download_link', $post_id);
    if (!empty($download_url)) {
      $total_download = get_field('_exbp_total_download', $post_id);
      $next = $total_download + 1;
      update_field('_exbp_total_download', $next, $post_id);
    }
  }
  echo $download_url;
  exit();
}
add_action( 'wp_ajax_nopriv_exbp_download_teplate_file', 'exbp_download_teplate_file' );
add_action( "wp_ajax_exbp_download_teplate_file", 'exbp_download_teplate_file' );
