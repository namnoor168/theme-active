<?php
/*************************************************
* WooCommerce Custom Hook                        *
**************************************************/

/*** Shop - Category ***/

/* Remove hook */
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
remove_action( 'woocommerce_product_tabs', 'dokan_set_more_from_seller_tab', 10 );
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);

remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);

remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);

remove_action('woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10);
remove_action('woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10);


/* Add new hook */
/*Shop Variation*/
function ftc_template_loop_variation() {
	global $smof_data;
	$enableVariation = isset($smof_data['ftc_variation_product_shop']) && $smof_data['ftc_variation_product_shop'];
	if ((is_tax('product_cat') && $enableVariation) || (is_shop() && $enableVariation) || (is_post_type_archive('product') && $enableVariation)) {
		add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_single_add_to_cart', 11);
	}
}
if( isset($smof_data['ftc_variation_product_shop']) && $smof_data['ftc_variation_product_shop']){
	add_filter('body_class',function($classes){
		return array_merge($classes, array('ftc-variation'));
	});
}
add_action('woocommerce_after_shop_loop_item', 'ftc_template_loop_variation', 80);
add_action('woocommerce_before_shop_loop_item_title', 'ftc_template_loop_product_thumbnail', 10);
add_action('woocommerce_after_shop_loop_item_title', 'ftc_template_loop_product_label', 1);

add_action('woocommerce_after_shop_loop_item', 'ftc_template_loop_categories', 10);
add_action('woocommerce_after_shop_loop_item', 'ftc_template_loop_product_title', 20);
add_action('woocommerce_after_shop_loop_item', 'ftc_template_loop_product_sku', 30);
add_action('woocommerce_after_shop_loop_item', 'ftc_template_loop_short_description', 40); 
add_action('woocommerce_after_shop_loop_item', 'ftc_template_loop_short_description_listview', 45); 
if( $smof_data['ftc_prod_price'] && isset($smof_data['ftc_prod_price']) ){
	add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 50);
}
add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 60);
if( $smof_data['ftc_prod_add_to_cart'] && isset($smof_data['ftc_prod_add_to_cart'])){
	add_action('woocommerce_after_shop_loop_item', 'ftc_template_loop_add_to_cart', 70); 
}
add_action('woocommerce_before_shop_loop', 'ftc_shop_top_content_widget_area_button', 25);

add_filter('loop_shop_per_page', 'ftc_change_products_per_page_shop' ); 
add_filter('woocommerce_product_get_rating_html', 'ftc_get_empty_rating_html', 10, 2);


function ftc_product_get_availability(){
	global $product;
	$availability = $class = '';
	if ( $product->managing_stock() ) {
		if ( $product->is_in_stock() && $product->get_stock_quantity() > get_option( 'woocommerce_notify_no_stock_amount' ) ) {
			switch ( get_option( 'woocommerce_stock_format' ) ) {
				case 'no_amount' :
				$availability = esc_html__( 'In stock', 'corona' );
				break;
				case 'low_amount' :
				if ( $product->get_stock_quantity() <= get_option( 'woocommerce_notify_low_stock_amount' ) ) {
					$availability = sprintf( esc_html__( 'Only %s left in stock', 'corona' ), $product->get_stock_quantity() );
					if ( $product->backorders_allowed() && $product->backorders_require_notification() ) {
						$availability .= ' ' . esc_html__( '(can be backordered)', 'corona' );
					}
				} else {
					$availability = esc_html__( 'In stock', 'corona' );
				}
				break;
				default :
				$availability = sprintf( esc_html__( '%s in stock', 'corona' ), $product->get_stock_quantity() );
				if ( $product->backorders_allowed() && $product->backorders_require_notification() ) {
					$availability .= ' ' . esc_html__( '(can be backordered)', 'corona' );
				}
				break;
			}
			$class        = 'in-stock';
		} elseif ( $product->backorders_allowed() && $product->backorders_require_notification() ) {
			$availability = esc_html__( 'Available on backorder', 'corona' );
			$class        = 'available-on-backorder';
		} elseif ( $product->backorders_allowed() ) {
			$availability = esc_html__( 'In stock', 'corona' );
			$class        = 'in-stock';
		} else {
			$availability = esc_html__( 'Out of stock', 'corona' );
			$class        = 'out-of-stock';
		}
	} elseif ( ! $product->is_in_stock() ) {
		$availability = esc_html__( 'Out of stock', 'corona' );
		$class        = 'out-of-stock';
	}

	return array( 'availability' => $availability, 'class' => $class );
}


/* readmore - readless - single product */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
add_action( 'woocommerce_single_product_summary', 'ftc_template_single_excerpt', 5 );

function ftc_template_single_excerpt(){
	$the_excerpt = get_the_excerpt('');
	$count_des = mb_strlen( $the_excerpt , 'UTF-8');
	if ($count_des >180){
		?>
		<div class="ftc_excerpt">
			<div class="collapsed-content">
				<p>
					<?php  global $more;
					$temp = $more;
					$more = false;
					$short_description = get_the_excerpt('');
					$short_descript = substr($short_description, 0, 130). '...';          
					print_r($short_descript);
					$more = $temp;
					?>
				</p>
			</div>
			<div class="full-content">
				<?php $full_description = the_excerpt(); ?>
			</div>
			<a href="#" id="readMore">Read more</a>
			<a href="#" id="readless" style="display: none">Read less</a>
		</div>
		<?php
	}
	else{
		echo  '<div class="collapsed-content">' ;
		echo '<p>'.$the_excerpt. '</p>';
		echo '</div>';
	}
}

