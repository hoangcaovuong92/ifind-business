<?php
$business_metadata = ifind_business_custom_metadata(get_the_ID());
?>
<section class="ifind-slider">
    <div class="footerSlider">
        <div class="ifind-footerSlider-container">
            <?php if (count($business_metadata) > 0) {
                foreach ($business_metadata as $business_id => $business_metadata) { 
                    $business_info_banner = wp_get_attachment_image_url( $business_metadata['info_banner'], 'full' );
                    $fancybox_class = 'ifind-fancybox-image';
                    $fancybox_link = $business_info_banner;
                    ?>
                    <div class="ifind-counter-item ifind-footerSlider-item" 
                            data-business-id="<?php echo $business_id; ?>" 
                            data-location-id="<?php echo get_the_ID(); ?>"
                            data-counter-position="footer-slider">
                        <div class="inner">
                            <a class="fancybox fancybox.iframe <?php echo $fancybox_class; ?> business-logo-link" href="<?php echo $fancybox_link; ?>" content="<p>">
                                <?php echo wp_get_attachment_image($business_metadata['small_banner'], 'small_banner' ); ?>
                            </a>
                        </div>
                    </div>
                <?php
                }
            } ?>
        </div>
    </div>
</section>

