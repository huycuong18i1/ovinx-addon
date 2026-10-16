<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( is_user_logged_in() ) { 
	
	$current_user = wp_get_current_user();
	$user_name = $current_user->user_login;
	?>
	<div class="offcanvas offcanvas-end canvas-account <?php if ( is_user_logged_in() && is_admin_bar_showing() ) { echo 'is-admin';} ?>" id="log">
	<div class="canvas-wrapper">

		<div class="canvas-header">
			<h6 class="title-pop"><?php echo esc_html( $user_name ); ?></h6>
			<button type="button" class="btn-close-popup" data-bs-dismiss="offcanvas">
				<i class="icon icon-Close2"></i>
			</button>
		</div>

		<div class="canvas-body">
			<ul class="list-menu">
				<?php
				$menu_items = [
					'dashboard' => [
						'url' => get_permalink( wc_get_page_id( 'myaccount' ) ),
						'label' => esc_html__('Dashboard','ovinx-addon')
					],
					'orders' => [
						'url' => wc_get_account_endpoint_url( 'orders' ),
						'label' => esc_html__('My Orders','ovinx-addon'),
						'endpoint' => 'orders'
					],
					'downloads' => [
						'url' => wc_get_account_endpoint_url( 'downloads' ),
						'label' => esc_html__('Downloads','ovinx-addon'),
						'endpoint' => 'downloads'
					],
					'addresses' => [
						'url' => wc_get_account_endpoint_url( 'edit-address' ),
						'label' => esc_html__('Addresses','ovinx-addon'),
						'endpoint' => 'edit-address'
					],
					'account_details' => [
						'url' => wc_get_account_endpoint_url( 'edit-account' ),
						'label' => esc_html__('Account Details','ovinx-addon'),
						'endpoint' => 'edit-account'
					],
					'logout' => [
						'url' => wp_logout_url(),
						'label' => esc_html__('Log Out','ovinx-addon')
					]
				];

				$current_url = home_url( add_query_arg( null, null ) );
				foreach ( $menu_items as $item ) {
					$is_active = '';
					
					if ( $item['url'] === get_permalink( wc_get_page_id( 'myaccount' ) ) && is_account_page() && !is_wc_endpoint_url() ) {
						$is_active = 'active';
					}
					
					elseif ( isset( $item['endpoint'] ) && is_wc_endpoint_url( $item['endpoint'] ) ) {
						$is_active = 'active';
					}
					
					elseif ( $current_url === esc_url( $item['url'] ) ) {
						$is_active = 'active';
					}

					echo '<li class="' . $is_active . '"><a href="' . esc_url( $item['url'] ) . '">' . $item['label'] . '</a></li>';
				}
				?>
			</ul>
		</div>
	</div>
</div>
<?php } ?>
