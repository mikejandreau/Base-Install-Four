<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Base_Install
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area">
	<h2 class="screen-reader-text">Sidebar</h2>
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