if( $smof_data['ftc_prod_cross_sell'] && isset($smof_data['ftc_prod_cross_sell']) ){
	add_action('woocommerce_after_single_product_summary', 'show_cross_sell_in_single_product', 30);
}
function show_cross_sell_in_single_product(){
	$crosssells = get_post_meta( get_the_ID(), '_crosssell_ids',true);

	if(empty($crosssells)){
		return;
	}

	$args = array(
		'post_type' => 'product',
		'posts_per_page' => -1,
		'post__in' => $crosssells
	);
	$products = new WP_Query( $args );
	if( $products->have_posts() ) :
		echo '<div class="ftc-cross-sells"><h2>'.esc_html__('Cross-Sells Products', 'corona').'</h2>';
		woocommerce_product_loop_start();
		while ( $products->have_posts() ) : $products->the_post();
			wc_get_template_part( 'content', 'product' );
endwhile; // end of the loop.
woocommerce_product_loop_end();
echo '</div>';
endif;
wp_reset_postdata();
}

/*Custom Desciption in Tab*/
add_filter( 'woocommerce_product_tabs', 'woo_custom_description_tab', 98 );
function woo_custom_description_tab( $tabs ) {

	$tabs['description']['callback'] = 'woo_custom_description_tab_content';
        // Custom description callback
	$tabs['description']['title'] = esc_html__( 'Description','corona' );
	$tabs['description']['priority'] = 10;
	return $tabs;
}
function woo_custom_description_tab_content($tab) {

	$short_description = get_the_content('');
	$count_des = mb_strlen( $short_description , 'UTF-8');
	if ($count_des >110){
		?>
		<div class="ftc_desciption_tab">
			<div class="desciption_content">     
				<?php  global $more;
				$temp = $more;
				$more = false;
				$short_description = get_the_content('');
				$short_descript = substr($short_description, 0, 220). '...';
				print_r($short_descript);
				$more = $temp;
				?>
			</div>
			<div class="description_fullcontent">
				<?php $full_description = the_content(); ?>
			</div>
			<a href="#" id="readmore_des">Read more</a>
			<a href="#" id="readless_des" style="display: none">Read less</a>
		</div>
		<?php
	}
	else{
		print_r($short_description);
	}
}
/*ENd*/

/* Infinite-Shop*/
function ftc_loadmore_shop() {
	if( get_next_posts_link() ) {
		?>
		<button class="onewave-products-load-more hidden">
			<span class="load-more"><?php esc_html_e('Load more', 'corona'); ?></span>
		</button> 
		<span class="page-load-status">
			<p class="infinite-scroll-request"><?php esc_html_e('Loading ', 'corona'); ?></p>
			<p class="infinite-scroll-last"><?php esc_html_e('No Products for load', 'corona'); ?></p>
		</span>
		<?php
	}
}
add_action('woocommerce_after_shop_loop', 'ftc_loadmore_shop');




function ftc_template_product_size_chart_button() {
    if (wp_is_mobile()) {
        return;
    }
    global $smof_data, $product;

    $size_chart_image_options = isset($smof_data['ftc_prod_size_chart']['url']) ? esc_url($smof_data['ftc_prod_size_chart']['url']) : '';
    $size_chart_image_product = get_post_meta($product->get_id(), 'ftc_prod_size_chart', true);

    if (!empty($size_chart_image_product) || !empty($size_chart_image_options) || (isset($smof_data['ftc_content_sizechart']) && $smof_data['ftc_content_sizechart'])) {
        $ajax_url = admin_url('admin-ajax.php', is_ssl() ? 'https' : 'http') . '?ajax=true&action=load_product_size_chart&product_id=' . $product->get_id();
        echo '<a class="ftc-size_chart" href="' . esc_url($ajax_url) . '"><svg aria-hidden="true" role="img" focusable="false" width="22" height="16" viewBox="0 0 22 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M7.53822 7.78217C9.35978 7.78217 10.8453 6.69132 10.8453 5.3549C10.8453 4.01832 9.36419 2.92747 7.53822 2.92747C5.71224 2.92747 4.23112 4.01832 4.23112 5.3549C4.23112 6.69132 5.71666 7.78217 7.53822 7.78217ZM7.53822 3.78967C8.86449 3.78967 9.96102 4.50547 9.96102 5.35474C9.96102 6.20417 8.85125 6.91982 7.53822 6.91982C6.22503 6.91982 5.11525 6.20417 5.11525 5.35474C5.11525 4.50547 6.22503 3.78536 7.53822 3.78536V3.78967Z"></path><path d="M21.5579 9.83887H15.0321V5.35045C15.0321 2.40144 11.6676 0 7.51614 0C3.36449 0 0 2.40144 0 5.35045V10.6537C0 13.3699 2.85171 15.6119 6.52573 15.9525H6.60095C6.90591 15.9785 7.21544 16 7.52939 16H21.5579C21.8019 16 22 15.8069 22 15.5688V10.2699C22 10.032 21.8019 9.83887 21.5579 9.83887ZM7.53822 0.866498C11.1856 0.866498 14.1701 2.87997 14.1701 5.35491C14.1701 7.8297 11.2034 9.84317 7.53822 9.84317C3.87302 9.84317 0.906365 7.8297 0.906365 5.35476C0.906365 2.87997 3.8951 0.866498 7.53822 0.866498ZM14.1701 7.86845V9.83887H11.6057C12.6066 9.39647 13.4799 8.71957 14.1478 7.86845H14.1701ZM21.1157 15.1377H20.2314V12.956C20.2314 12.7181 20.0334 12.5248 19.7894 12.5248C19.5453 12.5248 19.3473 12.7179 19.3473 12.956V15.142H17.5787V13.9562C17.5787 13.7182 17.3807 13.5251 17.1366 13.5251C16.8925 13.5251 16.6944 13.7182 16.6944 13.9562V15.142H14.926V13.9562C14.926 13.7182 14.728 13.5251 14.4839 13.5251C14.2398 13.5251 14.0417 13.7182 14.0417 13.9562V15.142H12.2731V12.956C12.2731 12.7181 12.0751 12.5248 11.831 12.5248C11.5871 12.5248 11.3888 12.7179 11.3888 12.956V15.142H9.69564V13.9562C9.69564 13.7182 9.49743 13.5251 9.2535 13.5251C9.0094 13.5251 8.81135 13.7182 8.81135 13.9562V15.142H7.5379C7.36997 15.142 7.20645 15.142 7.04277 15.142V13.9562C7.04277 13.7182 6.84472 13.5251 6.60063 13.5251C6.35654 13.5251 6.15849 13.7182 6.15849 13.9562V15.0385C3.17448 14.6073 0.92844 12.8051 0.92844 10.6494V7.86845C2.1929 9.55424 4.6821 10.7011 7.56029 10.7011H21.1157V15.1377Z"></path></svg> ' . esc_html__('Size Chart','corona') . '</a>';
    }
}


