<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Isbank_Gateway_Fields {

	public static function init_fields() {
		return array(
			'enabled'   => array(
				'title' => __( 'Enable', 'wc-isbank' ),
				'type'  => 'checkbox',
				'label' => __( 'Activate the payment method for IsBank', 'wc-isbank' )
			),
			'test'  => array(
				'title' => __( 'Enable test environment', 'wc-isbank' ),
				'type'  => 'checkbox'
			),
			'client_id' => array(
				'title' => __( 'IsBank Customer ID', 'wc-isbank' ),
				'type'  => 'text'
			),
			'store_key' => array(
				'title' => __( '3D Secret', 'wc-isbank' ),
				'type'  => 'text'
			),
			'api_user'  => array(
				'title' => __( 'API Username', 'wc-isbank' ),
				'type'  => 'text'
			),
			'api_user_password' => array(
				'title' => __( 'API User password', 'wc-isbank' ),
				'type'  => 'password'
			),
			'currency' => array(
				'title' => __('Currency','wc-isbank'),
				'type'=>'select',
				'options'=>[
					'949'=>'TRY',
					'840'=>'USD',	
				],
				'default'=>'840'
			),
			'instalment'=>array(
				'title'=> __('Instalment','wc-isbank'),
				'type'=>'checkbox'	
			)
		);
	}

}
