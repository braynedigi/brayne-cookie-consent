<?php
/**
 * Plugin Name: Brayne Cookie Consent
 * Plugin URI: https://braynedigital.com/
 * Description: Easy to configure cookie consent banner with GDPR and CCPA law support. Professional design with ADVANCED RESPONSIVE customization for Desktop, Tablet, and Mobile. Zero coding required.
 * Version: 1.6.0
 * Author: Brayne Digital
 * Author URI: https://braynedigital.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: brayne-cookie-consent
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Tested up to: 6.4
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('SCC_VERSION', '1.6.0');
define('SCC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SCC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SCC_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Get responsive value for a setting
 */
function scc_get_responsive_value($options, $key, $default, $device = 'desktop') {
    $device_key = $key . '_' . $device;
    
    // Check if responsive setting exists
    if (isset($options[$device_key]) && $options[$device_key] !== '') {
        return $options[$device_key];
    }
    
    // Fall back to base setting
    if (isset($options[$key]) && $options[$key] !== '') {
        return $options[$key];
    }
    
    return $default;
}

/**
 * Check if banner should display on current page
 */
function scc_should_display_banner() {
    $options = get_option('scc_options', array());
    $display_mode = isset($options['display_mode']) ? $options['display_mode'] : 'all_pages';
    $selected_pages = isset($options['selected_pages']) ? $options['selected_pages'] : array();
    $excluded_pages = isset($options['excluded_pages']) ? $options['excluded_pages'] : array();
    
    // Get current page ID
    $current_page_id = get_queried_object_id();
    
    switch ($display_mode) {
        case 'all_pages':
            return true;
            
        case 'homepage_only':
            return is_front_page() || is_home();
            
        case 'specific_pages':
            if (empty($selected_pages)) {
                return true; // If no pages selected, show everywhere
            }
            return in_array($current_page_id, $selected_pages);
            
        case 'exclude_pages':
            if (empty($excluded_pages)) {
                return true; // If no pages excluded, show everywhere
            }
            return !in_array($current_page_id, $excluded_pages);
            
        case 'all_except_homepage':
            return !(is_front_page() || is_home());
            
        default:
            return true;
    }
}

/**
 * Display the cookie consent banner
 */
