<div class="map-directions-email">
    <div class="map-directions-email-form">
        <form id="send-directions-form" method="post" action="" role="form">
            <div class="form-group">
                <input type="email" name="email" class="form-control input-lg" placeholder="Please enter your email *" autocomplete="off" required="required" data-error="<?php _e("Valid email is required.", 'ifind'); ?>">
                <div class="softkeys" data-target="input[name='email']"></div>
                <input type="hidden" name="title">
                <div class="help-block with-errors"></div>

            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-success btn-lg" value="<?php _e("Send", 'ifind'); ?>">
            </div>
        </form>
        <div class="send-directions-form-close"><?php _e('x','ifind'); ?></div>
    </div>
</div>