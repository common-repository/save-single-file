<?php
/*
Plugin Name: Save Single File
Description: It saves the page as single file
Author: Jose Mortellaro
Author URI: https://josemortellaro.com/
Text Domain: eos-ssf
Domain Path: /languages/
Version: 1.0.1
*/
/*  This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
 
//Definitions
define( 'EOS_SINGLE_FILE_VERSION','1.0.1' );


add_action( 'admin_bar_menu','eos_ssf_top_bar',999999);
// Add download link to admin top  bar
function eos_ssf_top_bar( $wp_admin_bar ){
	if( eos_ssf_remove_admin_menu() ){
		$all_toolbar_nodes = $wp_admin_bar->get_nodes();
		foreach ( $all_toolbar_nodes as $node ) {
			$wp_admin_bar->remove_node( $node->id );
		}
	}
	$wp_admin_bar->add_menu( array(
		'id'    => 'eos-ssf-menu',
		'title' => !eos_ssf_remove_admin_menu()  ? '<span class="dashicons dashicons-download" style="font-family:dashicons"></span> '.__( 'Single File','eos-sss' ) : __( 'Exported by Save Single File','eos-sss' ),
		'href' => '#ssf-strip_menu',
	) );
	if( !eos_ssf_remove_admin_menu() ){
		$wp_admin_bar->add_menu( array(
			'id'    => 'eos-ssf-strip-menu',
			'parent' => 'eos-ssf-menu',
			'title' => '<span> '.__( 'Strip the admin menu before saving.','eos-sss' ).'</span>',
			'href' => '#',
		) );				
		$wp_admin_bar->add_menu( array(
			'id'    => 'eos-ssf-keep-menu',
			'parent' => 'eos-ssf-menu',
			'title' => '<span> '.__( 'Save including the admin menu.','eos-sss' ).'</span>',
			'href' => '#',
		) );
	}
	return $wp_admin_bar;
}

add_action( 'admin_footer','eos_ssf_top_bar_script',999999 );
add_action( 'wp_footer','eos_ssf_top_bar_script',999999 );
//Add script after admin top bar
function eos_ssf_top_bar_script( $wp_admin_bar ){
	if( !function_exists( 'is_admin_bar_showing' ) || !is_admin_bar_showing() ) return;
	?>
	<script>
		var eos_ssf_li = document.getElementById("wp-admin-bar-eos-ssf-menu"),eos_ssf_title = document.getElementsByTagName("title");
		if(eos_ssf_li){
			eos_ssf_title = eos_ssf_title ? eos_ssf_title[0].innerHTML : "single-file-" + Date.now();
			var eos_ssf_download_link = eos_ssf_li.getElementsByTagName("a")[0];
				eos_ssf_download_link.setAttribute( "download", eos_ssf_title);
				if(window.location.search.indexOf('ssf=strip_menu') >0){
					eos_ssf_download_link.click();
					window.location.href = window.location.pathname + window.location.search.replace('&ssf=strip_menu','').replace('?ssf=strip_menu','');
				}
				document.getElementById('wp-admin-bar-eos-ssf-keep-menu').addEventListener('click',function(){
					eos_ssf_download_link.click();
				
				});
				document.getElementById('wp-admin-bar-eos-ssf-strip-menu').addEventListener('click',function(){
					var extra_search = window.location.search.indexOf('&') > 0 ? '&ssf=strip_menu' : '?ssf=strip_menu',
						href = window.location.pathname + window.location.search.replace('&ssf=strip_menu','').replace('?ssf=strip_menu','') + extra_search;
					if(href.split('?').length > 2){
						href = href.replace('?ssf=strip_menu','&ssf=strip_menu');
					}
					window.location.href = href;

				});
		}
	</script>
	<?php
	return $wp_admin_bar;
}

if( isset( $_GET['ssf'] ) && 'strip_menu' === esc_attr( $_GET['ssf'] ) ){
	add_action( 'admin_menu', 'eos_ssf_remove_admin_menu_items', 999 );
}
//Remove admin menu
function eos_ssf_remove_admin_menu_items() {
	global $menu,$submenu;
	foreach( $menu as &$menu_item ){
		$menu_item = array();
	}
	return;
}

//Check if the admin menu and top bar should be removed
function eos_ssf_remove_admin_menu(){
	return ( isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) && false !== strpos( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],'ssf-strip-menu' ) ) || ( isset( $_GET['ssf'] ) && 'strip_menu' === esc_attr( $_GET['ssf'] )  );
}