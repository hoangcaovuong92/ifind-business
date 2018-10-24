<?php 
$location_meta_data = ifind_get_post_custom_metadata(get_the_ID(), 'location');
$location_lat = $location_meta_data['location_data']['lat'];
$location_lng = $location_meta_data['location_data']['lng'];
$location_address = $location_meta_data['location_data']['address'];
$list_business_id = $location_meta_data['list_business'];
?>
<div class="ifind-list-business">
    <?php 
    $taxonomy = 'business_group';
    $options = array(
        'parent' => 0,
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'meta_key'      => 'order_index',
        'meta_compare'  => 'NUMERIC',
    );
    $list_business_group_parent = ifind_get_list_category($taxonomy, $options); 
    $list_data = array();
    if (count($list_business_group_parent) > 0) {
        foreach ($list_business_group_parent as $term_id => $name) {
            $term = get_term( $term_id, $taxonomy );
            $list_data[$term_id]['name'] = $term->name;
            $list_data[$term_id]['map_marker_icon'] = get_term_meta($term_id, 'map_marker_icon', true)['id'];
            $list_data[$term_id]['banner_image'] = get_term_meta($term_id, 'banner_image', true)['id'];
            $list_data[$term_id]['category_color'] = get_term_meta($term_id, 'category_color', true);
        }
    }
    require_once TVLGIAO_WPDANCE_THEME_DIR."/sections/tabs/banner-image.php"; ?>

    <div id="ifind-business-tabs" class="wow fadeInUp" data-wow-duration="2s">	
        <?php 
        require_once TVLGIAO_WPDANCE_THEME_DIR."/sections/tabs/back-link.php";
        require_once TVLGIAO_WPDANCE_THEME_DIR."/sections/tabs/map-directions.php";
        require_once TVLGIAO_WPDANCE_THEME_DIR."/sections/tabs/tab-nav.php";
        require_once TVLGIAO_WPDANCE_THEME_DIR."/sections/tabs/tab-content.php";
        ?>
    </div>
</div>

<script src="http://www.youtube.com/player_api"></script>
<script>
    // Fires whenever a player has finished loading
    function onPlayerReady(event) {
        event.target.mute();
        event.target.playVideo();
    }

    // Fires when the player's state changes.
    function onPlayerStateChange(event) {
        // Go to the next video after the current one is finished playing
        if (event.data === 0 || event.data === 2) {
            jQuery.fancybox.close();
        } 
    }

    // The API will call this function when the page has finished downloading the JavaScript for the player API
    function onYouTubePlayerAPIReady() {
        // Initialise the fancyBox after the DOM is loaded
        jQuery(document).ready(function() {
            var fancybox_video = ".ifind-fancybox-video";
            jQuery(fancybox_video).fancybox({
                    openEffect  : 'fade',
                    closeEffect : 'fade',
                    padding     : 0,
                    margin      : [-60, 0, 0, 0],
                    fitToView   : false,
                    autoSize    : false,
                    width       : '100%',
                    height      : '100%',
                    closeBtn    : false,
                    arrows      : false,
                    mobile : {
                        margin      : [0, 0, 0, 0]
                    },
                    beforeShow  : function() {
                        // Find the iframe ID
                        var id = jQuery.fancybox.inner.find('iframe').attr('id');
                        
                        // Create video player object and add event listeners
                        var player = new YT.Player(id, {
                            playerVars: { 'autoplay': 1, 'controls': 0 },
                            events: {
                                'onReady': onPlayerReady,
                                'onStateChange': onPlayerStateChange
                            }
                        });
                    },
                    afterClose: function() {
                    }
                });

            jQuery(fancybox_video).on("click", function(){
                // Launch fancyBox
                jQuery(fancybox_video).eq(0).trigger('click');
            });
        })
    }
</script>