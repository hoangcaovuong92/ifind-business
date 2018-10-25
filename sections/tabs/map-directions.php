 <!-- Display Text Directions -->
 <div id="map-directions">
    <div class="map-directions-wrap">
        <span class="directions-close"><?php _e('x','ifind'); ?></span>
        <div id="map-directions-content-wrap">
            <button class="btn btn-primary map-directions-email-send" style=""><?php _e("Email me directions", 'ifind'); ?></i></button>

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