function ftc_load_product_size_chart_callback() {
    global $smof_data, $product;
    if (empty($_GET['product_id'])) {
        wp_die('Invalid Products');
    }

    $prod_id = absint($_GET['product_id']);

    if ($prod_id <= 0) {
        wp_die('Invalid Products');
    }

    $size_chart_image_options = isset($smof_data['ftc_prod_size_chart']['url']) ? esc_url($smof_data['ftc_prod_size_chart']['url']) : '';

    $size_chart_image_product = get_post_meta($prod_id, 'ftc_prod_size_chart', true);
    ob_start();
    if (!empty($size_chart_image_product) && $size_chart_image_product) {
        echo '<div class="product-size-chart">
        <img src="'.esc_url($size_chart_image_product).'"/>
        </div>';
    }
    elseif(isset($smof_data['ftc_content_sizechart']) && $smof_data['ftc_content_sizechart']) { ?>
        <div class="product-size-chart">
            <?php if(isset($smof_data['ftc_content_sizechart']) && $smof_data['ftc_content_sizechart']){ ?>
                <div class="size_chart_table">
                    <?php echo wp_kses_post( do_shortcode($smof_data['ftc_content_sizechart']) ); ?>
                </div>
            <?php } ?>
        </div>
    <?php }
    elseif (!empty($size_chart_image_options) && $size_chart_image_options) {
        echo '<div class="product-size-chart"><img src="'.esc_url($size_chart_image_options).'"/></div>';
    }
    wp_die(ob_get_clean());
}

function ftc_template_loop_product_label(){
	global $product, $post, $smof_data;
	$out_of_stock = false;
	$product_stock = ftc_product_get_availability();
	if( isset($product_stock['class']) && $product_stock['class'] == 'out-of-stock' ){
		$out_of_stock = true;
	}
	?>
	<div class="conditions-box">
		<?php 
		/* Sale label */
		if( $product->is_on_sale() && !$out_of_stock ){ 
			if( $product->get_regular_price() > 0 && isset($smof_data['ftc_show_sale_label_as']) && $smof_data['ftc_show_sale_label_as'] != 'text' ){
				$_off_percent = (1 - round($product->get_price() / $product->get_regular_price(), 2))*100;
				$_off_price = round($product->get_regular_price() - $product->get_price(), 0);
				$_price_symbol = get_woocommerce_currency_symbol();
				if( isset($smof_data['ftc_show_sale_label_as']) && $smof_data['ftc_show_sale_label_as'] == 'number' ){
					
					$symbol_pos = get_option('woocommerce_currency_pos', 'left');
					$price_display = '';
					switch( $symbol_pos ){
						case 'left':
						$price_display = '-'.$_price_symbol.$_off_price;
						break;
						case 'right':
						$price_display = '-'.$_off_price.$_price_symbol;
						break;
						case 'left_space':
						$price_display = '-'.$_price_symbol.' '.$_off_price;
						break;
						default: /* right_space */
						$price_display = '-'.$_off_price.' '.$_price_symbol;
						break;
					}
					
					echo '<span class="onsale amount" data-original="'.$price_display.'">'.$price_display.'</span>';
				}
				if( isset($smof_data['ftc_show_sale_label_as']) && $smof_data['ftc_show_sale_label_as'] == 'percent' ){
					echo '<span class="onsale percent">-'.$_off_percent.'%</span>';
				}
			}
			else{
				echo '<span class="onsale">'.esc_html(stripslashes($smof_data['ftc_product_sale_label_text'])).'</span>';
			}
			
		}
		
		/* Hot label */
		if( $product->is_featured() && !$out_of_stock ){
			echo '<span class="featured">'.esc_html(stripslashes($smof_data['ftc_product_feature_label_text'])).'</span>';
		}
		
		/* Out of stock */
		if( $out_of_stock ){
			echo '<span class="out-of-stock">'.esc_html(stripslashes($smof_data['ftc_product_out_of_stock_label_text'])).'</span>';
		}
		?>
	</div>
	<?php
}

