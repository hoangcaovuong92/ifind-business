<div id="map-directions-email" class="map-directions-email">
    <div class="map-directions-email-form">
        <form id="send-directions-form" class="ifind-validator-form" method="post" action="" role="form">
            <div class="form-group">
                <input type="email" name="email" class="form-control input-lg" 
                        placeholder="<?php _e("Please enter your email *", 'ifind'); ?>" autocomplete="off" 
                        required="required" 
                        data-error="<?php _e("Valid email is required.", 'ifind'); ?>">
                <div class="help-block with-errors"></div>
                <div class="softkeys" data-target="input[name='email']"></div>
                <input type="hidden" name="title">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-success btn-lg" value="<?php _e("Send", 'ifind'); ?>">
            </div>
        </form>
        <div class="ifind-fancybox-close send-directions-form-close"><?php _e('x','ifind'); ?></div>
    </div>
</div>