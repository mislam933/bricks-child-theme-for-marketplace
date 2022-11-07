<?php 
/**
 * Register/enqueue custom scripts and styles
 */
add_action( 'wp_enqueue_scripts', function() {
  // Enqueue your files on the canvas & frontend, not the builder panel. Otherwise custom CSS might affect builder)
  if ( ! bricks_is_builder_main() ) {
    wp_enqueue_style( 'bricks-child', get_stylesheet_uri(), ['bricks-frontend'], filemtime( get_stylesheet_directory() . '/style.css' ) );
  }
  wp_enqueue_style('bricks-child-public-css', get_stylesheet_directory_uri() . '/assets/css/public-style.css', '', '', 'all'  );
  wp_enqueue_script( 'bricks-child-public-js', get_stylesheet_directory_uri() . '/assets/js/public-script.js', ['jquery'], '', true );
  wp_localize_script( 'bricks-child-public-js', 'exbp_ajax_object',
    array( 
      'ajaxurl'   => admin_url( 'admin-ajax.php' )
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
      exbp_template_dounloaded_by_user($post_id);
    }
  }
  echo $download_url;
  exit();
}
add_action( 'wp_ajax_nopriv_exbp_download_teplate_file', 'exbp_download_teplate_file' );
add_action( "wp_ajax_exbp_download_teplate_file", 'exbp_download_teplate_file' );

// 
function exbp_template_dounloaded_by_user($post_id){
  $user_id = get_current_user_id();
  $teplates = array( $post_id );

  // try to find some `downloaded_templates` user meta 
  $previous_downloaded_templates = get_user_meta( $user_id, 'downloaded_templates', true );

  /**
  * First, the condition for when no downloaded_templates user meta data exists
  **/ 
  if ( empty( $previous_downloaded_templates ) ) {
      add_user_meta( $user_id, 'downloaded_templates', $teplates );
  }else{
    $new_template = $post_id;
    $new_template_attr = array($new_template);

    if (!in_array($new_template, $previous_downloaded_templates)) {
      $new_downloaded_templates = array_merge($previous_downloaded_templates,$new_template_attr);
      update_user_meta( $user_id, 'downloaded_templates', $new_downloaded_templates, $previous_downloaded_templates );
    }

  }
}

// function that runs when shortcode is called
function exbp_downloaded_template_shortcode() {
if (!is_user_logged_in() ) {
    return;
  }
      $user_id = get_current_user_id();
      $post_ids = get_user_meta( $user_id, 'downloaded_templates', true );
      if (empty($post_ids)) {
        echo "<p style='color: red;'>You do not have any item in your download</p>";
        return;
      }
      $query = new WP_Query( array( 
        'post_type' => 'bricks-templates',
        'post__in' => $post_ids
      ) );  
    ob_start();
  echo '<div class="download-items-cards">';?>

<?php 

    while ( $query->have_posts() ) : $query->the_post(); 
    $categories = get_the_terms(get_the_ID(), 'temlate_cats');
?>

      <div class="download-items-cards-image-grid">
        <div class="download-items-cards-image-preview">
        <?php if ( has_post_thumbnail() ) : ?>
          <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
            <?php the_post_thumbnail(); ?>
          </a>
        <?php endif; ?>
        </div>
        <div class="download-items-cards-content-preview">
          <div class="download-items-cards-content-title">
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
          </div>
          <div class="download-items-cards-content-author-category">
            <i>by </i> <?php the_author(); ?> in <?php echo esc_html($categories[0]->name); ?>
          </div>
          <div class="download-items-cards-content-footer">
            <div class="download-items-cards-content-footer-content">
              <div class="download-items-cards-content-footer-bottom-content-star-review">
                <?php echo do_shortcode('[posts_like_dislike id='.get_the_ID().']'); ?>
              </div>
              <div class="download-items-cards-content-footer-bottom-content-total-download">
                <i class="fas fa-download"></i>
                <?php echo get_field('_exbp_total_download', get_the_ID()); ?>
              </div>              
            </div>
            <div class="download-items-cards-content-footer-bottom-button">
              <a href="<?php the_permalink(); ?>" class="btn">Live Preview</a>
            </div>
          </div>
        </div>
      </div>

<?php
 
    endwhile;

    wp_reset_postdata(); 
   ?>
   <?php echo '</div>'; ?> 
  <?php
     // echo 'shortcode output';

      $output = ob_get_contents();
      ob_end_clean();
      echo $output; // Here comes the total output of your shortcode
}
// register shortcode
add_shortcode('downloaded_templates', 'exbp_downloaded_template_shortcode');


//the function return subscription
function exbp_membership_levels_shortcode(){
  global $wpdb, $pmpro_msg, $pmpro_msgt, $current_user;
  if ( !is_user_logged_in() ) {
    return; 
  }
  $pmpro_levels = pmpro_sort_levels_by_order( pmpro_getAllLevels(false, true) );
  $pmpro_levels = apply_filters( 'pmpro_levels_array', $pmpro_levels );
  ob_start();
  ?>
    <div class="exbp-level-items-container">
    <?php 
    $count = 0;
    $has_any_level = false;
    foreach($pmpro_levels as $level)
    {
      $user_level = pmpro_getSpecificMembershipLevelForUser( $current_user->ID, $level->id );
      $has_level = ! empty( $user_level );
      $has_any_level = $has_level ?: $has_any_level;
      $icon_class = '';
      switch ($level->name) {
        case 'Starter':
          $icon_class = 'fa-paper-plane';
          break;

        case 'Premium':
          $icon_class = 'fa-rocket';
          break;

        case 'Lifetime':
          $icon_class = 'fa-place-of-worship';
          break;
        
        default:
          $icon_class = 'fa-paper-plane';
          break;
      }
    ?>
      <div class="exbp-level-items-wrapper <?php if($count++ % 2 == 0) { ?>odd<?php } ?><?php if( $has_level ) { ?> active<?php } ?>">
        <div class="exbp-level-item item-<?php echo $has_level ? $level->name : ''; ?>">
            <div class="exbp-level-item-header">
              <div class="exbp-level-item-header-icon">
                <i class="fas <?php echo $icon_class ?>"></i>
              </div>
              <div class="exbp-level-item-header-content">
                <h5 class="exbp-level-item-header-content-title"><?php echo $has_level ? "<strong>{$level->name}</strong>" : $level->name?></h5>
                <?php 
                  $cost_text = pmpro_getLevelCost($level, true, true);
                  if (!empty($cost_text )) {
                    echo '<p class="exbp-level-item-header-content-price">'. $cost_text .'</p>';
                  }
                 ?>
                
              </div>
            </div>
            <div class="exbp-level-item-body">
              <?php 
                $expiration_text = pmpro_getLevelExpiration($level);
                if (!empty($expiration_text)) {
                  echo '<p class="exbp-level-item-body-content">'. $expiration_text .'</p>';
                }
               ?>
            </div>
            <div class="exbp-level-item-footer">
              <?php if ( ! $has_level ) { ?>                  
                <a class="<?php echo pmpro_get_element_class( 'pmpro_btn pmpro_btn-select', 'pmpro_btn-select' ); ?>" href="<?php echo pmpro_url("checkout", "?level=" . $level->id, "https")?>"><?php _e('Select', 'paid-memberships-pro' );?></a>
              <?php } else { ?>      
                <?php
                  //if it's a one-time-payment level, offer a link to renew 
                  if( pmpro_isLevelExpiringSoon( $user_level ) && $level->allow_signups ) {
                    ?>
                      <a class="<?php echo pmpro_get_element_class( 'pmpro_btn pmpro_btn-select', 'pmpro_btn-select' ); ?>" href="<?php echo pmpro_url("checkout", "?level=" . $level->id, "https")?>"><?php _e('Renew', 'paid-memberships-pro' );?></a>
                    <?php
                  } else {
                    ?>
                      <a class="<?php echo pmpro_get_element_class( 'pmpro_btn disabled', 'pmpro_btn' ); ?>" href="<?php echo pmpro_url("account")?>"><?php _e('Active', 'paid-memberships-pro' );?></a>
                    <?php
                  }
                ?>
              <?php } ?>
            </div>
        </div>
      </div>
      <?php } ?>
    </div>
  <?php
      $output = ob_get_contents();
      ob_end_clean();
      echo $output; // Here comes the total output of your shortcode
}
// register shortcode
add_shortcode('exbp_membership_levels','exbp_membership_levels_shortcode');

// login page custom design


function exbp_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/site-login-logo.png);
            height:65px;
            width:320px;
            background-size: 320px 65px;
            background-repeat: no-repeat;
          padding-bottom: 30px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'exbp_login_logo' );