function ftc_template_loop_product_thumbnail(){
	global $product, $smof_data;
	$lazy_load = isset($smof_data['ftc_prod_lazy_load']) && $smof_data['ftc_prod_lazy_load'] && !( defined( 'DOING_AJAX' ) && DOING_AJAX );
	$placeholder_img_src = isset($smof_data['ftc_prod_placeholder_img']['url'])?$smof_data['ftc_prod_placeholder_img']['url']:wc_placeholder_img_src();
	
	if( defined( 'YITH_INFS' ) && (is_shop() || is_product_taxonomy()) ){ /* Compatible with YITH Infinite Scrolling */
		$lazy_load = false;
	}
	
	$prod_galleries = $product->get_gallery_image_ids();
	
	$has_back_image = (isset($smof_data['ftc_effect_product']) && (int)$smof_data['ftc_effect_product'] == 0)?false:true;
	
	if( !is_array($prod_galleries) || ( is_array($prod_galleries) && count($prod_galleries) == 0 ) ){
		$has_back_image = false;
	}
	
	if( wp_is_mobile() ){
		$has_back_image = false;
	}
	
	//$image_size = apply_filters('ftc_loop_product_thumbnail', 'shop_catalog');
	$image_size = apply_filters('ftc_loop_product_thumbnail', 'woocommerce_thumbnail');
	
	$dimensions = wc_get_image_size( $image_size );
	
	echo '<span class="'.(($has_back_image)?'has-more-image':'no-back-image').' woocommerce-product-gallery__image">';
	if( !$lazy_load ){
		echo woocommerce_get_product_thumbnail( $image_size );
		if( $has_back_image ){
			echo wp_get_attachment_image( $prod_galleries[0], $image_size, 0, array('class' => 'product-image-back') );
		}
	}
	else{
		$front_img_src = '';
		$alt = '';
		if( has_post_thumbnail( $product->get_id() ) ){
			$post_thumbnail_id = get_post_thumbnail_id($product->get_id());
			$image_obj = wp_get_attachment_image_src($post_thumbnail_id, $image_size, 0);
			if( isset($image_obj[0]) ){
				$front_img_src = $image_obj[0];
			}
			$alt = trim(strip_tags( get_post_meta($post_thumbnail_id, '_wp_attachment_image_alt', true) ));
		}
		else if( wc_placeholder_img_src() ){
			$front_img_src = wc_placeholder_img_src();
		}
		
		echo '<img src="'.esc_url($placeholder_img_src).'" data-src="'.esc_url($front_img_src).'" class="attachment-shop_catalog wp-post-image ftc-lazy-load" alt="'.esc_attr($alt).'" width="'.$dimensions['width'].'" height="'.$dimensions['height'].'" />';
		
		if( $has_back_image ){
			$back_img_src = '';
			$alt = '';
			$image_obj = wp_get_attachment_image_src($prod_galleries[0], $image_size, 0);
			if( isset($image_obj[0]) ){
				$back_img_src = $image_obj[0];
				$alt = trim(strip_tags( get_post_meta($prod_galleries[0], '_wp_attachment_image_alt', true) ));
			}
			else if( wc_placeholder_img_src() ){
				$back_img_src = wc_placeholder_img_src();
			}
			
			echo '<img src="'.esc_url($placeholder_img_src).'" data-src="'.esc_url($back_img_src).'" class="product-image-back ftc-lazy-load" alt="'.esc_attr($alt).'" width="'.$dimensions['width'].'" height="'.$dimensions['height'].'" />';
		}
	}
	echo '</span>';
}

function ftc_template_loop_product_title(){
	global $post, $product;
	$uri = esc_url(get_permalink($post->ID));
	echo "<h3 class=\"heading-title product-name\">";
	echo "<a href='{$uri}'>". esc_attr(get_the_title()) ."</a>";
	echo "</h3>";
}

function ftc_template_loop_add_to_cart(){
	global $smof_data;
	
	if( $smof_data['ftc_enable_catalog_mode'] && isset($smof_data['ftc_enable_catalog_mode']) ){
		return;
	}
	
	echo "<div class='add-to-cart loop-add-to-cart'>";
	woocommerce_template_loop_add_to_cart();
	echo "</div>";
}

function ftc_template_loop_product_sku(){
	global $product, $post;
	echo "<span class=\"product-sku\">" . esc_attr($product->get_sku()) . "</span>";
}

function ftc_template_loop_short_description(){
	global $product, $post, $smof_data;
	$has_grid_list = get_option('ftc_enable_glt', 'yes') == 'yes';
	$grid_limit_words = absint($smof_data['ftc_prod_cat_grid_desc_words']);
	$show_grid_desc = $smof_data['ftc_prod_cat_grid_desc'];
	
	if( empty($post->post_excerpt) )
		return;
	
	if( !(is_tax( get_object_taxonomies( 'product' ) ) || is_post_type_archive('product')) ):
		?>
	<div class="short-description">
		<?php ftc_the_excerpt_max_words($grid_limit_words, '', true, '', true); ?>
	</div>
	<?php
else:
	if( $show_grid_desc ):
		?>
		<div class="short-description grid" style="<?php echo esc_attr(($has_grid_list)?'display: none':''); ?>" >
			<?php ftc_the_excerpt_max_words($grid_limit_words, '', true, '', true); ?>
		</div>
		<?php
	endif;
endif;
}

function ftc_template_loop_short_description_listview(){
	global $product, $post, $smof_data;
	$list_limit_words = absint($smof_data['ftc_prod_cat_list_desc_words']);
	$show_list_desc = $smof_data['ftc_prod_cat_list_desc'];
	$is_archive = is_tax( get_object_taxonomies( 'product' ) ) || is_post_type_archive('product');
	if( $show_list_desc && $is_archive ):
		?>
		<div class="short-description list" style="display: none" >
			<?php ftc_the_excerpt_max_words($list_limit_words, '', true, '', true); ?>
		</div>
		<?php
	endif;
}

function ftc_template_loop_categories(){
	global $product;
	$categories_label = esc_html__('Categories: ', 'corona');
	echo wc_get_product_category_list($product->get_id(),', ', '<div class="product-categories"><span>'.$categories_label.'</span>', '</div>'); 
}

function ftc_change_products_per_page_shop(){
	global $smof_data;
	if( is_tax( get_object_taxonomies( 'product' ) ) || is_post_type_archive('product') ){
		if( isset($smof_data["ftc_prod_cat_per_page"]) && absint($smof_data["ftc_prod_cat_per_page"]) > 0){
			return absint($smof_data["ftc_prod_cat_per_page"]);
		}
	}
}

function ftc_get_empty_rating_html( $rating_html, $rating ){
	if( $rating == 0 ){
		$rating_html  = '<div class="star-rating no-rating" title="#">';
		$rating_html .= '<span style="width:0%"></span>';
		$rating_html .= '</div>';
	}
	return $rating_html;
}

function ftc_shop_top_content_widget_area_button(){
	global $smof_data;
	if( is_active_sidebar('product-category-top-content') && $smof_data['ftc_prod_cat_top_content'] && isset($smof_data['ftc_prod_cat_top_content']) ){
		?>
		<div class="prod-cat-show-top-content-button"><a href="#"><?php esc_html_e('Filter', 'corona') ?></a></div>
		<?php
	}
}

/*** End Shop - Category ***/



/*** Single Product ***/

/* Remove hook */
remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
remove_action('woocommerce_single_product_summary', 'osapa_template_product_size_chart_button', 80);
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

