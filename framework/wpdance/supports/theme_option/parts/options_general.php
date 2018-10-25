<?php 
Redux::setSection( $opt_name, array(
    'title'            => __( 'Google API', 'ifind' ),
    'id'               => 'ifind_google_map_api_section',
    'desc'             => __( '', 'ifind' ),
    'customizer_width' => '400px',
    'icon'             => 'el el-map-marker',
    'fields'     => array(
        array(
            'id'       => 'ifind_google_map_api_key',
            'type'     => 'text',
            'title'    => __( 'Google Map API Key', 'ifind' ),
            'subtitle' => __( '', 'ifind' ),
            'desc'     => __( '', 'ifind' ),
            'default'  => $wd_default_data['google_map']['default']['api_key'],
        ),
        array(
            'id'       => 'ifind_google_map_zoom',
            'type'     => 'text',
            'title'    => __( 'Map Zoom', 'ifind' ),
            'subtitle' => __( '', 'ifind' ),
            'desc'     => __( '', 'ifind' ),
            'default'  => $wd_default_data['google_map']['default']['zoom'],
        ),
    ) 
) );

Redux::setSection( $opt_name, array(
    'title'            => __( 'Weather API', 'ifind' ),
    'id'               => 'ifind_weather_api_section',
    'desc'             => __( '', 'ifind' ),
    'customizer_width' => '400px',
    'icon'             => 'el el-cloud',
    'fields'     => array(
        array(
            'id'       => 'ifind_weather_api_key',
            'type'     => 'text',
            'title'    => __( 'Openweathermap API Key', 'ifind' ),
            'subtitle' => __( '', 'ifind' ),
            'desc'     => __( '', 'ifind' ),
            'default'  => $wd_default_data['weather']['default']['api_key'],
        ),
        array(
            'id'       => 'ifind_weather_update_time',
            'type'     => 'text',
            'title'    => __( 'Auto Update Time', 'ifind' ),
            'subtitle' => __( '', 'ifind' ),
            'desc'     => __( 'Unit: ms (1000ms = 1s)', 'ifind' ),
            'default'  => $wd_default_data['weather']['default']['update_time'],
        ),
    ) 
) );

Redux::setSection( $opt_name, array(
    'title'            => __( 'Slider Settings', 'ifind' ),
    'id'               => 'ifind_time_settings',
    'desc'             => __( '', 'ifind' ),
    'icon'             => 'el el-time',
    'customizer_width' => '400px',
    'fields'     => array(
        array(
            'id'       => 'ifind_slider_timerShowPopup',
            'type'     => 'text',
            'title'    => __( 'Show Popup After', 'ifind' ),
            'subtitle' => __( 'Waiting time before show large popup slider.', 'ifind' ),
            'desc'     => __( 'Unit: ms (1000ms = 1s)', 'ifind' ),
            'default'  => $wd_default_data['slider']['default']['timerShowPopup'],
        ),
        array(
            'id'       => 'ifind_slider_timerShowPopupViewingInfo',
            'type'     => 'text',
            'title'    => __( 'Show Popup After (When watching information)', 'ifind' ),
            'subtitle' => __( 'Waiting time before show large popup slider.', 'ifind' ),
            'desc'     => __( 'Unit: ms (1000ms = 1s)', 'ifind' ),
            'default'  => $wd_default_data['slider']['default']['timerShowPopupViewingInfo'],
        ),
        array(
            'id'       => 'ifind_slider_timerDelayPopup',
            'type'     => 'text',
            'title'    => __( 'Popup Delay', 'ifind' ),
            'subtitle' => __( 'Time delay after slider break.', 'ifind' ),
            'desc'     => __( 'Unit: ms (1000ms = 1s)', 'ifind' ),
            'default'  => $wd_default_data['slider']['default']['timerDelayPopup'],
        ),
        array(
            'id'       => 'ifind_slider_numSliderBreak',
            'type'     => 'text',
            'title'    => __( 'Break Popup Slider After Item', 'ifind' ),
            'subtitle' => __( 'Slider will break after number slide.', 'ifind' ),
            'desc'     => __( '', 'ifind' ),
            'default'  => $wd_default_data['slider']['default']['numSliderBreak'],
        ),
        array(
            'id'       => 'ifind_slider_bigAutoplaySpeed',
            'type'     => 'text',
            'title'    => __( 'Popup Slider Autoplay Speed', 'ifind' ),
            'subtitle' => __( '', 'ifind' ),
            'desc'     => __( 'Unit: ms (1000ms = 1s)', 'ifind' ),
            'default'  => $wd_default_data['slider']['default']['bigAutoplaySpeed'],
        ),
        array(
            'id'       => 'ifind_slider_smallAutoplaySpeed',
            'type'     => 'text',
            'title'    => __( 'Top Slider Autoplay Speed', 'ifind' ),
            'subtitle' => __( '', 'ifind' ),
            'desc'     => __( 'Unit: ms (1000ms = 1s)', 'ifind' ),
            'default'  => $wd_default_data['slider']['default']['smallAutoplaySpeed'],
        ),
        array(
            'id'       => 'ifind_slider_numFooterSliderItems',
            'type'     => 'text',
            'title'    => __( 'Footer Slider Items', 'ifind' ),
            'subtitle' => __( 'Slide number of footer slider.', 'ifind' ),
            'desc'     => __( '', 'ifind' ),
            'default'  => $wd_default_data['slider']['default']['numFooterSliderItems'],
        ),
    ) 
) );