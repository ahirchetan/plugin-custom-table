<?php 
/*
 * @wordpress-plugin
 * Plugin Name:       Custom Tables
 * Plugin URI:        https://wordpress.org/plugins/custom-tables/
 * Description:       this plugin is used to add and remove extra column in post list table from front side
 * Author:            Chetan
 * Author URI:        https://wordpress.org/
 * Version:           1
 * Requires at least: 5.2
 * Requires PHP:      5.6
 * Text Domain:       custom-tables
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */


/**
 * Add default post collumn in the database
 */ 

/**
 * Activate the plugin.
 */
function custom_table_activate() { 
	// Clear the permalinks after active the plugin.
	flush_rewrite_rules(); 
}
register_activation_hook( __FILE__, 'custom_table_activate' );


function custom_table_deactivate() {
	// remove all options into database, so the rules are no longer in memory.

	// Clear the permalinks after deactive the plugin.
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'custom_table_deactivate' );


add_action('table_colunms','post_table');

 function  post_table(){

	$args = array(
			'post_type' => 'post',
		    'order' => 'date',
		    'orderby' => 'title',
		    'numberposts' => -1,
		    'color' => '',
		    'fabric' => '',
		    'category' => ''
    	);

		
	$custom_posts = get_posts( $args );
	if( ! empty( $custom_posts ) ){
	$output = "";
?>

<!-- 
 for column add and remove from post table 
-->

<?php

$output .= '<table id="post_table">';
	$output .= '<thead>';
	$output .= '<tr>';
		$output .= '<th> ID </th>';
		$output .= '<th> Title </th>';
		$output .= '<th> Image </th>';
		$output .= '<th> Except </th>';

		// add column filter to here
		$output .= apply_filters('extended_column_th',array('author'=>'Author','email' => 'Email'));
		$output .= '<th>action</th>';
	$output .= ' </tr> ';
	$output .= ' </thead> ';

	$output .= ' <tbody> ';

			foreach ( $custom_posts as $p ){

		$output .= ' <tr> ';
				$output .= '<td>'.$p->ID.'</td>';
				$output .= '<td>'. $p->post_title . '</td>';
				$output .= '<td> <img src="'. get_the_post_thumbnail_url($p->ID).'" width="100px" height="100px"></td>';
				$output .= '<td>'.$p->post_excerpt.'</td>';

				// add column filter to here
				$output .= apply_filters( 'extended_column', $p->post_author );				
				$output .= '<td><a target="_blank" href="'. get_permalink( $p->ID ) . '"> view </a> </td>';
		$output .= ' </tr> ';
			}
	$output .= ' </tbody> ';
	$output .= ' </table> ';

	}

return print_r($output);
}

/*
/ extend collumn table email and author column
*/
 add_filter('extended_column','table_extend');
 function table_extend($p){
		$output="";
		$output .= '<td>'.get_the_author_meta('display_name', $p).'</td>';
		$output .= '<td>'.get_the_author_meta('user_email', $p).'</td>';
	return $output;
 }


/*
/ extend column table th
*/
 add_filter('extended_column_th','table_th_extend');
 function table_th_extend($arrs){
		$output="";
		foreach ($arrs as $arr => $value) {
			$output .= '<th>'.$value.'</th>';	
		}
	return $output;
 }

/*
* use shortcode in text editor [gest_posts_table]
*/
function shorcode_table(){
	do_action('table_colunms','post_table');
}

/*
* initialize shortcode hook
*/
add_action('init', 'posts_table_shortcodes');
function posts_table_shortcodes(){
	add_shortcode( 'gest_posts_table', 'shorcode_table' );
}
?>