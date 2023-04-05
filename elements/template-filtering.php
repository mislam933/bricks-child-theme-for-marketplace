<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Element_Bricksplus_Template_Filter extends \Bricks\Element {
  /** 
   * How to create bricksplus elements in Bricks
   * 
   * https://academy.bricksbuilder.io/article/create-your-own-elements
   */
  public $category     = 'bricksplus';
  public $name         = 'bricks-title';
  public $icon         = 'fas fa-anchor'; // FontAwesome 5 icon in builder (https://fontawesome.com/icons)
  public $css_selector = '.bricks-title-wrapper'; // Default CSS selector for all controls with 'css' properties
  // public $scripts      = []; // Enqueue registered scripts by their handle

  public function get_label() {
    return esc_html__( 'Template Filter', 'bricks' );
  }

  public function set_control_groups() {

  }

  public function set_controls() {

  }

  // Enqueue element styles and scripts
  public function enqueue_scripts() {
  	wp_enqueue_script( 'bcm-template-filtering' );
  }

  /** 
   * Render element HTML on frontend
   * 
   * If no 'render_builder' function is defined then this code is used to render element HTML in builder, too.
   */
  public function render() { 
?>
<div class="bcm-template-filtering-wrap">
	<div class="flowers-wrap">
		<div class="flowers-content">
			<div class="bcm-demo-cat-search-wrapper">
				<svg class="bcm-demo-cat-search-icon" width="32" height="32" viewBox="0 0 32 32">
					<path d="M1.125 13.712q0-2.557 0.992-4.89t2.682-4.023 4.023-2.682 4.89-0.992 4.89 0.992 4.023 2.682 2.682 4.023 0.992 4.89q0 3.933-2.217 7.133l6.132 6.132q0.662 0.662 0.662 1.609 0 0.93-0.679 1.609t-1.609 0.679q-0.965 0-1.609-0.679l-6.132-6.114q-3.2 2.217-7.133 2.217-2.557 0-4.89-0.992t-4.022-2.682-2.682-4.023-0.992-4.89zM5.702 13.712q0 3.307 2.351 5.658t5.658 2.351 5.658-2.351 2.351-5.658-2.351-5.658-5.658-2.351-5.658 2.351-2.351 5.658z"></path>
				</svg>
				<input id="bcm-demo-cat-search" class="bcm-demo-cat-search" type="text" placeholder="Search Template">
			</div>
			<?php 
					$args = array(
						'post_type'      => 'bricks-templates',
						'posts_per_page' => -1,
						'post_status' => 'publish'
					);

					$post_terms = array();

					$loop = new \WP_Query($args);

					while ( $loop->have_posts() ) {
						$loop->the_post();
						$terms = get_the_terms( get_the_ID(), 'temlate_cats' ); 
						foreach($terms as $term) {
							$post_terms[$term->name] = $term->slug;
						}
					} wp_reset_query();

			?>
				  <p><strong><?php _e('Topics', 'bricks'); ?></strong></p>
				  <form>
			<?php 
				if (is_array($post_terms) || is_object($post_terms))
				{
				  foreach ($post_terms as $key => $value)
				  {
				     echo '<label><input type="checkbox" name="fl-category" value="'. $value .'" id="'. $value .'" /> '. $key .'</label>';
				  }
				}
			 ?>
		</div>
	</div>

	<div class="flowers">
		<?php 
				$args = array(
					'post_type'      => 'bricks-templates',
					'posts_per_page' => -1,
					'post_status' => 'publish'
				);

				$loop = new \WP_Query($args);
				$the_terms = array();
				while ( $loop->have_posts() ) {
					$loop->the_post();
					$terms = get_the_terms( get_the_ID(), 'temlate_cats' ); 
					foreach($terms as $term) {
						$the_terms[] = $term->slug;
					}
		?>
			  <div class="flower" data-id="aloe" data-category="<?php echo implode(' ', $the_terms); ?>">
			  	<div class="bcm-template-thumb">
						<a href="<?php the_permalink(); ?>" target="_blank">
							<?php the_post_thumbnail(); ?>
						</a>	  
						<div	class="bcm-link-container">
								<a href="<?php the_permalink(); ?>" target="_blank" class="project-details"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="none" d="M0 0h24v24H0z"></path><path d="M16.004 9.414l-8.607 8.607-1.414-1.414L14.589 8H7.004V6h11v11h-2V9.414z"></path></svg></a>	
						</div>	
			  	</div>
			  	<div class="bcm-template-details-wrappre">
			  		<div class="bcm-template-details">
			  			<a href="<?php the_permalink(); ?>" class="template-title"><?php the_title(); ?></a>
			  			<div class="template-meta">
			  				<?php echo ucfirst(implode(', ', $the_terms)); ?>
			  			</div>
			  		</div>
			  	</div>
			  </div>
		<?php
		} wp_reset_query(); ?>
               	
	</div>
</div>
<?php
  }

}