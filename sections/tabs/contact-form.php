<div id="ifind-contact-form-wrap" style="display: none;">
    <form id="ifind-contact-form" class="ifind-validator-form" method="post" action="" role="form">
        <h2><?php _e("First Step", 'ifind'); ?></h2>
        <section>
            <div class="form-group">    
                <input type="email" name="contact-email" class="form-control input-lg" 
                        placeholder="<?php _e("Please enter your email *", 'ifind'); ?>" autocomplete="off" 
                        required="required" 
                        data-error="<?php _e("Valid email is required.", 'ifind'); ?>">
                <div class="help-block with-errors"></div>
                <div class="softkeys" data-target="input[name='contact-email']"></div>
            </div>
        </section>

        <h2><?php _e("Second Step", 'ifind'); ?></h2>
        <section>
            <div class="form-group">    
                <input type="text" name="contact-phone" class="form-control input-lg" 
                        placeholder="<?php _e("Please enter your phone *", 'ifind'); ?>" autocomplete="off" 
                        required="required" 
                        data-error="<?php _e("Valid phone is required.", 'ifind'); ?>">
                <div class="help-block with-errors"></div>
                <div class="softkeys" data-target="input[name='contact-phone']"></div>
            </div>
        </section>

        <h2><?php _e("Third Step", 'ifind'); ?></h2>
        <section>
            <div class="form-group">    
                <textarea name="contact-message" class="form-control input-lg" rows="3"
                        placeholder="<?php _e("Please enter your message *", 'ifind'); ?>" autocomplete="off" 
                        required="required" 
                        data-error="<?php _e("Valid message is required.", 'ifind'); ?>"></textarea>
                <div class="help-block with-errors"></div>
                <div class="softkeys" data-target="textarea[name='contact-message']"></div>
                <input type="hidden" name="email_to">
                <input type="hidden" name="cc_to">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-success btn-lg" value="<?php _e("Send", 'ifind'); ?>">
            </div>
        </section>
    </form>
    <div id="ifind-qr-code">
        <img alt="">
    </div>
</div>