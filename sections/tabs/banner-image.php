<?php $style = 'style-4'; ?>
<div class="ifind-business-title-banner <?php echo $style; ?>">
    <?php 
    if (count($list_data) > 0) {
        foreach ($list_data as $business_group_parent_id => $business_group_meta_data) {
            $business_group_parent_name = get_term_by('id', $business_group_parent_id, 'business_group')->name;
            $background_img = wp_get_attachment_image( $business_group_meta_data['banner_image'], 'full' );
            $background_img_url = wp_get_attachment_image_url( $business_group_meta_data['banner_image'], 'tab_banner_image' );
            $avatar_img_url = wp_get_attachment_image_url( $business_group_meta_data['avatar_image'], 'tab_avatar_image' );
            $map_marker_icon = wp_get_attachment_image( $business_group_meta_data['map_marker_icon'], 'map_logo' );
            $font_awsome = '<i class="'.$business_group_meta_data['font_icon_class'].'" aria-hidden="true"></i>';
            echo '<div class="ifind-business-title-banner-item main-timeline" data-category-id="'.$business_group_parent_id.'">';
            if ($style === 'style-default') {
                echo '<a href="#" class="business-group-banner-link">'.wp_get_attachment_image( $business_group_meta_data['banner_image'], 'full' ).'</a>';
            } else if ($style === 'style-2') {
                
                echo $background_img; 
                echo '<a href="#" class="business-group-banner-link">';
                echo '<span>'.$business_group_parent_name.'</span></a>';
            } else if ($style === 'style-3') { ?>
                <div class="timeline">
                    <a href="#" class="timeline-content">
                        <div class="timeline-icon">
                        <?php echo $font_awsome; ?>
                        </div>
                        <div class="inner-content">
                            <h3 class="title"><?php echo $business_group_parent_name; ?></h3>
                            <p class="description">
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias animi dolor in, maiores natus
                            </p>
                        </div>
                    </a>
                </div>
            <?php
            } else if ($style === 'style-4') { ?>
                <div class="timeline">
                    <a href="#" class="timeline-content" <?php echo ($background_img_url) ? 'style="background-image: url('.$background_img_url.')"' : ''; ?>>
                        <h3 class="title"><span><?php echo $business_group_parent_name; ?> <?php echo ($avatar_img_url) ? $font_awsome : ''; ?></span></h3>
                        <div class="timeline-icon" <?php echo ($avatar_img_url) ? 'style="background-image: url('.$avatar_img_url.')"' : ''; ?>>
                            <div class="timeline-icon-img">
                                <?php echo !($avatar_img_url) ? $font_awsome : ''; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php
            }
            echo '</div>';
        }
    }
    ?>
</div>