<?php
$business_metadata = ifind_business_custom_metadata(get_the_ID());
?>
<section class="ifind-slider">
    <div id="popupSlider" class="popupSlider">
        <div class="ifind-sliderPop-container">
            <?php if (count($business_metadata) > 0) {
                foreach ($business_metadata as $business_id => $business_metadata) { 
                    if (!empty($business_metadata['small_banner']) && !empty($business_metadata['large_banner'])) { ?>
                        <div class="ifind-sliderPop-item">
                            <div class="inner">
                                <?php echo wp_get_attachment_image($business_metadata['large_banner'], 'full', "", ["class" => "big-popup-slider-image"] ); ?>
                            </div>
                        </div>
                <?php
                    }
                }
            } ?>
        </div>
    </div>
</section>

