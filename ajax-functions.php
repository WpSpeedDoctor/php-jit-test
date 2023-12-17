<?php

defined( 'ABSPATH' ) || exit;

function perform_jit_enabled_test_ajax() {

	$status_jit = get_jit_status();

	$status_markup = $status_jit['success'] ? 'good' : 'recommended';

    $highlight_color = $status_jit['success'] ? 'blue' : 'orange';

	$result = array(
		'label'       => esc_html__( 'PHP JIT (Just in Time) compiler helps run code faster', 'php-jit-test' ),
		'status'      => $status_markup,
		'badge'       => array(
			'label' => esc_html__( 'Performance', 'php-jit-test' ),
			'color' => $highlight_color,
		),
		'description' => "<p>{$status_jit['message']}</p>",
		'actions'     => '',
		'test'        => 'jit_enabled_test',
	);

	wp_send_json_success( $result );
}


/**
* @return array -
* - success => bool
* - message => string
*/

function get_jit_status(){
	
	switch(true){

		case !is_php_version_with_jit():
			
			$status = 'insufficient-php-version';
		break;			
		
		case !function_exists("opcache_get_status"):
			
			$status = 'no-opcache';
		break;
		
		case is_all_parameters_ok( opcache_get_status()["jit"] ):
			
			$status = 'success';
		break;
		
		case !is_jit_enabled( opcache_get_status()["jit"] ):
			
			$status = 'not-enabled';
		break;
		
		default:

			$status = 'no-buffer';
		break;	
	}

	return get_responses()[$status];

}

function is_all_parameters_ok( $jit_data ){

	return 
		
		$jit_data['enabled'] == '1' &&
	
		$jit_data['on'] == '1' && 
		
		$jit_data['buffer_size'] > 0;
}

function is_jit_enabled( $jit_data ){

	return 
		
		$jit_data['enabled'] == '1' &&
	
		$jit_data['on'] == '1';
}

function is_buffer_size_set( $jit_data ){

	return $jit_data['buffer_size'] > 0;
}

function is_php_version_with_jit() {

    return version_compare(PHP_VERSION, '8.0.0', '>=');
}

function get_responses(){

	return [
		'no-opcache' => [

			'success' => false,

			'message' => esc_html__( 'OPcache is not on. In order to have JIT you need to have OPcache on!', 'php-jit-test' ),
		],
		
		'success' => [
			
			'success' => true,
			
			'message' => esc_html__( 'JIT is enabled', 'php-jit-test' )
			
		],
		
		'not-enabled' => [
			
			'success' => false,
			
			'message' => esc_html__( 'JIT is NOT enabled', 'php-jit-test' )
		],
		
		'no-buffer' => [
			
			'success' => false,
			
			'message' => esc_html__( 'JIT is enabled but the size buffer is set to zero', 'php-jit-test' )
			
	
		],
		
		'insufficient-php-version' => [
			
			'success' => false,
			
			'message' => esc_html__( 'Version of PHP you\'re using has no JIT', 'php-jit-test' )
			
	
		],

	];
}