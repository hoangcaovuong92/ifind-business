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