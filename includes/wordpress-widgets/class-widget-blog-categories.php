<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Themesflat_Blog_Categories extends WP_Widget {
	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructor
	 *
	 * @return Themesflat_Blog_Categories
	 */
	function __construct() {
		$this->defaults = array(
			'title'      => esc_html__( 'Categories', 'ovinx-addon' ),
			'count'      => 0,
			'hide_empty' => true,
		);
		parent::__construct(
			'widget_blog_categories',
			esc_html__( 'Themesflat - Blog Categories', 'ovinx-addon' ),
			array(
				'classname'   => 'widget-blog-categories',
				'description' => esc_html__( 'Display post categories for the blog sidebar.', 'ovinx-addon' ),
			)
		);
	}

	/**
	 * Display widget
	 */
	function widget( $args, $instance ) {
		$instance   = wp_parse_args( $instance, $this->defaults );
		$title      = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$count      = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 0;
		$hide_empty = isset( $instance['hide_empty'] ) ? (bool) $instance['hide_empty'] : true;

		$categories = get_categories( array(
			'orderby'    => 'name',
			'hide_empty' => $hide_empty,
			'number'     => $count,
		) );

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}
		?>
		<ul class="sb-category">
			<?php if ( ! empty( $categories ) ) : ?>
				<?php foreach ( $categories as $category ) : ?>
					<li>
						<a href="<?php echo esc_url( get_category_link( $category ) ); ?>"
						   class="d-flex align-items-center justify-content-between link text-body-03 fw-medium">
							<?php echo esc_html( $category->name ); ?>
							<span class="count">
								(<?php echo esc_html( $category->count ); ?>)
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			<?php else : ?>
				<li><?php esc_html_e( 'No categories found.', 'ovinx-addon' ); ?></li>
			<?php endif; ?>
		</ul>
		<?php
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Update widget
	 */
	function update( $new_instance, $old_instance ) {
		$instance               = $old_instance;
		$instance['title']      = strip_tags( $new_instance['title'] );
		$instance['count']      = absint( $new_instance['count'] );
		$instance['hide_empty'] = isset( $new_instance['hide_empty'] ) ? (bool) $new_instance['hide_empty'] : false;
		return $instance;
	}

	/**
	 * Widget setting
	 */
	function form( $instance ) {
		$instance   = wp_parse_args( $instance, $this->defaults );
		$hide_empty = isset( $instance['hide_empty'] ) ? (bool) $instance['hide_empty'] : true;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'ovinx-addon' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Number of categories to show (0 = all):', 'ovinx-addon' ); ?></label>
			<input class="widefat" type="number" min="0" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" value="<?php echo esc_attr( $instance['count'] ); ?>">
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $hide_empty ); ?> id="<?php echo esc_attr( $this->get_field_id( 'hide_empty' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hide_empty' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'hide_empty' ) ); ?>"><?php esc_html_e( 'Hide categories with no posts?', 'ovinx-addon' ); ?></label>
		</p>
		<?php
	}
}

add_action( 'widgets_init', 'themesflat_register_blog_categories' );

/**
 * Register widget
 *
 * @return void
 * @since 1.0
 */
function themesflat_register_blog_categories() {
	register_widget( 'Themesflat_Blog_Categories' );
}