/* Add hook */

if( isset($smof_data['ftc_prod_title']) && $smof_data['ftc_prod_title'] ){
	add_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 0);
}
if( isset($smof_data['ftc_show_prod_navigation']) && $smof_data['ftc_show_prod_navigation'] ){
	add_action('woocommerce_single_product_summary', 'ftc_template_single_navigation', 1);
}
if( isset($smof_data['ftc_prod_rating']) && $smof_data['ftc_prod_rating'] ){
	add_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 2);
}
if( isset($smof_data['ftc_prod_availability']) && $smof_data['ftc_prod_availability'] ){
	add_action('woocommerce_single_product_summary', 'ftc_template_single_availability', 4);
}
if( isset($smof_data['ftc_prod_price']) && $smof_data['ftc_prod_price'] ){
	add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 4);
}
if( isset($smof_data['ftc_prod_label']) && $smof_data['ftc_prod_label'] ){
	add_action('ftc_before_product_image', 'ftc_template_loop_product_label', 10);
}
add_action('ftc_before_product_image', 'ftc_template_single_product_video_button', 20);
if( isset($smof_data['ftc_prod_sku']) && $smof_data['ftc_prod_sku'] ){
	add_action('woocommerce_single_product_summary', 'ftc_template_single_sku', 6);
}
add_action('woocommerce_single_product_summary', 'ftc_template_single_meta', 60);

add_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 70);

add_action('woocommerce_after_single_product_summary', 'ftc_product_ads_banner', 12);
if(isset( $smof_data['ftc_prod_sharing']) && $smof_data['ftc_prod_sharing'] ){
	add_action('woocommerce_share', 'ftc_template_before_single_social_sharing', 9);
}
add_action('woocommerce_share', 'ftc_template_social_sharing', 10);
add_action('woocommerce_share', 'ftc_template_after_single_social_sharing', 11);
if ( isset($smof_data['ftc_prod_related']) && $smof_data['ftc_prod_related']){
	add_action( 'woocommerce_related', 'woocommerce_output_related_products', 20 );
}
if( function_exists('ftc_template_loop_time_deals') ){
	add_action('woocommerce_single_product_summary', 'ftc_template_loop_time_deals', 20);
}

add_filter('woocommerce_available_variation', 'ftc_variable_product_price_filter', 10, 3);

add_filter('woocommerce_output_related_products_args', 'ftc_output_related_products_args_filter');
if( isset($smof_data['ftc_show_prod_size_chart']) && $smof_data['ftc_show_prod_size_chart'] ){
	add_action('woocommerce_single_product_summary', 'ftc_template_product_size_chart_button', 20);
}

if( !is_admin() ){ /* Fix for WooCommerce Tab Manager plugin */
	add_filter( 'woocommerce_product_tabs', 'ftc_product_remove_tabs', 999 );
	add_filter( 'woocommerce_product_tabs', 'ftc_add_product_custom_tab', 90 );
}

add_action('wp_ajax_load_product_video', 'ftc_load_product_video_callback' );
add_action('wp_ajax_nopriv_load_product_video', 'ftc_load_product_video_callback' );
add_action('wp_ajax_load_product_size_chart', 'ftc_load_product_size_chart_callback');
add_action('wp_ajax_nopriv_load_product_size_chart', 'ftc_load_product_size_chart_callback');
/*** End Product ***/

add_action('woocommerce_widget_shopping_cart_before_buttons','ftc_woo_mini_cart_before_buttons' );

function ftc_woo_mini_cart_before_buttons(){
    wp_nonce_field( 'woocommerce-cart' ); 
    ?>
    <button type="submit"  class="button hidden" name="update_cart" value="Update cart"><?php esc_html__('Update cart', 'corona'); ?></button>
    <?php
}

/* Product  360 */
add_action('ftc_before_product_image', 'ftc_template_video_360', 30);
function ftc_template_video_360() {
	global $post;

	$gallery_ids = get_post_meta($post->ID, 'ftc_product360', true);
	if (empty($gallery_ids)) {
		return;
	}
	$gallery_ids = explode(',', $gallery_ids);
	if( is_array($gallery_ids) && has_post_thumbnail() ){
		array_unshift($gallery_ids, get_post_thumbnail_id());
	}
	$frames_count = count( $gallery_ids );
	$images_js_string = '';
	?>
	<a class="ftc-video360" href="#product-360" title="click show image 360°"><?php echo esc_html__('Video 360&deg;', 'corona') ?></a>
	<div id="product-360" class="product-360 mfp-hide">
		<div class="threesixty threesixty-product-360">
			<ul class="threesixty_images">
				<?php $i=0; foreach( $gallery_ids as $gallery_id ): $i++;  ?>
				<?php
				$img = wp_get_attachment_image_src( $gallery_id, 'full' );
				$width = $img[1];
				$height = $img[2];
				$images_js_string .= "'" . $img[0] . "'";
				if( $i < $frames_count ) {
					$images_js_string .= ","; 
				}
				?>
			<?php endforeach; ?>
		</ul>
		<div class="spinner">
			<span>0%</span>
		</div>
	</div>
</div>
<?php
wp_add_inline_script('ftc-global', 'jQuery(document).ready(function( $ ) {
	$(".threesixty-product-360").ThreeSixty({
		totalFrames: ' . esc_js( $frames_count ) . ',
		endFrame: ' . esc_js( $frames_count ) . ',
		currentFrame: 1,
		imgList: ".threesixty_images",
		progress: ".spinner",
		imgArray: ' . "[".$images_js_string."]" . ',
		height: ' . esc_js( $height ) . ',  
		width: ' . esc_js( $width ) . ',
		responsive: true,
		navigation: true
		});
	});', 'after');
}
/*end product 360 */
function ftc_template_single_product_video_button(){
	if( wp_is_mobile() ){
		return;
	}
	global $product;
	$video_url = get_post_meta($product->get_id(), 'ftc_prod_video_url', true);
	if( !empty($video_url) ){
		$ajax_url = admin_url('admin-ajax.php', is_ssl()?'https':'http').'?ajax=true&action=load_product_video&product_id='.$product->get_id();
		echo '<a class="ftc-product-video-button" href="'.esc_url($ajax_url).'"><span class="watch-video">'.esc_html__("Watch video",'corona').'</span></a>';
	}
}

