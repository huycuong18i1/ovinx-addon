<?php
require_once TF_PLUGIN_PATH . 'woocommerce/popup-builder/popup-builder.php';
require_once TF_PLUGIN_PATH . 'woocommerce/popup-builder/admin.php';

add_action( 'after_setup_theme',function(){
    if ( defined('THEMESFLAT_LINK') ) {
        new TFWC_Popup_Builder();
    }
});
?>