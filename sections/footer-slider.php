<?php
$business_metadata = ifind_business_custom_metadata(get_the_ID());
?>
<section class="ifind-slider">
    <div class="footerSlider">
        <div class="ifind-footerSlider-container">
            <?php if (count($business_metadata) > 0) {
                foreach ($business_metadata as $business_id => $business_metadata) { 
                    $business_info_banner = wp_get_attachment_image_url( $business_metadata['info_banner'], 'full' );
                    $youtube_video_id = $business_metadata['youtube_video_id'];
                    $youtube_video_url = 'http://www.youtube.com/embed/'.$youtube_video_id.'?enablejsapi=1&rel=0&modestbranding=0&wmode=opaque&showinfo=0&controls=0';
                    $fancybox_class = ($youtube_video_id) ? 'ifind-fancybox-video' : 'ifind-fancybox-image';
                    $fancybox_link = ($youtube_video_id) ? $youtube_video_url : $business_info_banner;
                    ?>
                    <div class="ifind-footerSlider-item">
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

