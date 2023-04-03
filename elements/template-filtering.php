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
				  <p><strong><?php _e('Category', 'bricks'); ?></strong></p>
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