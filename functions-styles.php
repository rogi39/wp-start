<?php
add_action('wp_enqueue_scripts', 'site_scripts');

function site_scripts(){
	$ver = '1.0.0';
	wp_enqueue_style('main-css', get_template_directory_uri() . '/css/app.min.css', array(), $ver);

	wp_deregister_script('wp-embed');
	wp_deregister_style('wp-block-library');

	// wp_enqueue_script( 'reacaptcha_js', 'https://www.google.com/recaptcha/api.js?render=6LfdrnwaAAAAABDn2Il7mXGDJuqnRIwyXsGV-3YS', '', '', true);
	wp_enqueue_script('main-js', get_template_directory_uri() . '/js/app.min.js', array(), $ver, true);

}