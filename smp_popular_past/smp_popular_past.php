<?php 

/**
 * 
 * @link              https://slicemypage.com/
 * @since             1.0.0
 * @package           smp_popular_past
 *
 * @wordpress-plugin
 * Plugin Name:       SMP popular Post
 * Plugin URI:        https://agency1.devsmp.com/
 * Description:       This pluign is used to show popular post by using shortcode, [smp_popular_posts num="" cat=""]. 
 * 					  num, and cat param is optional. by deafult it will show 10 post.
 * Version:           1.0.0
 * Author:            SLICEmyPAGE
 * Author URI:        https://slicemypage.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       smp-popular-past
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'SMP_LATEST_VERSION', '1.0.0' );


function smp_popular_post_by_shortcode($atts, $content = null) {
	extract(shortcode_atts(array('num' => 10,'cat' => '',), $atts)); 
	$temps = explode(',', $cat);
	$array = array();
	foreach ($temps as $temp) $array[] = trim($temp);
	$cats = !empty($cat) ? $array : '';
	?>
	<h3>Popular Posts</h3>
	<ul>
		<?php $popular = new WP_Query(array('posts_per_page' => $num, 'meta_key' => 'popular_posts', 'orderby' => 'meta_value_num', 'order' => 'DESC', 'category__in' => $cats));
		while ($popular->have_posts()) : $popular->the_post(); ?>
		<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
		<?php endwhile; wp_reset_postdata(); ?>
	</ul>
<?php 
}
add_shortcode('smp_popular_posts', 'smp_popular_post_by_shortcode');