/* Single Product Video - Register ajax */
function ftc_load_product_video_callback(){
	if( empty($_GET['product_id']) ){
		wp_die('Invalid Products');
	}
	
	$prod_id = absint($_GET['product_id']);

	if( $prod_id <= 0 ){
		wp_die('Invalid Products');
	}
	
	$video_url = get_post_meta($prod_id, 'ftc_prod_video_url', true);
	ob_start();
	if( !empty($video_url) ){
		echo do_shortcode('[ftc_video src='.esc_url($video_url).']');
	}
	wp_die( ob_get_clean() );
}

function ftc_template_single_navigation(){
	$prev_post = get_adjacent_post(false, '', true, 'product_cat');
	$next_post = get_adjacent_post(false, '', false, 'product_cat');
	?>
	<div class="single-navigation">
		<?php 
		if( $prev_post ){
			$post_id = $prev_post->ID;
			$product = wc_get_product($post_id);
			?>
			<a href="<?php echo get_permalink($post_id); ?>" rel="prev">
				<div class="product-info prev-product-info">
					<?php echo wp_kses_post($product->get_image()); ?>
					<div class="content-nav">
						<span class="nav-title"><?php echo esc_html($product->get_title()); ?></span>
						<span class="price"><?php echo wp_kses_post($product->get_price_html()); ?></span>
					</div>
				</div>
			</a>
			<?php
		}
		
		if( $next_post ){
			$post_id = $next_post->ID;
			$product = wc_get_product($post_id);
			?>
			<a href="<?php echo get_permalink($post_id); ?>" rel="next">
				<div class="product-info next-product-info">
					<?php echo wp_kses_post($product->get_image()); ?>
					<div class="content-nav">
						<span class="nav-title"><?php echo esc_html($product->get_title()); ?></span>
						<span class="price"><?php echo wp_kses_post($product->get_price_html()); ?></span>
					</div>
				</div>
			</a>
			<?php
		}
		?>
	</div>
	<?php
}

function ftc_template_before_single_social_sharing(){
	?>
	<div class="social-sharing">
		
		<ul class="ftc-social-sharing">
			<span>Share:</span>
			<li class="twitter">
				<a href="https://twitter.com/home?status=<?php echo esc_url(get_permalink()); ?>" target="_blank"><i class="fa fa-twitter"></i> Tweet</a>
			</li>

			<li class="facebook">
				<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_url(get_permalink()); ?>" target="_blank"><i class="fa fa-facebook"></i> Share</a>
			</li>

			<li class="pinterest">
				<?php $image_link  = wp_get_attachment_url( get_post_thumbnail_id() );?>
				<a href="https://pinterest.com/pin/create/button/?url=<?php echo esc_url(get_permalink()); ?>&amp;media=<?php echo esc_url($image_link);?>" target="_blank"><i class="fa fa-pinterest"></i> Pinterest</a>
			</li>

		</ul>
		<?php
	}

	function ftc_template_after_single_social_sharing(){
		?>
	</div>
	<?php
}

function ftc_template_single_meta() {
	global $product, $post, $smof_data;

	echo '<div class="content">';
	do_action('woocommerce_product_meta_start');
	if (isset($smof_data['ftc_prod_cat']) && $smof_data['ftc_prod_cat']) {
		echo wc_get_product_category_list($product->get_id(),', ', '<div class="caftc-link"><span>' . esc_html__('Categories: ', 'corona') . '</span><span class="cat-links">', '</span></div>');
	}
	if (isset($smof_data['ftc_prod_tag']) && $smof_data['ftc_prod_tag']) {
		echo wc_get_product_tag_list($product->get_id(),', ', '<div class="tags-link"><span>' . esc_html__('Tags: ', 'corona') . '</span><span class="tag-links">', '</span></div>');
	}
	do_action('woocommerce_product_meta_end');
	echo '</div>';
}

function ftc_template_single_sku(){
	global $product;
	if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ){
		echo '<div class="sku-wrapper product_meta">' . esc_html__( 'Sku: ', 'corona' ) . '<span class="sku" itemprop="sku">' . (( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'corona' )) . '</span></div>';
	}
}
function ftc_template_single_availability(){
	global $product;

	$product_stock = ftc_product_get_availability();
	$availability_text = empty($product_stock['availability'])?esc_html__('In Stock', 'corona'):esc_attr($product_stock['availability']);
	?>	
	<p class="availability stock <?php echo esc_attr($product_stock['class']); ?>" data-original="<?php echo esc_attr($availability_text) ?>" data-class="<?php echo esc_attr($product_stock['class']) ?>"><span><?php echo esc_html($availability_text); ?></span></p>	
	<?php
}

/* Canvas filter */

add_action('woocommerce_before_shop_loop', 'ftc_shop_top_filter_boxed_button', 30);
function ftc_shop_top_filter_boxed_button(){
	global $smof_data;
	if($smof_data['ftc_prod_cat_layout'] == '0-1-0' && isset($smof_data['ftc_prod_box_sidebar_filter']) && $smof_data['ftc_prod_box_sidebar_filter'] && is_active_sidebar($smof_data['ftc_prod_cat_left_sidebar'])) {
		?>
		<div class="button-filter-boxed"><a href="#"><?php esc_html_e('Off-Canvas Filter', 'corona') ?></a></div>
		<?php
	}
}

