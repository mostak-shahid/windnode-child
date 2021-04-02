<?php
function add_slug_body_class( $classes ) {
    global $post;
    if ( isset( $post ) AND $post->post_type == 'page' ) {
        $classes[] = $post->post_type . '-' . $post->post_name;
    } else {
        $classes[] = $post->post_type . '-archive';
    }
    return $classes;
}
add_filter( 'body_class', 'add_slug_body_class' );

add_action( 'astra_template_parts_content', 'mos_author_details_func', 14 );
function mos_author_details_func(){
    if(is_single()) :
    ?>
    <div class="post-autor-details">
        <div class="img-part"><?php echo get_avatar(get_the_author_meta('ID'),120) ?></div>
        <div class="text-part">
            <h4 class="author-name" itemprop="name"><a href="<?php echo get_the_author_meta('user_url') ?>" title="View all posts by <?php echo get_the_author_meta('display_name') ?>" rel="author" class="url fn n" itemprop="url"><?php echo get_the_author_meta('display_name') ?></a></h4>
            <div class="author-description" itemprop="name"><?php echo get_the_author_meta('description') ?></div>
        </div>
    </div>
    <?php
    endif;
}
add_action('astra_primary_content_bottom','mos_related_posts_func');
function mos_related_posts_func(){
    if(is_single()):
        $term_ids = [];
        $categories = get_the_category(get_the_ID());
        foreach($categories as $category){
            $term_ids[] = $category->term_id;
        }
        //var_dump(implode(',',$term_ids));
        $args = array(
            'posts_per_page' => 6,
            'cat' => implode(',',$term_ids),
            'post__not_in' => array(get_the_ID())
        );
        // The Query
        $the_query = new WP_Query( $args );

        // The Loop
        if ( $the_query->have_posts() ) : ?>
        <div class="related-post">
            <h2 class="section-title"><?php echo __('Related Posts') ?></h2>
            
            <div class="related-post-wrapper">
                <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

                    <div class="post-content">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="ast-blog-featured-section post-thumb">
                                <div class="post-thumb-img-content post-thumb"><a href="<?php echo get_the_permalink() ?>"><img width="373" height="210" src="<?php echo aq_resize(get_the_post_thumbnail_url('','full'), 384, 210, true) ?>" class="attachment-373x250 size-373x250 wp-post-image" alt="office cleaning safety tips - janitorial leads pro" loading="lazy" itemprop="image"></a></div>
                            </div>
                        <?php endif;?>
                        <div class="related-entry-header">
                            <h4 class="related-entry-title" itemprop="headline"><a href="<?php echo get_the_permalink() ?>" rel="bookmark"><?php echo get_the_title() ?></a></h4>
                        </div>
                    </div>       
               
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif;
        /* Restore original Post Data */
        wp_reset_postdata();        
    endif;
}

/**
 * Detect plugin. For use on Front End only.
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
 
// check for plugin using plugin name
if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
    function is_shop(){
        return false;
    }
}
add_action('astra_content_top','mos_custom_header');
function mos_custom_header(){
    if (is_home()) :
        $page_for_posts = get_option( 'page_for_posts' );
    ?>       
        <header class="entry-header ast-no-thumbnail ast-no-meta"><h1 class="entry-title" itemprop="headline"><?php echo get_the_title($page_for_posts) ?></h1></header>    
    <?php
    elseif (is_shop()) :
        $page_for_products = get_option( 'woocommerce_shop_page_id' );
    ?>       
        <header class="entry-header ast-no-thumbnail ast-no-meta"><h1 class="entry-title" itemprop="headline"><?php echo get_the_title($page_for_products) ?></h1></header>    
    <?php
    elseif ( is_single() && 'product' == get_post_type() ) :
    ?>       
        <header class="entry-header ast-no-thumbnail ast-no-meta"><h1 class="entry-title" itemprop="headline"><?php echo get_the_title(get_the_ID()) ?></h1></header>    
    <?php
    endif;
}