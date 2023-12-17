<?php

/*
* Plugin Name: PHP JIT test for Health Check menu
* Plugin URI: https://wpspeeddoctor.com/
* Description: Adds check for PHP JIT into Health Check menu
* Version: 1.0.0
* Last update: 2023-12-17
* Author: Jaro Kurimsky
* License: GPLv2 or later
* Text Domain: php-jit-test
* Domain Path: /languages/
* Requires at least: 5.1
* Requires PHP: 7.0.0
*/	

defined( 'ABSPATH' ) || exit;

if( !is_admin() ) return;

if( $pagenow === 'site-health.php' && empty( $_GET['tab'] ) ) {
	
	function add_jit_test_tab( $tests ) {
		$tests['async']['jit_enabled_test'] = array(
			'label' => esc_html__( 'JIT enabled test', 'php-jit-test' ),
			'test'  => 'perform_jit_enabled_test',
		);

		return $tests;
	}

	add_filter( 'site_status_tests', 'add_jit_test_tab' );
} 


if( wp_doing_ajax() && ($_POST['action']??'') === 'health-check-perform-jit_enabled_test' ){
	
	require __DIR__.'/ajax-functions.php';

	add_action( 'wp_ajax_health-check-perform-jit_enabled_test', 'perform_jit_enabled_test_ajax' );
}
