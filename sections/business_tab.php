<?php 
$location_meta_data = ifind_get_post_custom_metadata(get_the_ID(), 'location');
$location_lat = $location_meta_data['location_data']['lat'];
$location_lng = $location_meta_data['location_data']['lng'];
$location_address = $location_meta_data['location_data']['address'];
$max_distance = $location_meta_data['max_distance'];
?>
<div class="ifind-list-business">
    <?php 
    $taxonomy = 'business_group';
    $options = array(
        'parent' => 0,
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'meta_key'      => 'order_index',
        'meta_compare'  => 'NUMERIC',
    );
    $list_business_group_parent = ifind_get_list_category($taxonomy, $options); 
    $list_data = array();
    if (count($list_business_group_parent) > 0) {
        foreach ($list_business_group_parent as $term_id => $name) {
            $term = get_term( $term_id, $taxonomy );
            $list_data[$term_id]['name'] = !$max_distance ? $term->name.' ('.$term->count.')' : $term->name;
            $list_data[$term_id]['map_marker_icon'] = get_term_meta($term_id, 'map_marker_icon', true)['id'];
            $list_data[$term_id]['banner_image'] = get_term_meta($term_id, 'banner_image', true)['id'];
            $list_data[$term_id]['category_color'] = get_term_meta($term_id, 'category_color', true);
        }
    }
    ?>
    <div class="ifind-business-title-banner">
        <?php 
        if (count($list_data) > 0) {
        	foreach ($list_data as $business_group_parent_id => $business_group_meta_data) {
        		echo '<a href="#" class="business-group-banner-link" data-category-id="'.$business_group_parent_id.'">'.wp_get_attachment_image( $business_group_meta_data['banner_image'], 'full' ).'</a>';
        	}
        }
        ?>
    </div>

    <div id="ifind-business-tabs" class="wow fadeInUp" data-wow-duration="2s">	
        <ul class="nav nav-pills">
            <?php 
            if (count($list_data) > 0) {
                $i = 0;
                foreach ($list_data as $business_group_parent_id => $business_group_meta_data) {
                    $style = "";
                    //$active = ($i == 0) ? ' active' : '';
                    //$active_icon = ($i == 0) ? 'wow pulse' : '';
                    $map_marker_icon_url = wp_get_attachment_image_url( $business_group_meta_data['map_marker_icon'], 'map_logo' );
                    $map_marker_icon_image = wp_get_attachment_image( $business_group_meta_data['map_marker_icon'], 'map_logo' );
                    $active_icon_iteration = ($i == 0) ? 'data-wow-iteration="600"' : '';
                    $style .= ($business_group_meta_data['category_color']) ? 'style="background: '.$business_group_meta_data['category_color'].'"' : "";
                    echo '<li class="ifind-business-tabs-nav" data-icon="'.$map_marker_icon_url.'" class="'.$active.' wow fadeInDown" data-wow-duration="0.5s" data-category-id="'.$business_group_parent_id.'" >';
                        echo '<a href="#business-tabs-'.$business_group_parent_id.'" data-toggle="tab">';
                        echo '<span class="business-tab-title-icon">'.$map_marker_icon_image.'</span> '.$business_group_meta_data['name'];
                        echo '</a>';
                    echo '</li>';
                    $i++;
                }
            }
            ?>
        </ul>
        <a class="business-back-link" href="#"><span class="arrow"><span></span></span><?php _e("Back", 'ifind'); ?></a>

        <!-- Display Text Directions -->
        <div id="map-directions">
            <div class="map-directions-wrap">
                <span class="directions-close"><?php _e('x','ifind'); ?></span>
                <div id="map-directions-content-wrap">
                    <div class="map-directions-header">
                        <div class="map-directions-distance"><span><?php _e("Distance:", 'ifind'); ?></span> <span class="content"></span></div>
                        <div class="map-directions-duration"><span><?php _e("Duration:", 'ifind'); ?></span> <span class="content"></span></div>
                        <div class="map-directions-start-address"><span><?php _e("Start Address:", 'ifind'); ?></span> <span class="content"></span></div>
                        <div class="map-directions-end-address"><span><?php _e("End Address:", 'ifind'); ?></span> <span class="content"></span></div>
                    </div>
                    <div class="map-directions-content"></div>
                </div>
            </div>
        </div>
        <div class="tab-content clearfix">
            <?php 
            if (count($list_data) > 0) {
                $i = 0;
                foreach ($list_data as $business_group_parent_id => $business_group_meta_data) { 
                    $map_marker_icon_url = wp_get_attachment_image_url( $business_group_meta_data['map_marker_icon'], 'map_logo' );
                    //$active = ($i == 0) ? ' active' : '';
                    ?>
                    <div class="tab-pane <?php //echo $active; ?>" data-category-id="<?php echo $business_group_parent_id; ?>" id="business-tabs-<?php echo $business_group_parent_id; ?>">
                        
                        <div class="ifind-business-filter">
                            <?php 
                            $list_business_group_children = ifind_get_list_category($taxonomy, array('parent' => $business_group_parent_id));
                            echo '<div class="heading">'.__('Filter by:','ifind').'</div>';
                            echo '<ul class="ifind-business-filter-list wow fadeInUp" data-wow-duration="0.5s">';
                                echo '<li><a class="ifind-business-filter-item active" data-icon="'.$map_marker_icon_url.'" href="#" data-filter-by="all">'.sprintf(__('All of %s','ifind'), $business_group_meta_data['name']).'</a></li>';
                                if (count($list_business_group_children) > 0) {
                                        foreach ($list_business_group_children as $business_group_children_id => $value) {
                                        $term_children = get_term( $business_group_children_id, $taxonomy );
                                        echo '<li><a class="ifind-business-filter-item" data-icon="'.$map_marker_icon_url.'" href="#" data-filter-by="'.$term_children->slug.'">'.$term_children->name.'</a></li>';
                                    }
                                }
                            echo '</ul>';
                            ?>
                        </div>
                        
                        <div class="ifind-business-list wow fadeInUp" data-wow-duration="0.5s">
                            <?php 
                            $list_business = ifind_get_list_posts('business', $location_meta_data['list_business'], 'business_group', $business_group_parent_id); 
                            if (count($list_business) > 0) {
                                foreach ($list_business as $business_id => $value) {
                                    $business_name = get_the_title($business_id);
                                    $business_metadata = ifind_get_post_custom_metadata($business_id, 'business');
                                    $business_logo = wp_get_attachment_image( $business_metadata['logo'], 'full' );
                                    $business_info_banner = wp_get_attachment_image_url( $business_metadata['info_banner'], 'full' );
                                    $youtube_video_id = $business_metadata['youtube_video_id'];
                                    $youtube_video_url = 'http://www.youtube.com/embed/'.$youtube_video_id.'?enablejsapi=1&rel=0&modestbranding=0&wmode=opaque&showinfo=0&controls=0';
                                    $business_desc = $business_metadata['office_address'] ? '<div class="ifind-business-office-address">'.$business_metadata['office_address'].'</div>' : '';
                                    $business_desc .= $business_metadata['office_phone'] ? '<div class="ifind-business-office-phone">'.$business_metadata['office_phone'].'</div>' : '';
                                    $business_lat = $business_metadata['location_data']['lat'];
                                    $business_lng = $business_metadata['location_data']['lng'];
                                    $business_address = $business_metadata['location_data']['address'];
                                    $list_category_filter = 'all';
                                    $list_category = get_the_terms( $business_id, 'business_group' );
                                    if (count($list_category) > 0) {
                                        foreach ($list_category as $category) {
                                            $list_category_filter .= ' '.$category->slug;
                                        }
                                    }
                                    ?>
                                    <div class="ifind-business-item <?php echo $list_category_filter; ?>">
                                        <div class="ifind-business-logo">
                                            <?php 
                                            $fancybox_class = ($youtube_video_id) ? 'ifind-fancybox-video' : 'ifind-fancybox-image';
                                            $fancybox_link = ($youtube_video_id) ? $youtube_video_url : $business_info_banner;
                                            ?>
                                            
                                            <a class="fancybox fancybox.iframe <?php echo $fancybox_class; ?> business-logo-link" href="<?php echo $fancybox_link; ?>" content="<p>">
                                                <?php echo $business_logo; ?>
                                                <div class="heading"><?php _e("More info", 'ifind'); ?></div>
                                            </a>
                                        </div>
                                        <div class="ifind-business-desc">
                                            <div class="heading"><?php echo $business_name; ?></div>
                                            <div class="ifind-business-desc-content"><?php echo $business_desc; ?></div>
                                        </div>
                                        <div class="ifind-business-direction">
                                            <a href="#" class="business-direction-link" data-lat="<?php echo $business_lat; ?>" data-lng="<?php echo $business_lng; ?>">
                                                <div class="heading"><?php _e("Directions",'ifind') ?></div>
                                                <img class="ifind-business-icon" src="<?php echo TVLGIAO_WPDANCE_THEME_IMAGES.'/directions.png'; ?>" alt="<?php printf(__("Distance from \"%s\" to \"%s\"",'ifind'), $business_address, $location_address); ?>">
                                                <div class="business-distance heading"></div>
                                            </a>
                                        </div>
                                        <div class="ifind-business-geocoding">
                                            <a href="#" class="business-geocoding-link" 
                                                        data-icon="<?php echo $map_marker_icon_url; ?>" 
                                                        data-address="<?php echo $business_address; ?>" 
                                                        data-lat="<?php echo $business_lat; ?>" 
                                                        data-lng="<?php echo $business_lng; ?>">
                                                <div class="heading"><?php _e("Show On",'ifind') ?></div>
                                                <img class="ifind-business-icon" src="<?php echo TVLGIAO_WPDANCE_THEME_IMAGES.'/marker.png'; ?>" alt="<div><?php _e("Show this location on map",'ifind') ?></div>">
                                                <div class="heading"><?php _e("Map",'ifind') ?></div>
                                            </a>
                                        </div>
                                    </div>
                                <?php
                                }
                            }else{
                                echo '<div class="ifind-business-item">';
                                echo '<div class="heading">'.__("No business available!",'ifind').'</div>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                    <?php 
                    $i++;
                }
            }
            ?>
        </div> <!-- End tabs content -->
    </div>
