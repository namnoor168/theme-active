<?php 
global $ftc_default_sidebars, $ftc_default_widgetareas;

$ftc_default_sidebars = array(
	array(
		'name' => esc_html__( 'Home Sidebar', 'corona' ),
		'id' => 'home-sidebar',
		'description' => '',
		'before_widget' => '<section id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<div class="widget-title-wrapper"><a class="block-control" href="javascript:void(0)"></a><h3 class="widget-title heading-title">',
		'after_title' => '</h3></div>',
	)
	,array(
		'name' => esc_html__( 'Blog Sidebar', 'corona' ),
		'id' => 'blog-sidebar',
		'description' => '',
		'before_widget' => '<section id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<div class="widget-title-wrapper"><a class="block-control" href="javascript:void(0)"></a><h3 class="widget-title heading-title">',
		'after_title' => '</h3></div>',
	)
	,array(
		'name' => esc_html__( 'Blog Detail Sidebar', 'corona' ),
		'id' => 'blog-detail-sidebar',
		'description' => '',
		'before_widget' => '<section id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<div class="widget-title-wrapper"><a class="block-control" href="javascript:void(0)"></a><h3 class="widget-title heading-title">',
		'after_title' => '</h3></div>',
	)
	,array(
		'name' => esc_html__( 'Product Category Sidebar', 'corona' ),
		'id' => 'product-category-sidebar',
		'description' => '',
		'before_widget' => '<section id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<div class="widget-title-wrapper"><a class="block-control" href="javascript:void(0)"></a><h3 class="widget-title heading-title">',
		'after_title' => '</h3></div>',
	)
	,array(
		'name' => esc_html__( 'Product Category Top Content', 'corona' ),
		'id' => 'product-category-top-content',
		'description' => '',
		'before_widget' => '<section id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<div class="widget-title-wrapper"><a class="block-control" href="javascript:void(0)"></a><h3 class="widget-title heading-title">',
		'after_title' => '</h3></div>',
	)
	,array(
		'name' => esc_html__( 'Product Detail Sidebar', 'corona' ),
		'id' => 'product-detail-sidebar',
		'description' => '',
		'before_widget' => '<section id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<div class="widget-title-wrapper"><a class="block-control" href="javascript:void(0)"></a><h3 class="widget-title heading-title">',
		'after_title' => '</h3></div>',
	)

	,array(
		'name'          => esc_html__( 'Product Detail Social Icon', 'corona' ),
		'id'            => 'product-detail-social-icon',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'description'   => esc_html__( 'Add social icon in your product detail page.', 'corona' ),
	)
	,array(

		'name'  => esc_html__( 'List Categories Breadcrumbs', 'corona' ),
		'id'            => 'list-categories-breadcrumbs',

		'before_widget' => '<div id="%1$s" class="widget %2$s">',

		'after_widget' => '</div>',
		'description'   => '',

	)
	,array(
		'name'          => esc_html__( 'Popup Newletter', 'corona' ),
		'id'            => 'popup-newletter',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'description'   => '',
	)
);

$ftc_default_widgetareas = array(
	array(
		'name'          => esc_html__( 'Footer Top', 'corona' ),
		'id'            => 'footer-top',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'description'   => esc_html__( 'Add widgets here to appear in your footer.', 'corona' ),
	)
	,array(
		'name'          => esc_html__( 'Footer Middle', 'corona' ),
		'id'            => 'footer-middle',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'description'   => esc_html__( 'Add widgets here to appear in your footer.', 'corona' ),
	)
	,array(
		'name'          => esc_html__( 'Footer Bottom', 'corona' ),
		'id'            => 'footer-bottom',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'description'   => esc_html__( 'Add widgets here to appear in your footer.', 'corona' ),
	)
);

$custom_sidebars = ftc_get_custom_sidebars();
if( is_array($custom_sidebars) && !empty($custom_sidebars) ){
	foreach( $custom_sidebars as $name ){
		$ftc_default_sidebars[] = array(
			'name' => ''.$name.'',
			'id' => sanitize_title($name),
			'description' => '',
			'class'			=> 'ftc-custom-sidebar',
			'before_widget' => '<section id="%1$s" class="widget-container %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<div class="widget-title-wrapper"><a class="block-control" href="javascript:void(0)"></a><h3 class="widget-title heading-title">',
			'after_title' => '</h3></div>',
		);
	}
}

function ftc_register_widget_area(){
	global $ftc_default_sidebars, $ftc_default_widgetareas;
	$default_sidebar = array_merge($ftc_default_sidebars, $ftc_default_widgetareas);
	foreach( $default_sidebar as $sidebar ){
		register_sidebar($sidebar);
	}
}
add_action( 'widgets_init', 'ftc_register_widget_area' );
?>