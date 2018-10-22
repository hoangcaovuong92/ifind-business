<div class="ifind-business-title-banner">
    <?php 
    if (count($list_data) > 0) {
        foreach ($list_data as $business_group_parent_id => $business_group_meta_data) {
            echo '<a href="#" class="business-group-banner-link" data-category-id="'.$business_group_parent_id.'">'.wp_get_attachment_image( $business_group_meta_data['banner_image'], 'full' ).'</a>';
        }
    }
    ?>
</div>