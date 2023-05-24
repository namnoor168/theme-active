<?php
/**
 * Displays top navigation
 *
 * @package WordPress
 * @subpackage corona
 * @since 1.0
 * @version 1.0
 */

?>
<nav id="site-navigation" class="main-navigation" aria-label="<?php echo esc_html( 'Primary Menu', 'corona' ); ?>">
<?php
    if( !ftc_has_megamainmenu() ){
        ?>
    <div class="menu-ftc" data-controls="primary-menu"><a><?php esc_html_e( 'Menu', 'corona' ); ?></a></div>
    <?php
    }
    ?>
	<?php wp_nav_menu( array(
		'theme_location' => 'primary',
		'menu_id'        => 'primary-menu',
	) ); ?>
</nav><!-- #site-navigation -->