</div>

<script src="http://www.youtube.com/player_api"></script>
<script>
    // Fires whenever a player has finished loading
    function onPlayerReady(event) {
        event.target.playVideo();
    }

    // Fires when the player's state changes.
    function onPlayerStateChange(event) {
        // Go to the next video after the current one is finished playing
        if (event.data === 0) {
            jQuery.fancybox.close();
        } else {
            event.target.playVideo();
        }
    }

    // The API will call this function when the page has finished downloading the JavaScript for the player API
    function onYouTubePlayerAPIReady() {
        // Initialise the fancyBox after the DOM is loaded
        jQuery(document).ready(function() {
            var fancybox_video = ".ifind-fancybox-video";
            jQuery(fancybox_video).fancybox({
                    openEffect  : 'fade',
                    closeEffect : 'fade',
                    padding     : 0,
                    margin      : [-60, 0, 0, 0],
                    fitToView   : false,
                    autoSize    : false,
                    width       : '100%',
                    height      : '100%',
                    closeBtn    : false,
                    arrows      : false,
                    mobile : {
                        margin      : [0, 0, 0, 0]
                    },
                    beforeShow  : function() {
                        // Find the iframe ID
                        var id = jQuery.fancybox.inner.find('iframe').attr('id');
                        
                        // Create video player object and add event listeners
                        var player = new YT.Player(id, {
                            playerVars: { 'autoplay': 1, 'controls': 0 },
                            events: {
                                'onReady': onPlayerReady,
                                'onStateChange': onPlayerStateChange
                            }
                        });
                    },
                    afterClose: function() {
                    }
                });

            jQuery(fancybox_video).on("click", function(){
                // Launch fancyBox
                jQuery(fancybox_video).eq(0).trigger('click');
            });
        })
    }
</script>