function exbp_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'exbp_login_logo_url' );

function exbp_login_logo_url_title() {
    return 'Bricks Plus';
}
add_filter( 'login_headertext', 'exbp_login_logo_url_title' );

function exbp_login_stylesheet() {
    wp_enqueue_style( 'exbp-custom-login', get_stylesheet_directory_uri() . '/assets/login/style-login.css' );
    wp_enqueue_script( 'exbp-custom-login', get_stylesheet_directory_uri() . '/assets/login/style-login.js' );
}
add_action( 'login_enqueue_scripts', 'exbp_login_stylesheet' );

add_filter( 'login_redirect', function( $url, $query, $user ) {
  return home_url();
}, 10, 3 );

add_action('after_setup_theme', 'exbp_remove_admin_bar');
function exbp_remove_admin_bar() {
  if (!current_user_can('administrator') && !is_admin()) {
    show_admin_bar(false);
  }
}

add_action('wp_logout','exbp_redirect_after_logout');
function exbp_redirect_after_logout(){
  wp_safe_redirect( home_url() );
  exit;
}

// redirect after login via modal.
function exbp_redirect_after_login_from_modal( $user ) {
  wp_safe_redirect( home_url() );
  exit;
}
add_action( 'xoo_el_login_success', 'exbp_redirect_after_login_from_modal', 10, 1 );

/**
 *  Add nonce to logout URL in navigation
 */

function exbp_add_logout_url_nonce($items){
  foreach($items as $item){
    if( $item->url == '/wp-login.php?action=logout'){
         $item->url = $item->url . '&_wpnonce=' . wp_create_nonce( 'log-out' );
    }
  }
  return $items;

}

add_filter('wp_nav_menu_objects', 'exbp_add_logout_url_nonce');

add_action( 'after_setup_theme', 'exbp_wpas_user_custom_fields' );
function exbp_wpas_user_custom_fields() {
  if ( function_exists( 'wpas_add_custom_field' ) ) {
    wpas_add_custom_field( 'exbp_invoice_id',  array(
      'title' => __( 'Invoice ID', 'bricks' ),
      'field_type' => 'text',
      'required' => true

    ) );
  }
}

//add this line to your active theme's functions.php or a custom plugin
add_filter('pmpro_register_redirect', '__return_false');

/*
 * Don't show confirm password or email fields on the checkout page.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/ 
 */
add_filter( 'pmpro_checkout_confirm_password', '__return_false' );
add_filter( 'pmpro_checkout_confirm_email', '__return_false' );