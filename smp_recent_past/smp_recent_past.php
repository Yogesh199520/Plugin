<?php 

/**
 * 
 * @link              https://slicemypage.com/
 * @since             1.0.0
 * @package           smp_recent_past
 *
 * @wordpress-plugin
 * Plugin Name:       SMP Recent Post
 * Plugin URI:        https://agency1.devsmp.com/
 * Description:       This pluign is used to show recent post by using shortcode, [smp_recent_posts num="" cat="" order="" orderby=""]. 
 * 					  num, cat, order, and orderby param is optional. by deafult it will show 5 post.
 * Version:           1.0.0
 * Author:            SLICEmyPAGE
 * Author URI:        https://slicemypage.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       smp-recent-past
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'SMP_LATEST_VERSION', '1.0.0' );


function smp_recent_post_by_shortcode($atts, $content = null) {
	global $post;
	extract(shortcode_atts(array('cat'=> '','num'=> '5','order'=>'DESC','orderby'=>'post_date',), $atts));
	$smp_args = array('cat'=>$cat,'posts_per_page'=>$num,'order'=> $order,'orderby'=> $orderby,);
	$smp_output = '';
	$smp_posts = get_posts($smp_args);
	foreach($smp_posts as $post) {
		setup_postdata($post);
		$smp_output .= '<li><a href="'. get_the_permalink() .'">'. get_the_title() .'</a><span>'.get_the_date("d/m/Y").'</span></li>';
	}
	wp_reset_postdata();
	return '<ul>'. $smp_output .'</ul>';
}
add_shortcode('smp_recent_posts', 'smp_recent_post_by_shortcode');