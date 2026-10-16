<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class TFWC_Select2_Control extends \Elementor\Control_Select2 {

    public function get_type() {
        return 'tfwc_select2';
    }

    public function __construct() {
        parent::__construct();
    }

    public function enqueue() {
        parent::enqueue();
        wp_enqueue_script('tfwc-select2-script', plugins_url( '../assets/js/tfwc-select2.js', __FILE__ ) , ['jquery'], null , true);
        wp_localize_script('tfwc-select2-script', 'tfwcSelect2Ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('tfwc_select2_nonce')
        ]);
    }

    protected function get_default_settings() {
        return [
            'placeholder' => __('Search...', 'ovinx-addon'),
            'ajax_action' => 'fetch_tfwc_select2_options', // AJAX action
        ];
    }

    /**
	 * Render select2 control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function content_template() {
		?>
		<div class="elementor-control-field">
			<# if ( data.label ) {#>
				<label for="<?php $this->print_control_uid(); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<# } #>
			<div class="elementor-control-input-wrapper elementor-control-unit-5">
				<# var multiple = ( data.multiple ) ? 'multiple' : ''; #>
				<select id="<?php $this->print_control_uid(); ?>" class="elementor-select2 elementor-tfwc-select2" type="select2" {{ multiple }} data-value="{{ data.controlValue }}" data-setting="{{ data.name }}">
					<# _.each( data.options, function( option_title, option_value ) {
						var value = data.controlValue;
						if ( typeof value == 'string' ) {
							var selected = ( option_value === value ) ? 'selected' : '';
						} else if ( null !== value ) {
							var value = _.values( value );
							var selected = ( -1 !== value.indexOf( option_value ) ) ? 'selected' : '';
						}
						#>
					<option {{ selected }} value="{{ _.escape( option_value ) }}">{{{ _.escape( option_title ) }}}</option>
					<# } ); #>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

}
