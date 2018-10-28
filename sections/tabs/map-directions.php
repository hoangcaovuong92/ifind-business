 <!-- Display Text Directions -->
 <div id="map-directions">
    <div class="map-directions-wrap">
        <span class="directions-close"><span class="arrow"><span></span></span><?php _e("Back", 'ifind'); ?></span>
        <div id="map-directions-content-wrap">
            <a class="btn btn-primary map-directions-email-send" href="#map-directions-email" content="<p>">
                <?php _e("Email me directions", 'ifind'); ?>
            </a>

            <div class="map-directions-header">
                <div class="map-directions-distance"><span><?php _e("Distance:", 'ifind'); ?></span> <b><span class="content"></span></b></div>
                <div class="map-directions-duration"><span><?php _e("Duration:", 'ifind'); ?></span> <b><span class="content"></span></b></div>
                <div class="map-directions-start-address"><span><?php _e("Start:", 'ifind'); ?></span> <b><span class="content"></span></b></div>
                <div class="map-directions-end-address"><span><?php _e("End:", 'ifind'); ?></span> <b><span class="content"></span></b></div>
            </div>
            <div class="map-directions-content"></div>
        </div>
    </div>
</div>