function scc_display_cookie_banner() {
    // Check if user has already accepted cookies
    if (isset($_COOKIE['brayne_cookie_consent'])) {
        return; // Don't show banner if already accepted
    }
    
    // Check display conditions
    if (!scc_should_display_banner()) {
        return; // Don't show if conditions not met
    }

    // Get plugin options with defaults
    $options = get_option('scc_options', array());
    
    // Content settings
    $banner_text = isset($options['banner_text']) ? $options['banner_text'] : 'We use cookies to improve your experience on our site. By continuing to browse, you agree to our use of cookies.';
    $banner_title = isset($options['banner_title']) ? $options['banner_title'] : '🍪 We use cookies';
    $accept_text = isset($options['accept_text']) ? $options['accept_text'] : 'Accept All Cookies';
    $decline_text = isset($options['decline_text']) ? $options['decline_text'] : 'Decline';
    $show_decline = isset($options['show_decline']) ? $options['show_decline'] : true;
    $cookie_duration = isset($options['cookie_duration']) ? intval($options['cookie_duration']) : 365;
    
    // Banner styling
    $banner_position = isset($options['banner_position']) ? $options['banner_position'] : 'bottom';
    $banner_bg_color = isset($options['banner_bg_color']) ? $options['banner_bg_color'] : '#ffffff';
    $border_color = isset($options['border_color']) ? $options['border_color'] : '#E1195B';
    $border_width = isset($options['border_width']) ? $options['border_width'] : '3';
    
    
    // Card-specific styling
    $card_max_width = isset($options['card_max_width']) ? $options['card_max_width'] : '400';
    $card_border_radius = isset($options['card_border_radius']) ? $options['card_border_radius'] : '12';
    $card_padding_v = isset($options['card_padding_v']) ? $options['card_padding_v'] : '20';
    $card_padding_h = isset($options['card_padding_h']) ? $options['card_padding_h'] : '20';
    $card_button_gap = isset($options['card_button_gap']) ? $options['card_button_gap'] : '10';
    $card_text_align = isset($options['card_text_align']) ? $options['card_text_align'] : 'center';
    
    // Typography
    $font_family = isset($options['font_family']) ? $options['font_family'] : 'inherit';
    $title_color = isset($options['title_color']) ? $options['title_color'] : '#222222';
    $text_color = isset($options['text_color']) ? $options['text_color'] : '#333333';
    $link_color = isset($options['link_color']) ? $options['link_color'] : '#E1195B';
    $link_hover_color = isset($options['link_hover_color']) ? $options['link_hover_color'] : '#48144A';
    
    // Responsive typography - Desktop
    $title_size_desktop = scc_get_responsive_value($options, 'title_size', '16', 'desktop');
    $text_size_desktop = scc_get_responsive_value($options, 'text_size', '14', 'desktop');
    
    // Responsive typography - Tablet
    $title_size_tablet = scc_get_responsive_value($options, 'title_size', '15', 'tablet');
    $text_size_tablet = scc_get_responsive_value($options, 'text_size', '13', 'tablet');
    
    // Responsive typography - Mobile
    $title_size_mobile = scc_get_responsive_value($options, 'title_size', '14', 'mobile');
    $text_size_mobile = scc_get_responsive_value($options, 'text_size', '12', 'mobile');
    
    // Accept button styling
    $accept_bg_color = isset($options['accept_bg_color']) ? $options['accept_bg_color'] : '#E1195B';
    $accept_text_color = isset($options['accept_text_color']) ? $options['accept_text_color'] : '#ffffff';
    $accept_hover_bg = isset($options['accept_hover_bg']) ? $options['accept_hover_bg'] : '#48144A';
    $accept_hover_text = isset($options['accept_hover_text']) ? $options['accept_hover_text'] : '#ffffff';
    
    // Decline button styling
    $decline_bg_color = isset($options['decline_bg_color']) ? $options['decline_bg_color'] : '#f5f5f5';
    $decline_text_color = isset($options['decline_text_color']) ? $options['decline_text_color'] : '#666666';
    $decline_hover_bg = isset($options['decline_hover_bg']) ? $options['decline_hover_bg'] : '#e0e0e0';
    $decline_hover_text = isset($options['decline_hover_text']) ? $options['decline_hover_text'] : '#333333';
    
    // Responsive button styling - Desktop
    $button_radius_desktop = scc_get_responsive_value($options, 'button_radius', '5', 'desktop');
    $button_size_desktop = scc_get_responsive_value($options, 'button_size', '14', 'desktop');
    $button_padding_v_desktop = scc_get_responsive_value($options, 'button_padding_v', '12', 'desktop');
    $button_padding_h_desktop = scc_get_responsive_value($options, 'button_padding_h', '24', 'desktop');
    
    // Responsive button styling - Tablet
    $button_radius_tablet = scc_get_responsive_value($options, 'button_radius', '5', 'tablet');
    $button_size_tablet = scc_get_responsive_value($options, 'button_size', '13', 'tablet');
    $button_padding_v_tablet = scc_get_responsive_value($options, 'button_padding_v', '10', 'tablet');
    $button_padding_h_tablet = scc_get_responsive_value($options, 'button_padding_h', '20', 'tablet');
    
    // Responsive button styling - Mobile
    $button_radius_mobile = scc_get_responsive_value($options, 'button_radius', '5', 'mobile');
    $button_size_mobile = scc_get_responsive_value($options, 'button_size', '13', 'mobile');
    $button_padding_v_mobile = scc_get_responsive_value($options, 'button_padding_v', '12', 'mobile');
    $button_padding_h_mobile = scc_get_responsive_value($options, 'button_padding_h', '20', 'mobile');
    
    // General button styling
    $button_font_weight = isset($options['button_font_weight']) ? $options['button_font_weight'] : '600';
    
    // Responsive padding - Desktop
    $banner_padding_v_desktop = scc_get_responsive_value($options, 'banner_padding_v', '20', 'desktop');
    
    // Responsive padding - Tablet
    $banner_padding_v_tablet = scc_get_responsive_value($options, 'banner_padding_v', '18', 'tablet');
    
    // Responsive padding - Mobile
    $banner_padding_v_mobile = scc_get_responsive_value($options, 'banner_padding_v', '15', 'mobile');
    
    // Content direction (row/column) - responsive
    $content_direction_desktop = isset($options['content_direction_desktop']) ? $options['content_direction_desktop'] : 'row';
    $content_direction_tablet = isset($options['content_direction_tablet']) ? $options['content_direction_tablet'] : 'row';
    $content_direction_mobile = isset($options['content_direction_mobile']) ? $options['content_direction_mobile'] : 'column';
    
    // Button layout (horizontal/vertical)
    $button_layout_desktop = isset($options['button_layout_desktop']) ? $options['button_layout_desktop'] : 'horizontal';
    $button_layout_tablet = isset($options['button_layout_tablet']) ? $options['button_layout_tablet'] : 'horizontal';
    $button_layout_mobile = isset($options['button_layout_mobile']) ? $options['button_layout_mobile'] : 'vertical';
    
    // Shadow settings
    $box_shadow = isset($options['box_shadow']) ? $options['box_shadow'] : 'yes';
    
    ?>
    <div id="scc-cookie-banner" class="scc-cookie-banner scc-position-<?php echo esc_attr($banner_position); ?>">
        <div class="scc-cookie-content">
            <div class="scc-cookie-text">
                <p>
                    <strong class="scc-title"><?php echo esc_html($banner_title); ?></strong><br>
                    <span class="scc-text"><?php echo esc_html($banner_text); ?></span>
                    <?php if (get_privacy_policy_url()) : ?>
                        <a href="<?php echo esc_url(get_privacy_policy_url()); ?>" class="scc-link" target="_blank"><?php _e('Learn more', 'brayne-cookie-consent'); ?></a>
                    <?php endif; ?>
                </p>
            </div>
            <div class="scc-cookie-buttons">
                <button id="scc-cookie-accept" class="scc-cookie-btn scc-cookie-accept" 
                        data-duration="<?php echo esc_attr($cookie_duration); ?>">
                    <?php echo esc_html($accept_text); ?>
                </button>
                <?php if ($show_decline) : ?>
                    <button id="scc-cookie-decline" class="scc-cookie-btn scc-cookie-decline"
                            data-duration="<?php echo esc_attr($cookie_duration); ?>">
                        <?php echo esc_html($decline_text); ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <style>
        /* ========================================
           BASE STYLES (DESKTOP - Above 1024px)
        ======================================== */
        .scc-cookie-banner {
            position: fixed;
            background: <?php echo esc_attr($banner_bg_color); ?>;
            <?php if ($box_shadow == 'yes') : ?>
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            <?php endif; ?>
            padding: <?php echo esc_attr($banner_padding_v_desktop); ?>px 20px;
            z-index: 999999;
            <?php if ($font_family != 'inherit') : ?>
            font-family: <?php echo esc_attr($font_family); ?>;
            <?php endif; ?>
            border-radius: 8px;
        }
        
        /* Full Width Positions */
        .scc-cookie-banner.scc-position-bottom,
        .scc-cookie-banner.scc-position-top {
            left: 0;
            right: 0;
            border-radius: 0;
        }
        
        .scc-cookie-banner.scc-position-bottom {
            bottom: 0;
            animation: sccSlideUp 0.5s ease-out;
            border-top: <?php echo esc_attr($border_width); ?>px solid <?php echo esc_attr($border_color); ?>;
        }
        
        .scc-cookie-banner.scc-position-top {
            top: 0;
            animation: sccSlideDown 0.5s ease-out;
            border-bottom: <?php echo esc_attr($border_width); ?>px solid <?php echo esc_attr($border_color); ?>;
        }
        
        /* Card Positions - Bottom Corners Only */
        .scc-cookie-banner.scc-position-bottom-left,
        .scc-cookie-banner.scc-position-bottom-right {
            bottom: 20px;
            max-width: <?php echo esc_attr($card_max_width); ?>px;
            width: auto;
            min-width: 300px;
            border: <?php echo esc_attr($border_width); ?>px solid <?php echo esc_attr($border_color); ?>;
            padding: 0;
            border-radius: <?php echo esc_attr($card_border_radius); ?>px;
        }
        
        .scc-cookie-banner.scc-position-bottom-left {
            left: 20px;
            animation: sccSlideInLeft 0.5s ease-out;
        }
        
        .scc-cookie-banner.scc-position-bottom-right {
            right: 20px;
            animation: sccSlideInRight 0.5s ease-out;
        }

        /* Animations */
        @keyframes sccSlideUp {
            from { transform: translateY(100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes sccSlideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes sccSlideInLeft {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes sccSlideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .scc-cookie-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        
        /* Card content should not be limited by the general max-width */
        .scc-position-bottom-left .scc-cookie-content,
        .scc-position-bottom-right .scc-cookie-content {
            max-width: none;
        }
        
        /* Full width banner content */
        .scc-position-bottom .scc-cookie-content,
        .scc-position-top .scc-cookie-content {
            padding: 0 20px;
        }
        
        /* Content direction control - Desktop */
        .scc-cookie-content {
            flex-direction: <?php echo esc_attr($content_direction_desktop); ?>;
        }
        
        <?php if ($content_direction_desktop == 'column') : ?>
        .scc-cookie-content {
            text-align: center;
        }
        
        .scc-cookie-buttons {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        <?php endif; ?>
        
        /* Card banner content - use content direction setting - Desktop */
        .scc-position-bottom-left .scc-cookie-content,
        .scc-position-bottom-right .scc-cookie-content {
            flex-direction: <?php echo esc_attr($content_direction_desktop); ?>;
            text-align: <?php echo esc_attr($card_text_align); ?>;
            max-width: none;
            width: 100%;
            padding: <?php echo esc_attr($card_padding_v); ?>px <?php echo esc_attr($card_padding_h); ?>px;
        }
        
        <?php if ($content_direction_desktop == 'column') : ?>
        .scc-position-bottom-left .scc-cookie-content,
        .scc-position-bottom-right .scc-cookie-content {
            text-align: center;
        }
        <?php endif; ?>
        
        .scc-position-bottom-left .scc-cookie-buttons,
        .scc-position-bottom-right .scc-cookie-buttons {
            width: 100%;
            <?php if ($button_layout_desktop == 'vertical') : ?>
            flex-direction: column;
            <?php else : ?>
            flex-direction: row;
            <?php endif; ?>
            gap: <?php echo esc_attr($card_button_gap); ?>px;
        }
        
        <?php if ($content_direction_desktop == 'row') : ?>
        .scc-position-bottom-left .scc-cookie-buttons,
        .scc-position-bottom-right .scc-cookie-buttons {
            width: auto;
            flex-shrink: 0;
        }
        <?php endif; ?>
        
        .scc-position-bottom-left .scc-cookie-btn,
        .scc-position-bottom-right .scc-cookie-btn {
            <?php if ($button_layout_desktop == 'vertical') : ?>
            width: 100%;
            <?php else : ?>
            flex: 1;
            <?php endif; ?>
        }

        .scc-cookie-text {
            flex: 1;
        }

        .scc-cookie-text p {
            margin: 0;
            line-height: 1.6;
        }

        /* Desktop Typography */
        .scc-cookie-text .scc-title {
            font-size: <?php echo esc_attr($title_size_desktop); ?>px;
            color: <?php echo esc_attr($title_color); ?>;
            font-weight: 700;
        }
        
        .scc-cookie-text .scc-text {
            color: <?php echo esc_attr($text_color); ?>;
            font-size: <?php echo esc_attr($text_size_desktop); ?>px;
        }

        .scc-cookie-text .scc-link {
            color: <?php echo esc_attr($link_color); ?>;
            text-decoration: underline;
            font-weight: 500;
        }

        .scc-cookie-text .scc-link:hover {
            color: <?php echo esc_attr($link_hover_color); ?>;
        }

        .scc-cookie-buttons {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
            <?php if ($button_layout_desktop == 'vertical') : ?>
            flex-direction: column;
            width: 100%;
            <?php else : ?>
            flex-direction: row;
            <?php endif; ?>
        }
        
        <?php if ($button_layout_desktop == 'vertical') : ?>
        /* Full-width banner buttons when vertical */
        .scc-position-bottom .scc-cookie-buttons,
        .scc-position-top .scc-cookie-buttons {
            max-width: 400px;
        }
        
        .scc-position-bottom .scc-cookie-btn,
        .scc-position-top .scc-cookie-btn {
            width: 100%;
        }
        <?php endif; ?>

        /* Desktop Button Styling */
        .scc-cookie-btn {
            padding: <?php echo esc_attr($button_padding_v_desktop); ?>px <?php echo esc_attr($button_padding_h_desktop); ?>px;
            border: none;
            border-radius: <?php echo esc_attr($button_radius_desktop); ?>px;
            cursor: pointer;
            font-size: <?php echo esc_attr($button_size_desktop); ?>px;
            font-weight: <?php echo esc_attr($button_font_weight); ?>;
            transition: all 0.3s ease;
            white-space: nowrap;
            <?php if ($font_family != 'inherit') : ?>
            font-family: <?php echo esc_attr($font_family); ?>;
            <?php endif; ?>
        }

        .scc-cookie-accept {
            background: <?php echo esc_attr($accept_bg_color); ?>;
            color: <?php echo esc_attr($accept_text_color); ?>;
        }

        .scc-cookie-accept:hover {
            background: <?php echo esc_attr($accept_hover_bg); ?>;
            color: <?php echo esc_attr($accept_hover_text); ?>;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .scc-cookie-decline {
            background: <?php echo esc_attr($decline_bg_color); ?>;
            color: <?php echo esc_attr($decline_text_color); ?>;
        }

        .scc-cookie-decline:hover {
            background: <?php echo esc_attr($decline_hover_bg); ?>;
            color: <?php echo esc_attr($decline_hover_text); ?>;
        }

        /* ========================================
           LARGE TABLET STYLES (900px - 1024px)
        ======================================== */
        @media (max-width: 1024px) and (min-width: 900px) {
            .scc-cookie-banner {
                padding: <?php echo esc_attr($banner_padding_v_tablet); ?>px 0;
            }
            
            /* Card positions - compact on large tablet */
            .scc-cookie-banner.scc-position-bottom-left,
            .scc-cookie-banner.scc-position-bottom-right {
                max-width: min(<?php echo esc_attr($card_max_width); ?>px, 80vw);
                width: auto;
                min-width: 300px;
            }
            
            /* Content direction control - Large Tablet */
            .scc-cookie-content {
                flex-direction: <?php echo esc_attr($content_direction_tablet); ?>;
            }
            
            <?php if ($content_direction_tablet == 'column') : ?>
            .scc-cookie-content {
                text-align: center;
            }
            
            .scc-cookie-buttons {
                width: 100%;
                max-width: 400px;
                margin: 0 auto;
            }
            <?php endif; ?>
            
            /* Card content direction - Large Tablet */
            .scc-position-bottom-left .scc-cookie-content,
            .scc-position-bottom-right .scc-cookie-content {
                flex-direction: <?php echo esc_attr($content_direction_tablet); ?>;
            }
            
            <?php if ($content_direction_tablet == 'column') : ?>
            .scc-position-bottom-left .scc-cookie-content,
            .scc-position-bottom-right .scc-cookie-content {
                text-align: center;
            }
            <?php endif; ?>
        }

        /* ========================================
           TABLET STYLES (768px - 899px)
        ======================================== */
        @media (max-width: 899px) and (min-width: 768px) {
            .scc-cookie-banner {
                padding: <?php echo esc_attr($banner_padding_v_tablet); ?>px 0;
            }
            
            /* Card positions - more compact on tablet */
            .scc-cookie-banner.scc-position-bottom-left,
            .scc-cookie-banner.scc-position-bottom-right {
                max-width: min(<?php echo esc_attr($card_max_width); ?>px, 85vw);
                width: auto;
                min-width: 280px;
            }
            
            /* Content direction control - Tablet */
            .scc-cookie-content {
                flex-direction: <?php echo esc_attr($content_direction_tablet); ?>;
            }
            
            <?php if ($content_direction_tablet == 'column') : ?>
            .scc-cookie-content {
                text-align: center;
            }
            
            .scc-cookie-buttons {
                width: 100%;
                max-width: 400px;
                margin: 0 auto;
            }
            <?php endif; ?>
            
            /* Card content direction - Tablet */
            .scc-position-bottom-left .scc-cookie-content,
            .scc-position-bottom-right .scc-cookie-content {
                flex-direction: <?php echo esc_attr($content_direction_tablet); ?>;
            }
            
            <?php if ($content_direction_tablet == 'column') : ?>
            .scc-position-bottom-left .scc-cookie-content,
            .scc-position-bottom-right .scc-cookie-content {
                text-align: center;
            }
            <?php endif; ?>
            
            /* Button layout for tablet */
            .scc-cookie-buttons {
                <?php if ($button_layout_tablet == 'vertical') : ?>
                flex-direction: column;
                width: 100%;
                <?php else : ?>
                flex-direction: row;
                <?php endif; ?>
            }
            
            <?php if ($button_layout_tablet == 'vertical') : ?>
            /* Full-width banner buttons when vertical */
            .scc-position-bottom .scc-cookie-btn,
            .scc-position-top .scc-cookie-btn {
                width: 100%;
            }
            <?php endif; ?>
            
            .scc-position-bottom-left .scc-cookie-buttons,
            .scc-position-bottom-right .scc-cookie-buttons {
                <?php if ($button_layout_tablet == 'vertical') : ?>
                flex-direction: column;
                <?php else : ?>
                flex-direction: row;
                <?php endif; ?>
            }
            
            <?php if ($content_direction_tablet == 'row') : ?>
            .scc-position-bottom-left .scc-cookie-buttons,
            .scc-position-bottom-right .scc-cookie-buttons {
                width: auto;
                flex-shrink: 0;
            }
            <?php endif; ?>
            
            .scc-position-bottom-left .scc-cookie-btn,
            .scc-position-bottom-right .scc-cookie-btn {
                <?php if ($button_layout_tablet == 'vertical') : ?>
                width: 100%;
                <?php else : ?>
                flex: 1;
                <?php endif; ?>
            }
            
            /* Tablet Typography */
            .scc-cookie-text .scc-title {
                font-size: <?php echo esc_attr($title_size_tablet); ?>px;
            }
            
            .scc-cookie-text .scc-text {
                font-size: <?php echo esc_attr($text_size_tablet); ?>px;
            }
            
            /* Tablet Button Styling */
            .scc-cookie-btn {
                padding: <?php echo esc_attr($button_padding_v_tablet); ?>px <?php echo esc_attr($button_padding_h_tablet); ?>px;
                border-radius: <?php echo esc_attr($button_radius_tablet); ?>px;
                font-size: <?php echo esc_attr($button_size_tablet); ?>px;
            }
        }

        /* ========================================
           MOBILE STYLES (Below 768px)
        ======================================== */
        @media (max-width: 767px) {
            .scc-cookie-banner {
                padding: <?php echo esc_attr($banner_padding_v_mobile); ?>px 0;
            }
            
            
            /* Content direction control - Mobile */
            .scc-cookie-content {
                flex-direction: <?php echo esc_attr($content_direction_mobile); ?>;
            }
            
            <?php if ($content_direction_mobile == 'column') : ?>
            .scc-cookie-content {
                text-align: center;
            }
            
            .scc-cookie-buttons {
                width: 100%;
                max-width: 400px;
                margin: 0 auto;
            }
            <?php endif; ?>
            
            /* Card content direction - Mobile */
            .scc-position-bottom-left .scc-cookie-content,
            .scc-position-bottom-right .scc-cookie-content {
                flex-direction: <?php echo esc_attr($content_direction_mobile); ?>;
            }
            
            <?php if ($content_direction_mobile == 'column') : ?>
            .scc-position-bottom-left .scc-cookie-content,
            .scc-position-bottom-right .scc-cookie-content {
                text-align: center;
            }
            <?php endif; ?>
            
            /* Button layout for mobile */
            .scc-cookie-buttons {
                <?php if ($button_layout_mobile == 'vertical') : ?>
                flex-direction: column;
                width: 100%;
                <?php else : ?>
                flex-direction: row;
                <?php endif; ?>
            }
            
            <?php if ($button_layout_mobile == 'vertical') : ?>
            /* Full-width banner buttons when vertical */
            .scc-position-bottom .scc-cookie-btn,
            .scc-position-top .scc-cookie-btn {
                width: 100%;
            }
            <?php endif; ?>
            
            .scc-position-bottom-left .scc-cookie-buttons,
            .scc-position-bottom-right .scc-cookie-buttons {
                <?php if ($button_layout_mobile == 'vertical') : ?>
                flex-direction: column !important;
                gap: 10px !important;
                width: 100% !important;
                <?php else : ?>
                flex-direction: row !important;
                gap: 8px !important;
                <?php endif; ?>
            }
            
            <?php if ($content_direction_mobile == 'row') : ?>
            .scc-position-bottom-left .scc-cookie-buttons,
            .scc-position-bottom-right .scc-cookie-buttons {
                width: auto;
                flex-shrink: 0;
            }
            <?php endif; ?>
            
            .scc-position-bottom-left .scc-cookie-btn,
            .scc-position-bottom-right .scc-cookie-btn {
                <?php if ($button_layout_mobile == 'vertical') : ?>
                width: 100% !important;
                <?php else : ?>
                flex: 1 !important;
                <?php endif; ?>
                min-height: 44px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                padding: <?php echo esc_attr($button_padding_v_mobile); ?>px <?php echo esc_attr($button_padding_h_mobile); ?>px !important;
                border-radius: <?php echo esc_attr($button_radius_mobile); ?>px !important;
                font-size: <?php echo esc_attr($button_size_mobile); ?>px !important;
                box-sizing: border-box !important;
            }
            
            /* Mobile Typography */
            .scc-cookie-text .scc-title {
                font-size: <?php echo esc_attr($title_size_mobile); ?>px;
            }
            
            .scc-cookie-text .scc-text {
                font-size: <?php echo esc_attr($text_size_mobile); ?>px;
            }
            
            /* Mobile Button Styling */
            .scc-cookie-btn {
                padding: <?php echo esc_attr($button_padding_v_mobile); ?>px <?php echo esc_attr($button_padding_h_mobile); ?>px;
                border-radius: <?php echo esc_attr($button_radius_mobile); ?>px;
                font-size: <?php echo esc_attr($button_size_mobile); ?>px;
            }
            
            /* Card positions go full-width on mobile with proper padding */
            .scc-cookie-banner.scc-position-bottom-left,
            .scc-cookie-banner.scc-position-bottom-right {
                left: 10px !important;
                right: 10px !important;
                max-width: calc(100% - 20px) !important;
                width: auto !important;
                min-width: auto !important;
                bottom: 10px !important;
            }
            
            /* Add more padding on mobile for better spacing */
            .scc-position-bottom-left .scc-cookie-content,
            .scc-position-bottom-right .scc-cookie-content {
                padding: 20px 20px !important;
            }
            
            /* Ensure mobile buttons are consistent */
            .scc-position-bottom-left .scc-cookie-btn,
            .scc-position-bottom-right .scc-cookie-btn {
                box-sizing: border-box !important;
                text-align: center !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            
            /* Force mobile vertical layout */
            @media (max-width: 767px) {
                .scc-cookie-banner.scc-position-bottom-left .scc-cookie-buttons,
                .scc-cookie-banner.scc-position-bottom-right .scc-cookie-buttons {
                    flex-direction: column !important;
                    gap: 10px !important;
                    width: 100% !important;
                }
                
                .scc-cookie-banner.scc-position-bottom-left .scc-cookie-btn,
                .scc-cookie-banner.scc-position-bottom-right .scc-cookie-btn {
                    width: 100% !important;
                    flex: none !important;
                }
            }
            
            /* Mobile buttons should always be full width when in vertical layout */
            .scc-position-bottom-left .scc-cookie-buttons,
            .scc-position-bottom-right .scc-cookie-buttons {
                width: 100% !important;
            }
        }

        /* Hide banner animations */
        .scc-cookie-banner.scc-hide.scc-position-bottom {
            animation: sccHideBottom 0.5s ease-out forwards;
        }
        
        .scc-cookie-banner.scc-hide.scc-position-top {
            animation: sccHideTop 0.5s ease-out forwards;
        }
        
        .scc-cookie-banner.scc-hide.scc-position-bottom-left {
            animation: sccHideLeft 0.5s ease-out forwards;
        }
        
        .scc-cookie-banner.scc-hide.scc-position-bottom-right {
            animation: sccHideRight 0.5s ease-out forwards;
        }

        @keyframes sccHideBottom {
            from { transform: translateY(0); opacity: 1; }
            to { transform: translateY(100%); opacity: 0; }
        }
        
        @keyframes sccHideTop {
            from { transform: translateY(0); opacity: 1; }
            to { transform: translateY(-100%); opacity: 0; }
        }
        
        @keyframes sccHideLeft {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(-100%); opacity: 0; }
        }
        
        @keyframes sccHideRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    </style>

    <script>
        (function() {
            var acceptBtn = document.getElementById('scc-cookie-accept');
            var declineBtn = document.getElementById('scc-cookie-decline');
            
            if (acceptBtn) {
                acceptBtn.addEventListener('click', function() {
                    var duration = parseInt(this.getAttribute('data-duration')) || 365;
                    sccSetCookie('brayne_cookie_consent', 'accepted', duration);
                    sccHideBanner();
                });
            }

            if (declineBtn) {
                declineBtn.addEventListener('click', function() {
                    var duration = parseInt(this.getAttribute('data-duration')) || 365;
                    sccSetCookie('brayne_cookie_consent', 'declined', duration);
                    sccHideBanner();
                });
            }

            function sccSetCookie(name, value, days) {
                var expires = "";
                if (days) {
                    var date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = "; expires=" + date.toUTCString();
                }
                
                // Get domain (remove port and www if present)
                var domain = window.location.hostname;
                var cookieString = name + "=" + (value || "") + expires + "; path=/";
                
                // Add SameSite for cross-site cookie compatibility
                cookieString += "; SameSite=Lax";
                
                // Add Secure flag if using HTTPS
                if (window.location.protocol === 'https:') {
                    cookieString += "; Secure";
                }
                
                // Set the cookie
                document.cookie = cookieString;
                
                // Verify cookie was set (for debugging)
                console.log('Cookie set: ' + name + '=' + value);
                console.log('Cookie string: ' + cookieString);
                
                // Double-check if cookie exists
                setTimeout(function() {
                    var cookieExists = document.cookie.indexOf(name + '=') !== -1;
                    console.log('Cookie verification: ' + (cookieExists ? 'SUCCESS' : 'FAILED'));
                    if (!cookieExists) {
                        console.error('Cookie was not set! Check browser settings or privacy mode.');
                    }
                }, 100);
            }

            function sccHideBanner() {
                var banner = document.getElementById('scc-cookie-banner');
                if (banner) {
                    banner.classList.add('scc-hide');
                    setTimeout(function() {
                        banner.style.display = 'none';
                    }, 500);
                }
            }
        })();
    </script>
    <?php
}
add_action('wp_footer', 'scc_display_cookie_banner');

/**
 * Add admin menu
 */
function scc_add_admin_menu() {
    add_options_page(
        __('Brayne Cookie Consent', 'brayne-cookie-consent'),
        __('Cookie Consent', 'brayne-cookie-consent'),
        'manage_options',
        'brayne-cookie-consent',
        'scc_settings_page'
    );
}
add_action('admin_menu', 'scc_add_admin_menu');

/**
 * Register settings
 */
function scc_register_settings() {
    register_setting('scc_options_group', 'scc_options', 'scc_sanitize_options');
}
add_action('admin_init', 'scc_register_settings');

/**
 * Sanitize options
 */
function scc_sanitize_options($input) {
    $sanitized = array();
    
    // Content settings
    $text_fields = array('banner_title', 'accept_text', 'decline_text');
    foreach ($text_fields as $field) {
        if (isset($input[$field])) {
            $sanitized[$field] = sanitize_text_field($input[$field]);
        }
    }
    
    if (isset($input['banner_text'])) {
        $sanitized['banner_text'] = sanitize_textarea_field($input['banner_text']);
    }
    
    if (isset($input['show_decline'])) {
        $sanitized['show_decline'] = (bool) $input['show_decline'];
    }
    
    if (isset($input['cookie_duration'])) {
        $sanitized['cookie_duration'] = absint($input['cookie_duration']);
    }
    
    // Banner styling
    if (isset($input['banner_position'])) {
        $allowed_positions = array('top', 'bottom', 'bottom-left', 'bottom-right');
        $sanitized['banner_position'] = in_array($input['banner_position'], $allowed_positions) ? $input['banner_position'] : 'bottom';
    }
    
    
    if (isset($input['card_max_width'])) {
        $sanitized['card_max_width'] = absint($input['card_max_width']);
    }
    
    if (isset($input['card_border_radius'])) {
        $sanitized['card_border_radius'] = absint($input['card_border_radius']);
    }
    
    if (isset($input['card_padding_v'])) {
        $sanitized['card_padding_v'] = absint($input['card_padding_v']);
    }
    
    if (isset($input['card_padding_h'])) {
        $sanitized['card_padding_h'] = absint($input['card_padding_h']);
    }
    
    if (isset($input['card_button_gap'])) {
        $sanitized['card_button_gap'] = absint($input['card_button_gap']);
    }
    
    if (isset($input['card_text_align'])) {
        $allowed_align = array('left', 'center', 'right');
        $sanitized['card_text_align'] = in_array($input['card_text_align'], $allowed_align) ? $input['card_text_align'] : 'center';
    }
    
    // Color fields
    $color_fields = array(
        'banner_bg_color', 'border_color', 'title_color', 'text_color', 
        'link_color', 'link_hover_color', 'accept_bg_color', 'accept_text_color',
        'accept_hover_bg', 'accept_hover_text', 'decline_bg_color', 'decline_text_color',
        'decline_hover_bg', 'decline_hover_text'
    );
    
    foreach ($color_fields as $field) {
        if (isset($input[$field])) {
            $sanitized[$field] = sanitize_hex_color($input[$field]);
        }
    }
    
    // Number fields (base + responsive)
    $number_fields = array(
        'border_width', 'title_size', 'text_size', 'button_radius',
        'button_size', 'button_padding_v', 'button_padding_h', 'banner_padding_v'
    );
    
    $devices = array('', '_desktop', '_tablet', '_mobile');
    
    foreach ($number_fields as $field) {
        foreach ($devices as $device) {
            $field_name = $field . $device;
            if (isset($input[$field_name])) {
                $sanitized[$field_name] = absint($input[$field_name]);
            }
        }
    }
    
    // Font fields
    if (isset($input['font_family'])) {
        $sanitized['font_family'] = sanitize_text_field($input['font_family']);
    }
    
    if (isset($input['button_font_weight'])) {
        $sanitized['button_font_weight'] = sanitize_text_field($input['button_font_weight']);
    }
    
    // Layout fields
    $content_direction_fields = array('content_direction_desktop', 'content_direction_tablet', 'content_direction_mobile');
    foreach ($content_direction_fields as $field) {
        if (isset($input[$field])) {
            $sanitized[$field] = in_array($input[$field], array('row', 'column')) ? $input[$field] : 'row';
        }
    }
    
    // Legacy support - remove these after migration
    if (isset($input['banner_layout_tablet'])) {
        $sanitized['banner_layout_tablet'] = in_array($input['banner_layout_tablet'], array('row', 'column')) ? $input['banner_layout_tablet'] : 'row';
    }
    if (isset($input['banner_layout_mobile'])) {
        $sanitized['banner_layout_mobile'] = in_array($input['banner_layout_mobile'], array('row', 'column')) ? $input['banner_layout_mobile'] : 'column';
    }
    
    if (isset($input['box_shadow'])) {
        $sanitized['box_shadow'] = sanitize_text_field($input['box_shadow']);
    }
    
    // Button layout fields
    $button_layout_fields = array('button_layout_desktop', 'button_layout_tablet', 'button_layout_mobile');
    foreach ($button_layout_fields as $field) {
        if (isset($input[$field])) {
            $sanitized[$field] = in_array($input[$field], array('horizontal', 'vertical')) ? $input[$field] : 'horizontal';
        }
    }
    
    // Display conditions
    if (isset($input['display_mode'])) {
        $allowed_modes = array('all_pages', 'homepage_only', 'all_except_homepage', 'specific_pages', 'exclude_pages');
        $sanitized['display_mode'] = in_array($input['display_mode'], $allowed_modes) ? $input['display_mode'] : 'all_pages';
    }
    
    if (isset($input['selected_pages']) && is_array($input['selected_pages'])) {
        $sanitized['selected_pages'] = array_map('absint', $input['selected_pages']);
    }
    
    if (isset($input['excluded_pages']) && is_array($input['excluded_pages'])) {
        $sanitized['excluded_pages'] = array_map('absint', $input['excluded_pages']);
    }
    
    return $sanitized;
}

/**
 * Settings page HTML
 */
function scc_settings_page() {
    $options = get_option('scc_options', array());
    
    // Set all defaults
    $defaults = array(
        'banner_title' => '🍪 We use cookies',
        'banner_text' => 'We use cookies to improve your experience on our site. By continuing to browse, you agree to our use of cookies.',
        'accept_text' => 'Accept All Cookies',
        'decline_text' => 'Decline',
        'show_decline' => true,
        'cookie_duration' => 365,
        'banner_position' => 'bottom',
        'banner_bg_color' => '#ffffff',
        'border_color' => '#E1195B',
        'border_width' => '3',
        'card_max_width' => '400',
        'card_border_radius' => '12',
        'card_padding_v' => '20',
        'card_padding_h' => '20',
        'card_button_gap' => '10',
        'card_text_align' => 'center',
        'box_shadow' => 'yes',
        'font_family' => 'inherit',
        'title_color' => '#222222',
        'title_size' => '16',
        'title_size_desktop' => '16',
        'title_size_tablet' => '15',
        'title_size_mobile' => '14',
        'text_color' => '#333333',
        'text_size' => '14',
        'text_size_desktop' => '14',
        'text_size_tablet' => '13',
        'text_size_mobile' => '12',
        'link_color' => '#E1195B',
        'link_hover_color' => '#48144A',
        'accept_bg_color' => '#E1195B',
        'accept_text_color' => '#ffffff',
        'accept_hover_bg' => '#48144A',
        'accept_hover_text' => '#ffffff',
        'decline_bg_color' => '#f5f5f5',
        'decline_text_color' => '#666666',
        'decline_hover_bg' => '#e0e0e0',
        'decline_hover_text' => '#333333',
        'button_radius' => '5',
        'button_radius_desktop' => '5',
        'button_radius_tablet' => '5',
        'button_radius_mobile' => '5',
        'button_size' => '14',
        'button_size_desktop' => '14',
        'button_size_tablet' => '13',
        'button_size_mobile' => '13',
        'button_padding_v' => '12',
        'button_padding_v_desktop' => '12',
        'button_padding_v_tablet' => '10',
        'button_padding_v_mobile' => '12',
        'button_padding_h' => '24',
        'button_padding_h_desktop' => '24',
        'button_padding_h_tablet' => '20',
        'button_padding_h_mobile' => '20',
        'button_font_weight' => '600',
        'banner_padding_v' => '20',
        'banner_padding_v_desktop' => '20',
        'banner_padding_v_tablet' => '18',
        'banner_padding_v_mobile' => '15',
        'content_direction_desktop' => 'row',
        'content_direction_tablet' => 'row',
        'content_direction_mobile' => 'column',
        // Legacy support - remove these after migration
        'banner_layout_tablet' => 'row',
        'banner_layout_mobile' => 'column',
        'button_layout_desktop' => 'horizontal',
        'button_layout_tablet' => 'horizontal',
        'button_layout_mobile' => 'vertical',
        'display_mode' => 'all_pages',
        'selected_pages' => array(),
        'excluded_pages' => array(),
    );
    
    $options = wp_parse_args($options, $defaults);
    ?>
    <div class="wrap scc-admin-wrap">
        <h1><?php _e('Brayne Cookie Consent - Settings', 'brayne-cookie-consent'); ?></h1>
        <p class="description"><?php _e('✨ Full responsive control for Desktop, Tablet, and Mobile!', 'brayne-cookie-consent'); ?></p>
        
        <form method="post" action="options.php">
            <?php settings_fields('scc_options_group'); ?>
            
            <h2 class="nav-tab-wrapper">
                <a href="#tab-content" class="nav-tab nav-tab-active"><?php _e('📝 Content', 'brayne-cookie-consent'); ?></a>
                <a href="#tab-banner" class="nav-tab"><?php _e('🎨 Banner Style', 'brayne-cookie-consent'); ?></a>
                <a href="#tab-typography" class="nav-tab"><?php _e('✍️ Typography', 'brayne-cookie-consent'); ?></a>
                <a href="#tab-buttons" class="nav-tab"><?php _e('🔘 Buttons', 'brayne-cookie-consent'); ?></a>
                <a href="#tab-responsive" class="nav-tab"><?php _e('📱 Responsive', 'brayne-cookie-consent'); ?></a>
                <a href="#tab-display" class="nav-tab"><?php _e('🎯 Display Rules', 'brayne-cookie-consent'); ?></a>
            </h2>
            
            <!-- Content Tab -->
            <div id="tab-content" class="scc-tab-content">
                <h2><?php _e('Content Settings', 'brayne-cookie-consent'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="banner_title"><?php _e('Banner Title', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="banner_title" name="scc_options[banner_title]" 
                                   value="<?php echo esc_attr($options['banner_title']); ?>" class="regular-text">
                            <p class="description"><?php _e('The main heading (you can use emoji 🍪)', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="banner_text"><?php _e('Banner Text', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <textarea id="banner_text" name="scc_options[banner_text]" rows="3" 
                                      class="large-text"><?php echo esc_textarea($options['banner_text']); ?></textarea>
                            <p class="description"><?php _e('The message displayed to visitors', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="accept_text"><?php _e('Accept Button Text', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="accept_text" name="scc_options[accept_text]" 
                                   value="<?php echo esc_attr($options['accept_text']); ?>" class="regular-text">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="decline_text"><?php _e('Decline Button Text', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="decline_text" name="scc_options[decline_text]" 
                                   value="<?php echo esc_attr($options['decline_text']); ?>" class="regular-text">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="show_decline"><?php _e('Show Decline Button', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="show_decline" name="scc_options[show_decline]" 
                                       value="1" <?php checked($options['show_decline'], true); ?>>
                                <?php _e('Display the decline button', 'brayne-cookie-consent'); ?>
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="cookie_duration"><?php _e('Cookie Duration (Days)', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="cookie_duration" name="scc_options[cookie_duration]" 
                                   value="<?php echo esc_attr($options['cookie_duration']); ?>" min="1" max="3650" class="small-text">
                            <p class="description"><?php _e('How long to remember the user\'s choice (default: 365 days)', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Banner Style Tab -->
            <div id="tab-banner" class="scc-tab-content" style="display:none;">
                <h2><?php _e('Banner Style Settings', 'brayne-cookie-consent'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="banner_position"><?php _e('Banner Position', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <select id="banner_position" name="scc_options[banner_position]">
                                <optgroup label="<?php _e('Full Width Bar', 'brayne-cookie-consent'); ?>">
                                    <option value="bottom" <?php selected($options['banner_position'], 'bottom'); ?>><?php _e('Bottom (Full Width)', 'brayne-cookie-consent'); ?></option>
                                    <option value="top" <?php selected($options['banner_position'], 'top'); ?>><?php _e('Top (Full Width)', 'brayne-cookie-consent'); ?></option>
                                </optgroup>
                                <optgroup label="<?php _e('Card Style - Corner Positions', 'brayne-cookie-consent'); ?>">
                                    <option value="bottom-left" <?php selected($options['banner_position'], 'bottom-left'); ?>><?php _e('Bottom Left Corner (Card)', 'brayne-cookie-consent'); ?></option>
                                    <option value="bottom-right" <?php selected($options['banner_position'], 'bottom-right'); ?>><?php _e('Bottom Right Corner (Card)', 'brayne-cookie-consent'); ?></option>
                                </optgroup>
                            </select>
                            <p class="description"><?php _e('Choose between full-width bar or corner card style', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                    
                    
                    <tr>
                        <th scope="row">
                            <label for="card_max_width"><?php _e('Card Max Width (px)', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="card_max_width" name="scc_options[card_max_width]" 
                                   value="<?php echo esc_attr($options['card_max_width']); ?>" min="250" max="1500" class="small-text">
                            <p class="description"><?php _e('Maximum width for card-style positions (default: 400px)', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <h3><?php _e('Card Design Settings', 'brayne-cookie-consent'); ?></h3>
                <p class="description"><?php _e('These settings only apply to card-style positions (Bottom Left/Right Corner)', 'brayne-cookie-consent'); ?></p>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="card_border_radius"><?php _e('Card Corner Radius (px)', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="card_border_radius" name="scc_options[card_border_radius]" 
                                   value="<?php echo esc_attr($options['card_border_radius']); ?>" min="0" max="50" class="small-text">
                            <p class="description"><?php _e('Roundness of card corners (0 = square, 25 = very round, default: 12px)', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="card_padding_v"><?php _e('Card Vertical Padding (px)', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="card_padding_v" name="scc_options[card_padding_v]" 
                                   value="<?php echo esc_attr($options['card_padding_v']); ?>" min="5" max="50" class="small-text">
                            <p class="description"><?php _e('Top and bottom padding inside the card (default: 20px)', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="card_padding_h"><?php _e('Card Horizontal Padding (px)', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="card_padding_h" name="scc_options[card_padding_h]" 
                                   value="<?php echo esc_attr($options['card_padding_h']); ?>" min="5" max="50" class="small-text">
                            <p class="description"><?php _e('Left and right padding inside the card (default: 20px)', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="card_button_gap"><?php _e('Button Gap (px)', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="card_button_gap" name="scc_options[card_button_gap]" 
                                   value="<?php echo esc_attr($options['card_button_gap']); ?>" min="0" max="30" class="small-text">
                            <p class="description"><?php _e('Space between Accept and Decline buttons (default: 10px)', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="card_text_align"><?php _e('Text Alignment', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <select id="card_text_align" name="scc_options[card_text_align]">
                                <option value="left" <?php selected($options['card_text_align'], 'left'); ?>><?php _e('Left', 'brayne-cookie-consent'); ?></option>
                                <option value="center" <?php selected($options['card_text_align'], 'center'); ?>><?php _e('Center', 'brayne-cookie-consent'); ?></option>
                                <option value="right" <?php selected($options['card_text_align'], 'right'); ?>><?php _e('Right', 'brayne-cookie-consent'); ?></option>
                            </select>
                            <p class="description"><?php _e('How text is aligned inside the card (default: center)', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <h3><?php _e('General Banner Settings', 'brayne-cookie-consent'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="banner_bg_color"><?php _e('Background Color', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="banner_bg_color" name="scc_options[banner_bg_color]" 
                                   value="<?php echo esc_attr($options['banner_bg_color']); ?>" class="scc-color-picker">
                            <p class="description"><?php _e('Banner background color', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="border_color"><?php _e('Border Color', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="border_color" name="scc_options[border_color]" 
                                   value="<?php echo esc_attr($options['border_color']); ?>" class="scc-color-picker">
                            <p class="description"><?php _e('Top/Bottom border color', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="border_width"><?php _e('Border Width (px)', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="border_width" name="scc_options[border_width]" 
                                   value="<?php echo esc_attr($options['border_width']); ?>" min="0" max="20" class="small-text">
                            <p class="description"><?php _e('Border thickness in pixels (0 to hide)', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="box_shadow"><?php _e('Drop Shadow', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <select id="box_shadow" name="scc_options[box_shadow]">
                                <option value="yes" <?php selected($options['box_shadow'], 'yes'); ?>><?php _e('Yes', 'brayne-cookie-consent'); ?></option>
                                <option value="no" <?php selected($options['box_shadow'], 'no'); ?>><?php _e('No', 'brayne-cookie-consent'); ?></option>
                            </select>
                            <p class="description"><?php _e('Add a subtle shadow effect', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Typography Tab -->
            <div id="tab-typography" class="scc-tab-content" style="display:none;">
                <h2><?php _e('Typography Settings', 'brayne-cookie-consent'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="font_family"><?php _e('Font Family', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <select id="font_family" name="scc_options[font_family]" class="regular-text">
                                <option value="inherit" <?php selected($options['font_family'], 'inherit'); ?>><?php _e('Inherit from Theme', 'brayne-cookie-consent'); ?></option>
                                <option value="Arial, sans-serif" <?php selected($options['font_family'], 'Arial, sans-serif'); ?>>Arial</option>
                                <option value="'Helvetica Neue', Helvetica, sans-serif" <?php selected($options['font_family'], "'Helvetica Neue', Helvetica, sans-serif"); ?>>Helvetica</option>
                                <option value="Georgia, serif" <?php selected($options['font_family'], 'Georgia, serif'); ?>>Georgia</option>
                                <option value="'Times New Roman', Times, serif" <?php selected($options['font_family'], "'Times New Roman', Times, serif"); ?>>Times New Roman</option>
                                <option value="'Courier New', Courier, monospace" <?php selected($options['font_family'], "'Courier New', Courier, monospace"); ?>>Courier New</option>
                                <option value="Verdana, sans-serif" <?php selected($options['font_family'], 'Verdana, sans-serif'); ?>>Verdana</option>
                                <option value="'Trebuchet MS', sans-serif" <?php selected($options['font_family'], "'Trebuchet MS', sans-serif"); ?>>Trebuchet MS</option>
                                <option value="'Arial Black', sans-serif" <?php selected($options['font_family'], "'Arial Black', sans-serif"); ?>>Arial Black</option>
                                <option value="Impact, sans-serif" <?php selected($options['font_family'], 'Impact, sans-serif'); ?>>Impact</option>
                                <option value="'Comic Sans MS', cursive" <?php selected($options['font_family'], "'Comic Sans MS', cursive"); ?>>Comic Sans MS</option>
                                <option value="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" <?php selected($options['font_family'], "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"); ?>>System Font</option>
                            </select>
                            <p class="description"><?php _e('Font for the entire banner', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="title_color"><?php _e('Title Color', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="title_color" name="scc_options[title_color]" 
                                   value="<?php echo esc_attr($options['title_color']); ?>" class="scc-color-picker">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="text_color"><?php _e('Text Color', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="text_color" name="scc_options[text_color]" 
                                   value="<?php echo esc_attr($options['text_color']); ?>" class="scc-color-picker">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="link_color"><?php _e('Link Color', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="link_color" name="scc_options[link_color]" 
                                   value="<?php echo esc_attr($options['link_color']); ?>" class="scc-color-picker">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="link_hover_color"><?php _e('Link Hover Color', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="link_hover_color" name="scc_options[link_hover_color]" 
                                   value="<?php echo esc_attr($options['link_hover_color']); ?>" class="scc-color-picker">
                        </td>
                    </tr>
                </table>
                
                <p class="description" style="margin-top: 20px;">
                    💡 <strong>Tip:</strong> For responsive font sizes, use the <strong>Responsive</strong> tab to set different sizes for Desktop, Tablet, and Mobile!
                </p>
            </div>
            
            <!-- Buttons Tab -->
            <div id="tab-buttons" class="scc-tab-content" style="display:none;">
                <h2><?php _e('Button Settings', 'brayne-cookie-consent'); ?></h2>
                
                <h3><?php _e('Button Layout', 'brayne-cookie-consent'); ?></h3>
                <p class="description"><?php _e('Choose how buttons are arranged (side by side or stacked)', 'brayne-cookie-consent'); ?></p>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="button_layout_desktop"><?php _e('Desktop Layout', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <select id="button_layout_desktop" name="scc_options[button_layout_desktop]">
                                <option value="horizontal" <?php selected($options['button_layout_desktop'], 'horizontal'); ?>><?php _e('Horizontal (Side by Side)', 'brayne-cookie-consent'); ?></option>
                                <option value="vertical" <?php selected($options['button_layout_desktop'], 'vertical'); ?>><?php _e('Vertical (Stacked)', 'brayne-cookie-consent'); ?></option>
                            </select>
                            <p class="description"><?php _e('How buttons are arranged on desktop screens', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="button_layout_tablet"><?php _e('Tablet Layout', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <select id="button_layout_tablet" name="scc_options[button_layout_tablet]">
                                <option value="horizontal" <?php selected($options['button_layout_tablet'], 'horizontal'); ?>><?php _e('Horizontal (Side by Side)', 'brayne-cookie-consent'); ?></option>
                                <option value="vertical" <?php selected($options['button_layout_tablet'], 'vertical'); ?>><?php _e('Vertical (Stacked)', 'brayne-cookie-consent'); ?></option>
                            </select>
                            <p class="description"><?php _e('How buttons are arranged on tablet screens', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="button_layout_mobile"><?php _e('Mobile Layout', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <select id="button_layout_mobile" name="scc_options[button_layout_mobile]">
                                <option value="horizontal" <?php selected($options['button_layout_mobile'], 'horizontal'); ?>><?php _e('Horizontal (Side by Side)', 'brayne-cookie-consent'); ?></option>
                                <option value="vertical" <?php selected($options['button_layout_mobile'], 'vertical'); ?>><?php _e('Vertical (Stacked)', 'brayne-cookie-consent'); ?></option>
                            </select>
                            <p class="description"><?php _e('How buttons are arranged on mobile screens (vertical recommended)', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <h3><?php _e('General Button Style', 'brayne-cookie-consent'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="button_font_weight"><?php _e('Font Weight', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <select id="button_font_weight" name="scc_options[button_font_weight]">
                                <option value="300" <?php selected($options['button_font_weight'], '300'); ?>><?php _e('Light (300)', 'brayne-cookie-consent'); ?></option>
                                <option value="400" <?php selected($options['button_font_weight'], '400'); ?>><?php _e('Normal (400)', 'brayne-cookie-consent'); ?></option>
                                <option value="500" <?php selected($options['button_font_weight'], '500'); ?>><?php _e('Medium (500)', 'brayne-cookie-consent'); ?></option>
                                <option value="600" <?php selected($options['button_font_weight'], '600'); ?>><?php _e('Semi-Bold (600)', 'brayne-cookie-consent'); ?></option>
                                <option value="700" <?php selected($options['button_font_weight'], '700'); ?>><?php _e('Bold (700)', 'brayne-cookie-consent'); ?></option>
                                <option value="800" <?php selected($options['button_font_weight'], '800'); ?>><?php _e('Extra Bold (800)', 'brayne-cookie-consent'); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
                
                <p class="description" style="margin: 20px 0;">
                    💡 <strong>Tip:</strong> For responsive button sizes, radius, and padding, use the <strong>Responsive</strong> tab!
                </p>
                
                <h3><?php _e('Accept Button Colors', 'brayne-cookie-consent'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="accept_bg_color"><?php _e('Background Color', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="accept_bg_color" name="scc_options[accept_bg_color]" 
                                   value="<?php echo esc_attr($options['accept_bg_color']); ?>" class="scc-color-picker">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="accept_text_color"><?php _e('Text Color', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="accept_text_color" name="scc_options[accept_text_color]" 
                                   value="<?php echo esc_attr($options['accept_text_color']); ?>" class="scc-color-picker">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="accept_hover_bg"><?php _e('Hover Background Color', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="accept_hover_bg" name="scc_options[accept_hover_bg]" 
                                   value="<?php echo esc_attr($options['accept_hover_bg']); ?>" class="scc-color-picker">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="accept_hover_text"><?php _e('Hover Text Color', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="accept_hover_text" name="scc_options[accept_hover_text]" 
                                   value="<?php echo esc_attr($options['accept_hover_text']); ?>" class="scc-color-picker">
                        </td>
                    </tr>
                </table>
                
                <h3><?php _e('Decline Button Colors', 'brayne-cookie-consent'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="decline_bg_color"><?php _e('Background Color', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="decline_bg_color" name="scc_options[decline_bg_color]" 
                                   value="<?php echo esc_attr($options['decline_bg_color']); ?>" class="scc-color-picker">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="decline_text_color"><?php _e('Text Color', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="decline_text_color" name="scc_options[decline_text_color]" 
                                   value="<?php echo esc_attr($options['decline_text_color']); ?>" class="scc-color-picker">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="decline_hover_bg"><?php _e('Hover Background Color', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="decline_hover_bg" name="scc_options[decline_hover_bg]" 
                                   value="<?php echo esc_attr($options['decline_hover_bg']); ?>" class="scc-color-picker">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="decline_hover_text"><?php _e('Hover Text Color', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="decline_hover_text" name="scc_options[decline_hover_text]" 
                                   value="<?php echo esc_attr($options['decline_hover_text']); ?>" class="scc-color-picker">
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Responsive Tab -->
            <div id="tab-responsive" class="scc-tab-content" style="display:none;">
                <h2><?php _e('📱 Responsive Settings', 'brayne-cookie-consent'); ?></h2>
                <p class="description" style="margin-bottom: 20px;">
                    <?php _e('Customize how the banner looks on different screen sizes. Leave empty to use the base values.', 'brayne-cookie-consent'); ?>
                </p>
                
                <h3><?php _e('Banner Layout Control', 'brayne-cookie-consent'); ?></h3>
                <p class="description"><?php _e('Control how text and buttons are arranged on different screen sizes', 'brayne-cookie-consent'); ?></p>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="content_direction_desktop"><?php _e('Desktop Layout', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <select id="content_direction_desktop" name="scc_options[content_direction_desktop]">
                                <option value="row" <?php selected($options['content_direction_desktop'], 'row'); ?>><?php _e('Row (Text & Buttons Side by Side)', 'brayne-cookie-consent'); ?></option>
                                <option value="column" <?php selected($options['content_direction_desktop'], 'column'); ?>><?php _e('Column (Text Above Buttons)', 'brayne-cookie-consent'); ?></option>
                            </select>
                            <p class="description"><?php _e('How text and buttons are arranged on desktop screens', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="content_direction_tablet"><?php _e('Tablet Layout', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <select id="content_direction_tablet" name="scc_options[content_direction_tablet]">
                                <option value="row" <?php selected($options['content_direction_tablet'], 'row'); ?>><?php _e('Row (Text & Buttons Side by Side)', 'brayne-cookie-consent'); ?></option>
                                <option value="column" <?php selected($options['content_direction_tablet'], 'column'); ?>><?php _e('Column (Text Above Buttons)', 'brayne-cookie-consent'); ?></option>
                            </select>
                            <p class="description"><?php _e('How text and buttons are arranged on tablet screens', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="content_direction_mobile"><?php _e('Mobile Layout', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <select id="content_direction_mobile" name="scc_options[content_direction_mobile]">
                                <option value="row" <?php selected($options['content_direction_mobile'], 'row'); ?>><?php _e('Row (Text & Buttons Side by Side)', 'brayne-cookie-consent'); ?></option>
                                <option value="column" <?php selected($options['content_direction_mobile'], 'column'); ?>><?php _e('Column (Text Above Buttons)', 'brayne-cookie-consent'); ?></option>
                            </select>
                            <p class="description"><?php _e('How text and buttons are arranged on mobile screens (column recommended)', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <hr style="margin: 30px 0;">
                
                
                <div class="scc-responsive-container">
                    <!-- Desktop Settings -->
                    <div class="scc-device-section">
                        <h3>🖥️ <?php _e('Desktop (Above 1024px)', 'brayne-cookie-consent'); ?></h3>
                        <table class="form-table">
                            <tr>
                                <th><?php _e('Title Font Size (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[title_size_desktop]" 
                                           value="<?php echo esc_attr($options['title_size_desktop']); ?>" 
                                           min="10" max="40" class="small-text" placeholder="16">
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Text Font Size (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[text_size_desktop]" 
                                           value="<?php echo esc_attr($options['text_size_desktop']); ?>" 
                                           min="10" max="30" class="small-text" placeholder="14">
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Button Font Size (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[button_size_desktop]" 
                                           value="<?php echo esc_attr($options['button_size_desktop']); ?>" 
                                           min="10" max="30" class="small-text" placeholder="14">
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Button Border Radius (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[button_radius_desktop]" 
                                           value="<?php echo esc_attr($options['button_radius_desktop']); ?>" 
                                           min="0" max="50" class="small-text" placeholder="5">
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Button Vertical Padding (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[button_padding_v_desktop]" 
                                           value="<?php echo esc_attr($options['button_padding_v_desktop']); ?>" 
                                           min="0" max="50" class="small-text" placeholder="12">
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Button Horizontal Padding (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[button_padding_h_desktop]" 
                                           value="<?php echo esc_attr($options['button_padding_h_desktop']); ?>" 
                                           min="0" max="100" class="small-text" placeholder="24">
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Banner Vertical Padding (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[banner_padding_v_desktop]" 
                                           value="<?php echo esc_attr($options['banner_padding_v_desktop']); ?>" 
                                           min="0" max="100" class="small-text" placeholder="20">
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Tablet Settings -->
                    <div class="scc-device-section">
                        <h3>📱 <?php _e('Tablet (768px - 1024px)', 'brayne-cookie-consent'); ?></h3>
                        <table class="form-table">
                            <tr>
                                <th><?php _e('Title Font Size (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[title_size_tablet]" 
                                           value="<?php echo esc_attr($options['title_size_tablet']); ?>" 
                                           min="10" max="40" class="small-text" placeholder="15">
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Text Font Size (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[text_size_tablet]" 
                                           value="<?php echo esc_attr($options['text_size_tablet']); ?>" 
                                           min="10" max="30" class="small-text" placeholder="13">
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Button Font Size (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[button_size_tablet]" 
                                           value="<?php echo esc_attr($options['button_size_tablet']); ?>" 
                                           min="10" max="30" class="small-text" placeholder="13">
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Button Border Radius (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[button_radius_tablet]" 
                                           value="<?php echo esc_attr($options['button_radius_tablet']); ?>" 
                                           min="0" max="50" class="small-text" placeholder="5">
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Button Vertical Padding (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[button_padding_v_tablet]" 
                                           value="<?php echo esc_attr($options['button_padding_v_tablet']); ?>" 
                                           min="0" max="50" class="small-text" placeholder="10">
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Button Horizontal Padding (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[button_padding_h_tablet]" 
                                           value="<?php echo esc_attr($options['button_padding_h_tablet']); ?>" 
                                           min="0" max="100" class="small-text" placeholder="20">
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Banner Vertical Padding (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[banner_padding_v_tablet]" 
                                           value="<?php echo esc_attr($options['banner_padding_v_tablet']); ?>" 
                                           min="0" max="100" class="small-text" placeholder="18">
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Mobile Settings -->
                    <div class="scc-device-section">
                        <h3>📱 <?php _e('Mobile (Below 768px)', 'brayne-cookie-consent'); ?></h3>
                        <table class="form-table">
                            <tr>
                                <th><?php _e('Title Font Size (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[title_size_mobile]" 
                                           value="<?php echo esc_attr($options['title_size_mobile']); ?>" 
                                           min="10" max="40" class="small-text" placeholder="14">
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Text Font Size (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[text_size_mobile]" 
                                           value="<?php echo esc_attr($options['text_size_mobile']); ?>" 
                                           min="10" max="30" class="small-text" placeholder="12">
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Button Font Size (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[button_size_mobile]" 
                                           value="<?php echo esc_attr($options['button_size_mobile']); ?>" 
                                           min="10" max="30" class="small-text" placeholder="13">
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Button Border Radius (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[button_radius_mobile]" 
                                           value="<?php echo esc_attr($options['button_radius_mobile']); ?>" 
                                           min="0" max="50" class="small-text" placeholder="5">
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Button Vertical Padding (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[button_padding_v_mobile]" 
                                           value="<?php echo esc_attr($options['button_padding_v_mobile']); ?>" 
                                           min="0" max="50" class="small-text" placeholder="12">
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Button Horizontal Padding (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[button_padding_h_mobile]" 
                                           value="<?php echo esc_attr($options['button_padding_h_mobile']); ?>" 
                                           min="0" max="100" class="small-text" placeholder="20">
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Banner Vertical Padding (px)', 'brayne-cookie-consent'); ?></th>
                                <td>
                                    <input type="number" name="scc_options[banner_padding_v_mobile]" 
                                           value="<?php echo esc_attr($options['banner_padding_v_mobile']); ?>" 
                                           min="0" max="100" class="small-text" placeholder="15">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="scc-responsive-info" style="margin-top: 30px; padding: 15px; background: #f0f8ff; border-left: 4px solid #2196F3; border-radius: 4px;">
                    <h4 style="margin-top: 0;">💡 <?php _e('Responsive Tips', 'brayne-cookie-consent'); ?></h4>
                    <ul style="margin: 10px 0;">
                        <li><?php _e('<strong>Desktop:</strong> Use larger fonts and horizontal layout for maximum readability', 'brayne-cookie-consent'); ?></li>
                        <li><?php _e('<strong>Tablet:</strong> Slightly smaller fonts, test both horizontal and vertical layouts', 'brayne-cookie-consent'); ?></li>
                        <li><?php _e('<strong>Mobile:</strong> Smaller fonts to fit screen, vertical layout recommended for better UX', 'brayne-cookie-consent'); ?></li>
                        <li><?php _e('<strong>Testing:</strong> Use browser DevTools (F12) to test different screen sizes', 'brayne-cookie-consent'); ?></li>
                        <li><?php _e('<strong>Breakpoints:</strong> Desktop (>1024px), Tablet (768-1024px), Mobile (<768px)', 'brayne-cookie-consent'); ?></li>
                    </ul>
                </div>
            </div>
            
            <!-- Display Rules Tab -->
            <div id="tab-display" class="scc-tab-content" style="display:none;">
                <h2><?php _e('🎯 Display Rules', 'brayne-cookie-consent'); ?></h2>
                <p class="description" style="margin-bottom: 20px;">
                    <?php _e('Control where the cookie consent banner appears on your website.', 'brayne-cookie-consent'); ?>
                </p>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="display_mode"><?php _e('Display Mode', 'brayne-cookie-consent'); ?></label>
                        </th>
                        <td>
                            <select id="display_mode" name="scc_options[display_mode]" class="regular-text">
                                <option value="all_pages" <?php selected($options['display_mode'], 'all_pages'); ?>><?php _e('All Pages (Default)', 'brayne-cookie-consent'); ?></option>
                                <option value="homepage_only" <?php selected($options['display_mode'], 'homepage_only'); ?>><?php _e('Homepage Only', 'brayne-cookie-consent'); ?></option>
                                <option value="all_except_homepage" <?php selected($options['display_mode'], 'all_except_homepage'); ?>><?php _e('All Pages Except Homepage', 'brayne-cookie-consent'); ?></option>
                                <option value="specific_pages" <?php selected($options['display_mode'], 'specific_pages'); ?>><?php _e('Specific Pages Only', 'brayne-cookie-consent'); ?></option>
                                <option value="exclude_pages" <?php selected($options['display_mode'], 'exclude_pages'); ?>><?php _e('All Pages Except Selected', 'brayne-cookie-consent'); ?></option>
                            </select>
                            <p class="description"><?php _e('Choose where the banner should appear', 'brayne-cookie-consent'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <div id="scc-specific-pages" style="margin-top: 20px; display: <?php echo ($options['display_mode'] == 'specific_pages') ? 'block' : 'none'; ?>;">
                    <h3><?php _e('Select Pages to Show Banner', 'brayne-cookie-consent'); ?></h3>
                    <p class="description"><?php _e('Check the pages where you want the cookie banner to appear:', 'brayne-cookie-consent'); ?></p>
                    <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; background: #fff; border-radius: 4px;">
                        <?php
                        $pages = get_pages();
                        if (!empty($pages)) {
                            foreach ($pages as $page) {
                                $checked = isset($options['selected_pages']) && is_array($options['selected_pages']) && in_array($page->ID, $options['selected_pages']) ? 'checked' : '';
                                echo '<label style="display: block; margin-bottom: 8px;">';
                                echo '<input type="checkbox" name="scc_options[selected_pages][]" value="' . esc_attr($page->ID) . '" ' . $checked . '> ';
                                echo esc_html($page->post_title);
                                echo '</label>';
                            }
                        } else {
                            echo '<p>' . __('No pages found.', 'brayne-cookie-consent') . '</p>';
                        }
                        ?>
                    </div>
                </div>
                
                <div id="scc-exclude-pages" style="margin-top: 20px; display: <?php echo ($options['display_mode'] == 'exclude_pages') ? 'block' : 'none'; ?>;">
                    <h3><?php _e('Select Pages to Hide Banner', 'brayne-cookie-consent'); ?></h3>
                    <p class="description"><?php _e('Check the pages where you want to HIDE the cookie banner:', 'brayne-cookie-consent'); ?></p>
                    <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; background: #fff; border-radius: 4px;">
                        <?php
                        $pages = get_pages();
                        if (!empty($pages)) {
                            foreach ($pages as $page) {
                                $checked = isset($options['excluded_pages']) && is_array($options['excluded_pages']) && in_array($page->ID, $options['excluded_pages']) ? 'checked' : '';
                                echo '<label style="display: block; margin-bottom: 8px;">';
                                echo '<input type="checkbox" name="scc_options[excluded_pages][]" value="' . esc_attr($page->ID) . '" ' . $checked . '> ';
                                echo esc_html($page->post_title);
                                echo '</label>';
                            }
                        } else {
                            echo '<p>' . __('No pages found.', 'brayne-cookie-consent') . '</p>';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="scc-display-examples" style="margin-top: 30px; padding: 15px; background: #e8f5e9; border-left: 4px solid #4caf50; border-radius: 4px;">
                    <h4 style="margin-top: 0;">💡 <?php _e('Display Rules Examples', 'brayne-cookie-consent'); ?></h4>
                    <ul style="margin: 10px 0;">
                        <li><strong><?php _e('All Pages:', 'brayne-cookie-consent'); ?></strong> <?php _e('Show banner on every page of your website', 'brayne-cookie-consent'); ?></li>
                        <li><strong><?php _e('Homepage Only:', 'brayne-cookie-consent'); ?></strong> <?php _e('Only show on the main homepage, hide on all other pages', 'brayne-cookie-consent'); ?></li>
                        <li><strong><?php _e('All Except Homepage:', 'brayne-cookie-consent'); ?></strong> <?php _e('Show everywhere except the homepage', 'brayne-cookie-consent'); ?></li>
                        <li><strong><?php _e('Specific Pages:', 'brayne-cookie-consent'); ?></strong> <?php _e('Show only on selected pages (e.g., Homepage & About Us)', 'brayne-cookie-consent'); ?></li>
                        <li><strong><?php _e('Exclude Pages:', 'brayne-cookie-consent'); ?></strong> <?php _e('Show on all pages EXCEPT the ones you select', 'brayne-cookie-consent'); ?></li>
                    </ul>
                </div>
                
                <script>
                    jQuery(document).ready(function($) {
                        $('#display_mode').on('change', function() {
                            var mode = $(this).val();
                            $('#scc-specific-pages').hide();
                            $('#scc-exclude-pages').hide();
                            
                            if (mode === 'specific_pages') {
                                $('#scc-specific-pages').show();
                            } else if (mode === 'exclude_pages') {
                                $('#scc-exclude-pages').show();
                            }
                        });
                    });
                </script>
            </div>
            
            <?php submit_button(__('Save All Settings', 'brayne-cookie-consent'), 'primary large'); ?>
        </form>
        
        <hr>
        
        <h2><?php _e('Testing & Reset', 'brayne-cookie-consent'); ?></h2>
        <p><?php _e('To test the cookie banner, clear your browser cookies or open an incognito/private window.', 'brayne-cookie-consent'); ?></p>
        <p><?php _e('The banner will only show to visitors who haven\'t made a choice yet.', 'brayne-cookie-consent'); ?></p>
        
        <div class="scc-test-devices" style="margin-top: 20px; padding: 15px; background: #fff3cd; border-left: 4px solid #ff9800; border-radius: 4px;">
            <h4 style="margin-top: 0;">🧪 <?php _e('Test on Different Devices', 'brayne-cookie-consent'); ?></h4>
            <ol>
                <li><?php _e('<strong>Desktop:</strong> Open in regular browser window (full screen)', 'brayne-cookie-consent'); ?></li>
                <li><?php _e('<strong>Tablet:</strong> Resize browser to 768-1024px or use DevTools (F12) → Device Toolbar', 'brayne-cookie-consent'); ?></li>
                <li><?php _e('<strong>Mobile:</strong> Resize to <768px or use DevTools mobile emulation', 'brayne-cookie-consent'); ?></li>
                <li><?php _e('<strong>Real Devices:</strong> Test on actual phones/tablets for best results', 'brayne-cookie-consent'); ?></li>
            </ol>
        </div>
        
        <hr>
        
        <h2><?php _e('Privacy Policy', 'brayne-cookie-consent'); ?></h2>
        <p>
            <?php 
            if (get_privacy_policy_url()) {
                printf(
                    __('Your privacy policy page is set to: <a href="%s" target="_blank">%s</a>', 'brayne-cookie-consent'),
                    esc_url(admin_url('options-privacy.php')),
                    esc_html(get_the_title(get_option('wp_page_for_privacy_policy')))
                );
            } else {
                printf(
                    __('No privacy policy page set. <a href="%s">Set one here</a>', 'brayne-cookie-consent'),
                    esc_url(admin_url('options-privacy.php'))
                );
            }
            ?>
        </p>
    </div>
    
    <style>
        .scc-admin-wrap .nav-tab-wrapper {
            margin: 20px 0;
        }
        .scc-tab-content h2 {
            margin-top: 20px;
        }
        .scc-tab-content h3 {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .scc-responsive-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
            margin: 20px 0;
        }
        .scc-device-section {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
        }
        .scc-device-section h3 {
            margin-top: 0;
            padding-top: 0;
            border-top: none;
            color: #2196F3;
        }
        .scc-device-section table {
            margin: 0;
        }
        .scc-device-section th {
            width: 200px;
        }
        @media (max-width: 1200px) {
            .scc-responsive-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
    
    <script>
        jQuery(document).ready(function($) {
            // Initialize color pickers
            $('.scc-color-picker').wpColorPicker();
            
            // Tab switching
            $('.nav-tab').on('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all tabs
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                
                // Hide all tab content
                $('.scc-tab-content').hide();
                
                // Show selected tab content
                var tabId = $(this).attr('href');
                $(tabId).show();
            });
        });
    </script>
    <?php
}

/**
 * Enqueue admin scripts
 */
function scc_enqueue_admin_scripts($hook) {
    if ('settings_page_brayne-cookie-consent' !== $hook) {
        return;
    }
    
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
}
add_action('admin_enqueue_scripts', 'scc_enqueue_admin_scripts');

/**
 * Add settings link on plugin page
 */
function scc_add_settings_link($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=brayne-cookie-consent') . '">' . __('Settings', 'brayne-cookie-consent') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . SCC_PLUGIN_BASENAME, 'scc_add_settings_link');

/**
 * Plugin activation
 */
function scc_activate() {
    // Get default options from settings page defaults
    $default_options = array(
        'banner_title' => '🍪 We use cookies',
        'banner_text' => 'We use cookies to improve your experience on our site. By continuing to browse, you agree to our use of cookies.',
        'accept_text' => 'Accept All Cookies',
        'decline_text' => 'Decline',
        'show_decline' => true,
        'cookie_duration' => 365,
        'banner_position' => 'bottom',
        'banner_bg_color' => '#ffffff',
        'border_color' => '#E1195B',
        'border_width' => '3',
        'card_max_width' => '400',
        'card_border_radius' => '12',
        'card_padding_v' => '20',
        'card_padding_h' => '20',
        'card_button_gap' => '10',
        'card_text_align' => 'center',
        'box_shadow' => 'yes',
        'font_family' => 'inherit',
        'title_color' => '#222222',
        'title_size_desktop' => '16',
        'title_size_tablet' => '15',
        'title_size_mobile' => '14',
        'text_color' => '#333333',
        'text_size_desktop' => '14',
        'text_size_tablet' => '13',
        'text_size_mobile' => '12',
        'link_color' => '#E1195B',
        'link_hover_color' => '#48144A',
        'accept_bg_color' => '#E1195B',
        'accept_text_color' => '#ffffff',
        'accept_hover_bg' => '#48144A',
        'accept_hover_text' => '#ffffff',
        'decline_bg_color' => '#f5f5f5',
        'decline_text_color' => '#666666',
        'decline_hover_bg' => '#e0e0e0',
        'decline_hover_text' => '#333333',
        'button_radius_desktop' => '5',
        'button_radius_tablet' => '5',
        'button_radius_mobile' => '5',
        'button_size_desktop' => '14',
        'button_size_tablet' => '13',
        'button_size_mobile' => '13',
        'button_padding_v_desktop' => '12',
        'button_padding_v_tablet' => '10',
        'button_padding_v_mobile' => '12',
        'button_padding_h_desktop' => '24',
        'button_padding_h_tablet' => '20',
        'button_padding_h_mobile' => '20',
        'button_font_weight' => '600',
        'banner_padding_v_desktop' => '20',
        'banner_padding_v_tablet' => '18',
        'banner_padding_v_mobile' => '15',
        'content_direction_desktop' => 'row',
        'content_direction_tablet' => 'row',
        'content_direction_mobile' => 'column',
        // Legacy support - remove these after migration
        'banner_layout_tablet' => 'row',
        'banner_layout_mobile' => 'column',
        'button_layout_desktop' => 'horizontal',
        'button_layout_tablet' => 'horizontal',
        'button_layout_mobile' => 'vertical',
        'display_mode' => 'all_pages',
        'selected_pages' => array(),
        'excluded_pages' => array(),
    );
    
    add_option('scc_options', $default_options);
}
register_activation_hook(__FILE__, 'scc_activate');

/**
 * Plugin deactivation
 */
function scc_deactivate() {
    // Nothing to do here for now
}
register_deactivation_hook(__FILE__, 'scc_deactivate');

