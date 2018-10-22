 <!-- Display Text Directions -->
 <div id="map-directions">
    <div class="map-directions-wrap">
        <span class="directions-close"><?php _e('x','ifind'); ?></span>
        <div id="map-directions-content-wrap">
            <div class="map-directions-email">
                <button class="btn btn-primary map-directions-email-send" style=""><?php _e("Email me directions", 'ifind'); ?></i></button>
                <div class="map-directions-email-form">
                    <form id="send-directions-form" method="post" action="" role="form">
                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="Please enter your email *" autocomplete="off" required="required" data-error="<?php _e("Valid email is required.", 'ifind'); ?>">
                            <input type="hidden" name="title">
                            <div class="help-block with-errors"></div>

                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-success" value="<?php _e("Send", 'ifind'); ?>">
                        </div>
                    </form>
                </div>
            </div>

            <div class="map-directions-header">
                <div class="map-directions-distance"><span><?php _e("Distance:", 'ifind'); ?></span> <b><span class="content"></span></b></div>
                <div class="map-directions-duration"><span><?php _e("Duration:", 'ifind'); ?></span> <b><span class="content"></span></b></div>
                <div class="map-directions-start-address"><span><?php _e("Start Address:", 'ifind'); ?></span> <b><span class="content"></span></b></div>
                <div class="map-directions-end-address"><span><?php _e("End Address:", 'ifind'); ?></span> <b><span class="content"></span></b></div>
            </div>
            <div class="map-directions-content"></div>
        </div>
    </div>
</div>