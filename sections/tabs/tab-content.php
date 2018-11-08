<div class="tab-content clearfix">
    <?php 
    if (count($list_data) > 0) {
        $i = 0;
        foreach ($list_data as $business_group_parent_id => $business_group_meta_data) { 
            $map_marker_icon_url = wp_get_attachment_image_url( $business_group_meta_data['map_marker_icon'], 'map_logo' );
            $list_business = ifind_get_list_posts('business', $list_business_id, 'business_group', $business_group_parent_id);
            ?>
            <div class="tab-pane" data-category-id="<?php echo $business_group_parent_id; ?>" id="business-tabs-<?php echo $business_group_parent_id; ?>">
                
                <div class="ifind-business-filter">
                    <?php 
                    $list_business_group_children = ifind_get_list_category($taxonomy, array('parent' => $business_group_parent_id));
                    echo '<div class="heading">'.__('Filter by:','ifind').'</div>';
                    echo '<ul class="ifind-business-filter-list wow fadeInUp" data-wow-duration="0.5s">';
                        echo '<li><a class="ifind-business-filter-item active" data-icon="'.$map_marker_icon_url.'" href="#" data-filter-by="all">'.sprintf(__('All of %s','ifind'), $business_group_meta_data['name']).' ('.count($list_business).')</a></li>';
                        if (count($list_business_group_children) > 0) {
                            foreach ($list_business_group_children as $business_group_children_id => $value) {
                                $list_business_children = ifind_get_list_posts('business', $list_business_id, 'business_group', $business_group_children_id);
                                if(count($list_business_children)){
                                    $term_children = get_term( $business_group_children_id, $taxonomy );
                                    echo '<li><a class="ifind-business-filter-item" data-icon="'.$map_marker_icon_url.'" href="#" data-filter-by="';
                                    echo $term_children->slug.'">'.$term_children->name.' ('.count($list_business_children).')';
                                    echo '</a></li>';
                                }
                            }
                        }
                    echo '</ul>';
                    ?>
                </div>
                
                <div class="ifind-business-list wow fadeInUp" data-wow-duration="0.5s">
                    <?php 
                    if (count($list_business) > 0) {
                        foreach ($list_business as $business_id => $value) {
                            $business_name = get_the_title($business_id);
                            $business_metadata = ifind_get_post_custom_metadata($business_id, 'business');
                            $business_logo = wp_get_attachment_image( $business_metadata['logo'], 'full' );

                            $business_info_banner_type = $business_metadata['info_banner_type'];
                            $business_info_banner = wp_get_attachment_image_url( $business_metadata['info_banner'], 'full' );
                            $business_info_file = $business_metadata['info_banner_file'];
                            $youtube_video_id = $business_metadata['youtube_video_id'];
                            $youtube_video_url = 'http://www.youtube.com/embed/'.$youtube_video_id.'?enablejsapi=1&rel=0&modestbranding=0&wmode=opaque&showinfo=0&controls=0';
                            $business_video_file = $business_metadata['video_file'];
                            $business_desc = $business_metadata['office_address'] ? '<div class="ifind-business-office-address">'.$business_metadata['office_address'].'</div>' : '';
                            $business_desc .= $business_metadata['office_phone'] ? '<div class="ifind-business-office-phone">'.$business_metadata['office_phone'].'</div>' : '';
                            $business_email = $business_metadata['email_address'];
                            $business_lat = $business_metadata['location_data']['lat'];
                            $business_lng = $business_metadata['location_data']['lng'];
                            $business_address = $business_metadata['location_data']['address'];
                            $list_category_filter = 'all';
                            $list_category = get_the_terms( $business_id, 'business_group' );
                            //QR Code
                            $qr_type = $business_metadata['qrcode']['qr_type'];
                            $qr_content = $business_metadata['qrcode']['qr_content'];
                            $qr_code = new ifind_QrCode();
                            $qr_code->SETCONTENT($qr_type, $qr_content);
                            $qr_link = $qr_code->QRCODE('link', 400);

                            if (count($list_category) > 0) {
                                foreach ($list_category as $category) {
                                    $list_category_filter .= ' '.$category->slug;
                                }
                            } ?>
                            <div class="ifind-business-item <?php echo $list_category_filter; ?>">
                                <div class="ifind-counter-item ifind-business-logo"
                                    data-business-id="<?php echo $business_id; ?>" 
                                    data-location-id="<?php echo get_the_ID(); ?>"
                                    data-counter-position="logo">
                                    <?php 
                                    $fancybox_class = 'ifind-fancybox-'.$business_info_banner_type;
                                    if ($business_info_banner_type === 'image') {
                                        $fancybox_link = $business_info_banner;
                                    } else if ($business_info_banner_type === 'content-file') {
                                        $fancybox_link = $business_info_file;
                                    } else if ($business_info_banner_type === 'video-file') {
                                        $fancybox_link = $business_video_file;
                                    } else if ($business_info_banner_type === 'video-youtube') {
                                        $fancybox_link = $youtube_video_url;
                                    } ?>
                                    
                                    <a class="fancybox fancybox.iframe <?php echo $fancybox_class; ?> business-logo-link" 
                                        href="<?php echo $fancybox_link; ?>" content="<p>">
                                        <?php echo $business_logo; ?>
                                        <div class="heading"><?php _e("More info", 'ifind'); ?></div>
                                    </a>
                                </div>
                                <div class="ifind-business-desc">
                                    <div class="heading"><?php echo $business_name; ?></div>
                                    <div class="ifind-business-desc-content"><?php echo $business_desc; ?></div>
                                </div>
                                <div class="ifind-business-direction">
                                    <a href="#" class="business-direction-link" 
                                                data-lat="<?php echo $business_lat; ?>" 
                                                data-lng="<?php echo $business_lng; ?>"
                                                title="<?php printf(__("Directions from %s to %s",'ifind'), ifind_removeSpecialChapr(get_the_title()), ifind_removeSpecialChapr(get_the_title($business_id))); ?>" >
                                        <div class="heading"><?php _e("Directions",'ifind') ?></div>
                                        <img class="ifind-business-icon" src="<?php echo TVLGIAO_WPDANCE_THEME_IMAGES.'/directions.png'; ?>">
                                        <div class="business-distance heading"></div>
                                    </a>
                                </div>
                                <div class="ifind-business-geocoding">
                                    <a href="#" class="business-geocoding-link" 
                                                data-icon="<?php echo $map_marker_icon_url; ?>" 
                                                data-address="<?php echo htmlspecialchars($business_address); ?>" 
                                                data-lat="<?php echo $business_lat; ?>" 
                                                data-lng="<?php echo $business_lng; ?>"
                                                title="<?php _e("Show this location on map",'ifind') ?>">
                                        <div class="heading"><?php _e("Show On",'ifind') ?></div>
                                        <img class="ifind-business-icon" src="<?php echo TVLGIAO_WPDANCE_THEME_IMAGES.'/marker.png'; ?>">
                                        <div class="heading"><?php _e("Map",'ifind') ?></div>
                                    </a>
                                </div>

                                <div class="ifind-business-contact">
                                    <a class="fancybox-contact-us" data-business-email="<?php echo $business_email; ?>" href="<?php echo $qr_link; ?>">
                                        <div class="heading"><?php _e("Contact",'ifind') ?></div>
                                        <img class="ifind-business-icon" src="<?php echo TVLGIAO_WPDANCE_THEME_IMAGES.'/contact.png'; ?>">
                                        <div class="heading"><?php _e("Us",'ifind') ?></div>
                                    </a>
                                </div>
                                <!-- <div class="ifind-business-qrcode">
                                    <a class="fancybox fancybox.iframe ifind-fancybox-image" href="<?php //echo $qr_link; ?>">
                                        <div class="heading"><?php //_e("QR Code",'ifind') ?></div>
                                        <img class="ifind-business-icon" src="<?php //echo TVLGIAO_WPDANCE_THEME_IMAGES.'/qrcode.png'; ?>">
                                        <div class="heading"><?php //_e("Scan",'ifind') ?></div>
                                    </a>
                                </div> -->
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