/*Scroll animation Shop*/
add_action('woocommerce_after_shop_loop', 'ftc_loadmore_shop_ajax');
function ftc_loadmore_shop_ajax(){
	global $smof_data;
	if(isset($smof_data['ftc_loadmore_button_infinite']) && $smof_data['ftc_loadmore_button_infinite']){
		echo '<div class="button-loadmore-ajax">';
		echo '<a class="ftc-load-more-button-shop">'.esc_html__('Load more products', 'corona').'<span class="ftc-loading-shop">';
		echo '<p>'.esc_html__('Loading', 'corona').'</p><div class="line"></div><div class="line"></div><div class="line"></div>';
		echo '</span></a>' ;
		echo '</div>';
	}
}
/*section add to cart bottom*/
if(isset($smof_data['ftc_prod_stic_bot']) && $smof_data['ftc_prod_stic_bot']){
	add_action('wp_footer', 'ftc_section_add_to_cart');
}
function ftc_section_add_to_cart(){
	global $product, $post;
	if(!class_exists('WooCommerce')){
		return ;
	}
	if(is_singular('product') && !$product->is_type( 'variable' ) && !wp_is_mobile() ){

		$image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID ), array(100, 100) );
		?>
		<section class="ftc-sticky-atc">
			<div class="container">
				<div class="content-product">
					<div class="images"><img src="<?php echo esc_url($image[0]); ?>"/></div>
					<div class="description">
						<div class="title-product">
							<h4><?php echo wp_kses_post(get_the_title()); ?></h4>
						</div>
						<div class="rating">
							<?php
							$product = wc_get_product();
							$rating_count = $product->get_rating_count();
							$average = $product->get_average_rating();
							echo wc_get_rating_html( $average, $rating_count ); ?>
						</div>
						<div class="price">
							<?php echo  wp_kses_post($product->get_price_html());?>
						</div>
					</div>
				</div>
				<div class="single-add-to-cart">
					<?php echo wp_kses_post(woocommerce_template_single_add_to_cart() ); ?>
				</div>
			</div>
		</section>
		<?php
	}
}

/* close pop-up cart dropdown*/
global $smof_data;
if ( isset($smof_data['ftc_cart_layout']) && $smof_data['ftc_cart_layout'] == 'dropdown'){
	add_filter( 'body_class', function( $classes ) {
		return array_merge( $classes, array( 'cart-dropdown' ) );
		return $classes;
	});
}
/*** Product tab ***/
function ftc_product_remove_tabs( $tabs = array() ){
	global $smof_data;
	if( !$smof_data['ftc_prod_tabs'] ){
		return array();
	}
	return $tabs;
}

function ftc_add_product_custom_tab( $tabs = array() ){
	global $smof_data, $post;
	
	$custom_tab_title = $smof_data['ftc_prod_custom_tab_title'];
	
	$product_custom_tab = get_post_meta( $post->ID, 'ftc_prod_custom_tab', true );
	if( $product_custom_tab ){
		$custom_tab_title = get_post_meta( $post->ID, 'ftc_prod_custom_tab_title', true );
	}
	
	if( isset($smof_data['ftc_prod_custom_tab']) && $smof_data['ftc_prod_custom_tab'] ){
		$tabs['ftc_custom'] = array(
			'title'    	=> esc_html( $custom_tab_title )
			,'priority' => 90
			,'callback' => "ftc_product_custom_tab_content"
		);
	} 
	return $tabs;
}

function ftc_product_custom_tab_content(){
	global $smof_data, $post;
	
	$custom_tab_content = $smof_data['ftc_prod_custom_tab_content'];
	
	$product_custom_tab = get_post_meta( $post->ID, 'ftc_prod_custom_tab', true );
	if( $product_custom_tab ){
		$custom_tab_content = get_post_meta( $post->ID, 'ftc_prod_custom_tab_content', true );
	}
	
	print_r(do_shortcode( stripslashes( wp_specialchars_decode( $custom_tab_content ) ) )) ;
}

/* Ads Banner */
function ftc_product_ads_banner(){
	global $smof_data;
	
	if( isset($smof_data['ftc_prod_ads_banner']) && $smof_data['ftc_prod_ads_banner'] && class_exists('js_composer.js_composer.php')){
		$ads_banner_content = $smof_data['ftc_prod_ads_banner_content'];
		echo '<div class="ads-banner">';
		print_r(do_shortcode( stripslashes( wp_specialchars_decode( $ads_banner_content ) ) )) ;
		echo '</div>';
	}
}

/* Related Products */
function ftc_output_related_products_args_filter( $args ){
	$args['posts_per_page'] = 6;
	$args['columns'] = 5;
	return $args;
}

/* Always show variable product price if they are same */
function ftc_variable_product_price_filter( $value, $object = null, $variation = null ){
	if( $value['price_html'] == '' ){
		$value['price_html'] = '<span class="price">' . $variation->get_price_html() . '</span>';
	}
	return $value;
}

/*** General hook ***/

/*************************************************************
* Custom group button on product (quickshop, wishlist, compare) 
* Begin tag: 	10000
* Add To Cart: 	10001
* Wishlist:  	10002
* Compare:   	10004
* Quickshop: 	10003
* End tag:   	10005
**************************************************************/
add_action('woocommerce_after_shop_loop_item_title', 'ftc_template_loop_add_to_cart', 10001 );
function ftc_product_group_button_start(){
	global $smof_data;
	$num_icon = 0;
	
	if( isset($smof_data['ftc_effect_hover_product_style']) && $smof_data['ftc_effect_hover_product_style'] != 'style-3' ){
		if( has_action('woocommerce_after_shop_loop_item_title', 'ftc_template_loop_add_to_cart') && !$smof_data['ftc_enable_catalog_mode'] && apply_filters('ftc_display_add_to_cart_button_on_thumbnail', true) ){
			$num_icon++;
		}
	}
	else{
		remove_action('woocommerce_after_shop_loop_item_title', 'ftc_template_loop_add_to_cart', 10001 );
	}
	
	echo "<div class=\"group-button-product\" ><div class=\"product-group-button\" >";
}

