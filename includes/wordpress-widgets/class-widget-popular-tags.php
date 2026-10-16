<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Themesflat_Popular_Tags extends WP_Widget {
	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructor
	 *
	 * @return Themesflat_Popular_Tags
	 */
	function __construct() {
		$this->defaults = array(
			'title' => esc_html__( 'Popular Tag', 'ovinx-addon' ),
			'count' => 11,
		);
		parent::__construct(
			'widget_popular_tags',
			esc_html__( 'Themesflat - Popular Tags', 'ovinx-addon' ),
			array(
				'classname'   => 'widget-popular-tags',
				'description' => esc_html__( 'Display popular post tags for the blog sidebar.', 'ovinx-addon' ),
			)
		);
	}

	/**
	 * Display widget
	 */
	function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );
		$title    = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$count    = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 0;

		$tags = get_tags( array(
			'orderby' => 'count',
			'order'   => 'DESC',
			'number'  => $count,
		) );

		if ( empty( $tags ) ) {
			return;
		}

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}
		?>
		<ul class="sb-tag">
			<?php foreach ( $tags as $tag ) : ?>
				<li>
					<a href="<?php echo esc_url( get_tag_link( $tag ) ); ?>" class="text-label">
						<?php echo esc_html( $tag->name ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Update widget
	 */
	function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['count'] = absint( $new_instance['count'] );
		return $instance;
	}

	/**
	 * Widget setting
	 */
	function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'ovinx-addon' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Number of tags to show:', 'ovinx-addon' ); ?></label>
			<input class="widefat" type="number" min="1" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" value="<?php echo esc_attr( $instance['count'] ); ?>">
		</p>
		<?php
	}
}

add_action( 'widgets_init', 'themesflat_register_popular_tags' );

/**
 * Register widget
 *
 * @return void
 * @since 1.0
 */
function themesflat_register_popular_tags() {
	register_widget( 'Themesflat_Popular_Tags' );
}
