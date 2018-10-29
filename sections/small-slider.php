<?php
$business_metadata = ifind_business_custom_metadata(get_the_ID());
?>
<section class="ifind-slider">
    <div class="smallSlider">
        <div class="ifind-smallSlider-container">
            <?php if (count($business_metadata) > 0) {
                foreach ($business_metadata as $business_id => $business_metadata) { 
                    if (!empty($business_metadata['small_banner']) && !empty($business_metadata['large_banner'])) { ?>
                        <div class="ifind-counter-item ifind-smallSlider-item" 
                            data-business-id="<?php echo $business_id; ?>" 
                            data-location-id="<?php echo get_the_ID(); ?>"
                            data-counter-position="small-slider">
                            <div class="inner">
                                <?php echo wp_get_attachment_image($business_metadata['small_banner'], 'small_banner' ); ?>
                            </div>
                        </div>
                <?php
                    }
                }
            } ?>
        </div>
    </div>
</section>