function ftc_product_group_button_end(){
	echo "</div></div>";
}
function ftc_meta_start(){
	echo "<div class='meta_info'>";
}
function ftc_meta_end(){
	echo "</div>";
}
add_action('woocommerce_after_shop_loop_item_title', 'ftc_product_group_button_start', 10000 );
add_action('woocommerce_after_shop_loop_item_title', 'ftc_product_group_button_end', 10005 );
add_action('woocommerce_after_shop_loop_item', 'ftc_meta_start', 69 );
add_action('woocommerce_after_shop_loop_item', 'ftc_meta_end', 100 );
/* Wishlist */
if( class_exists('YITH_WCWL') ){
	function ftc_add_wishlist_button_to_product_list(){
		global $product, $yith_wcwl;
		
		$default_wishlists = is_user_logged_in() ? YITH_WCWL()->get_wishlists( array( 'is_default' => true ) ) : false;
		if( ! empty( $default_wishlists ) ){
			$default_wishlist = $default_wishlists[0]['ID'];
		}
		else{
			$default_wishlist = false;
		}
		
		$exists = YITH_WCWL()->is_product_in_wishlist( $product->get_id(), $default_wishlist );
		
		$wishlist_url = YITH_WCWL()->get_wishlist_url();
		
		$added_class = $exists?'added':'';
		
		echo '<div class="yith-wcwl-add-to-wishlist add-to-wishlist-'.$product->get_id().' '.$added_class.'">';
		echo '<a href="' . esc_url( add_query_arg( 'add_to_wishlist', $product->get_id() ) )
		. '" data-product-id="' . $product->get_id() . '" data-product-type="' . $product->get_type() 
		. '" class="add_to_wishlist wishlist" ><i class="fa fa-heart"></i><span class="ftc-tooltip button-tooltip">'.esc_html__('Wishlist', 'corona').'</span></a>';
		
		echo '<span class="yith-wcwl-wishlistexistsbrowse '.($exists?'show':'hide').'" style="'.($exists?'':'display: none').'">';
		echo '<a href="'.esc_url($wishlist_url).'"><i class="fa fa-heart"></i><span class="ftc-tooltip button-tooltip">'.esc_html__('Wishlist', 'corona').'</span></a>';
		echo '</span>';
		
		echo '</div>';
	}
	add_action( 'woocommerce_after_shop_loop_item_title', 'ftc_add_wishlist_button_to_product_list', 10002 );
	add_action( 'woocommerce_after_shop_loop_item', 'ftc_add_wishlist_button_to_product_list', 80 );
}

/* Compare */
if( class_exists('YITH_Woocompare') && get_option('yith_woocompare_compare_button_in_products_list') == 'yes' ){
	global $yith_woocompare;
	$is_ajax = ( defined( 'DOING_AJAX' ) && DOING_AJAX );
	if( $yith_woocompare->is_frontend() || $is_ajax ) {
		if( $is_ajax ){
			if( defined('YITH_WOOCOMPARE_DIR') && !class_exists('YITH_Woocompare_Frontend') ){
				$compare_frontend_class = YITH_WOOCOMPARE_DIR . 'includes/class.yith-woocompare-frontend.php';
				if( file_exists($compare_frontend_class) ){
					require_once $compare_frontend_class;
				}
			}
			$yith_woocompare->obj = new YITH_Woocompare_Frontend();
		}
		remove_action( 'woocommerce_after_shop_loop_item', array( $yith_woocompare->obj, 'add_compare_link' ), 20 );
		function ftc_add_compare_button_to_product_list(){
			if( wp_is_mobile() )
				return;
			global $yith_woocompare, $product;
			echo '<a class="compare" href="'.$yith_woocompare->obj->add_product_url( $product->get_id() ).'" data-product_id="'.$product->get_id().'">'.get_option('yith_woocompare_button_text').'</a>';
		}
		add_action( 'woocommerce_after_shop_loop_item_title', 'ftc_add_compare_button_to_product_list', 10004 );
		add_action( 'woocommerce_after_shop_loop_item', 'ftc_add_compare_button_to_product_list', 70 );
		
		add_filter( 'option_yith_woocompare_button_text', 'ftc_compare_button_text_filter', 99 );
		function ftc_compare_button_text_filter( $button_text ){
			return '<i class="fa fa-retweet"></i><span class="ftc-tooltip button-tooltip">'.esc_html($button_text).'</span>';
		}
	}
}
/* Compare - Add custom style */
if( isset($_GET['action']) && $_GET['action'] == 'yith-woocompare-view-table' ){
	add_action('wp_head', 'ftc_add_custom_style_compare_popup');
}
function ftc_add_custom_style_compare_popup(){
	global $smof_data;
	echo '<link rel="stylesheet" type="text/css" media="all" href="'.get_template_directory_uri().'/assets/css/default.css" />';
	echo '<link rel="stylesheet" type="text/css" media="all" href="'.get_template_directory_uri().'/style.css" />';
	echo '<link rel="stylesheet" type="text/css" media="all" href="'.get_template_directory_uri().'/assets/css/font-awesome.css" />';
	
	/* Add custom css for iframe */
	ftc_add_header_dynamic_css( true );
	
	/* Register google font for iframe */
	ftc_register_google_font( true );
}
/*** End General hook ***/

/*** Cart - Checkout hooks ***/
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 10 );
add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display', 10 );

add_action('woocommerce_proceed_to_checkout', 'ftc_cart_continue_shopping_button', 20);

/* Continue Shopping button */
function ftc_cart_continue_shopping_button(){
	echo '<a href="'.esc_url(wc_get_page_permalink('shop')).'" class="button button-secondary">'.esc_html__('Continue Shopping', 'corona').'</a>';
}

add_filter( 'woocommerce_cart_item_name', 'product_thumbnail_in_checkout', 20, 3 );
function product_thumbnail_in_checkout( $product_name, $cart_item ) {
    if ( is_checkout() )
    {
        $thumbnail   = $cart_item['data']->get_image(array( 80, 80));
        $thumbnail_product  = '<div class="product-item-thumbnail">'.$thumbnail.'</div> ';
        $product_name = $thumbnail_product . '<h4>' .$product_name .'</h4>';
    }
    return $product_name;
}
?>