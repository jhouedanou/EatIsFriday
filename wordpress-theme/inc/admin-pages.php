<?php
/**
 * Eat Is Family - Admin Pages
 * 
 * This file handles admin pages for editing Site Content and Pages Content
 * that were previously stored in JSON files.
 * 
 * @package EatIsFamily
 * @version 2.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ============================================================================
 * ADMIN MENU PAGES
 * ============================================================================
 */

/**
 * Add admin menu pages for Site Content and Pages Content
 */
function eatisfamily_add_content_admin_pages() {
    // Main menu page
    add_menu_page(
        __('Site Content', 'eatisfamily'),
        __('Site Content', 'eatisfamily'),
        'manage_options',
        'eatisfamily-site-content',
        'eatisfamily_site_content_page',
        'dashicons-admin-site-alt3',
        30
    );
    
    // Pages Content submenu
    add_submenu_page(
        'eatisfamily-site-content',
        __('Pages Content', 'eatisfamily'),
        __('Pages Content', 'eatisfamily'),
        'manage_options',
        'eatisfamily-pages-content',
        'eatisfamily_pages_content_page'
    );
    
    // Data Management submenu (for manual import)
    add_submenu_page(
        'eatisfamily-site-content',
        __('Data Management', 'eatisfamily'),
        __('Data Management', 'eatisfamily'),
        'manage_options',
        'eatisfamily-data-management',
        'eatisfamily_data_management_page'
    );
}
add_action('admin_menu', 'eatisfamily_add_content_admin_pages');

/**
 * ============================================================================
 * SITE CONTENT EDITOR
 * ============================================================================
 */

/**
 * Site Content admin page
 */
function eatisfamily_site_content_page() {
    // Handle form submission
    if (isset($_POST['eatisfamily_site_content_nonce']) && 
        wp_verify_nonce($_POST['eatisfamily_site_content_nonce'], 'save_site_content')) {
        
        $site_content = array(
            'site' => array(
                'name' => sanitize_text_field($_POST['site_name'] ?? ''),
                'tagline' => sanitize_text_field($_POST['site_tagline'] ?? ''),
                'description' => sanitize_textarea_field($_POST['site_description'] ?? ''),
                'seo' => array(
                    'default_title' => sanitize_text_field($_POST['seo_default_title'] ?? ''),
                    'default_description' => sanitize_textarea_field($_POST['seo_default_description'] ?? ''),
                    'keywords' => sanitize_text_field($_POST['seo_keywords'] ?? ''),
                    'og_image' => esc_url_raw($_POST['seo_og_image'] ?? ''),
                ),
                'contact' => array(
                    'email' => sanitize_email($_POST['contact_email'] ?? ''),
                    'phone' => sanitize_text_field($_POST['contact_phone'] ?? ''),
                ),
                'social' => array(
                    'facebook' => esc_url_raw($_POST['social_facebook'] ?? ''),
                    'instagram' => esc_url_raw($_POST['social_instagram'] ?? ''),
                    'twitter' => esc_url_raw($_POST['social_twitter'] ?? ''),
                    'linkedin' => esc_url_raw($_POST['social_linkedin'] ?? ''),
                    'youtube' => esc_url_raw($_POST['social_youtube'] ?? ''),
                ),
            ),
        );
        
        update_option('eatisfamily_site_content', $site_content);
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Site content saved successfully!', 'eatisfamily') . '</p></div>';
    }
    
    // Get current values
    $site_content = get_option('eatisfamily_site_content', array());
    $site = $site_content['site'] ?? array();
    $seo = $site['seo'] ?? array();
    $contact = $site['contact'] ?? array();
    $social = $site['social'] ?? array();
    
    ?>
    <div class="wrap">
        <h1><?php _e('Site Content Settings', 'eatisfamily'); ?></h1>
        <p class="description"><?php _e('Edit global site information, SEO settings, contact details, and social media links.', 'eatisfamily'); ?></p>
        
        <form method="post" action="">
            <?php wp_nonce_field('save_site_content', 'eatisfamily_site_content_nonce'); ?>
            
            <table class="form-table" role="presentation">
                <tbody>
                    <!-- Site Information -->
                    <tr>
                        <th colspan="2">
                            <h2><?php _e('Site Information', 'eatisfamily'); ?></h2>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="site_name"><?php _e('Site Name', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="site_name" id="site_name" value="<?php echo esc_attr($site['name'] ?? ''); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="site_tagline"><?php _e('Tagline', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="site_tagline" id="site_tagline" value="<?php echo esc_attr($site['tagline'] ?? ''); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="site_description"><?php _e('Description', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <textarea name="site_description" id="site_description" rows="3" class="large-text"><?php echo esc_textarea($site['description'] ?? ''); ?></textarea>
                        </td>
                    </tr>
                    
                    <!-- SEO Settings -->
                    <tr>
                        <th colspan="2">
                            <h2><?php _e('SEO Settings', 'eatisfamily'); ?></h2>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="seo_default_title"><?php _e('Default Title', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="seo_default_title" id="seo_default_title" value="<?php echo esc_attr($seo['default_title'] ?? ''); ?>" class="large-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="seo_default_description"><?php _e('Default Description', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <textarea name="seo_default_description" id="seo_default_description" rows="3" class="large-text"><?php echo esc_textarea($seo['default_description'] ?? ''); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="seo_keywords"><?php _e('Keywords', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="seo_keywords" id="seo_keywords" value="<?php echo esc_attr($seo['keywords'] ?? ''); ?>" class="large-text">
                            <p class="description"><?php _e('Comma-separated keywords', 'eatisfamily'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="seo_og_image"><?php _e('Default OG Image', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="seo_og_image" id="seo_og_image" value="<?php echo esc_attr($seo['og_image'] ?? ''); ?>" class="regular-text">
                            <button type="button" class="button eatisfamily-upload-media" data-target="seo_og_image"><?php _e('Select Image', 'eatisfamily'); ?></button>
                        </td>
                    </tr>
                    
                    <!-- Contact Information -->
                    <tr>
                        <th colspan="2">
                            <h2><?php _e('Contact Information', 'eatisfamily'); ?></h2>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="contact_email"><?php _e('Email', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="email" name="contact_email" id="contact_email" value="<?php echo esc_attr($contact['email'] ?? ''); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="contact_phone"><?php _e('Phone', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="contact_phone" id="contact_phone" value="<?php echo esc_attr($contact['phone'] ?? ''); ?>" class="regular-text">
                        </td>
                    </tr>
                    
                    <!-- Social Media -->
                    <tr>
                        <th colspan="2">
                            <h2><?php _e('Social Media Links', 'eatisfamily'); ?></h2>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="social_facebook"><?php _e('Facebook', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="url" name="social_facebook" id="social_facebook" value="<?php echo esc_attr($social['facebook'] ?? ''); ?>" class="regular-text" placeholder="https://facebook.com/...">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="social_instagram"><?php _e('Instagram', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="url" name="social_instagram" id="social_instagram" value="<?php echo esc_attr($social['instagram'] ?? ''); ?>" class="regular-text" placeholder="https://instagram.com/...">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="social_twitter"><?php _e('Twitter', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="url" name="social_twitter" id="social_twitter" value="<?php echo esc_attr($social['twitter'] ?? ''); ?>" class="regular-text" placeholder="https://twitter.com/...">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="social_linkedin"><?php _e('LinkedIn', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="url" name="social_linkedin" id="social_linkedin" value="<?php echo esc_attr($social['linkedin'] ?? ''); ?>" class="regular-text" placeholder="https://linkedin.com/company/...">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="social_youtube"><?php _e('YouTube', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="url" name="social_youtube" id="social_youtube" value="<?php echo esc_attr($social['youtube'] ?? ''); ?>" class="regular-text" placeholder="https://youtube.com/...">
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <?php submit_button(__('Save Site Content', 'eatisfamily')); ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('.eatisfamily-upload-media').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var targetId = button.data('target');
            
            var frame = wp.media({
                title: 'Select Image',
                button: { text: 'Use this image' },
                multiple: false
            });
            
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#' + targetId).val(attachment.url);
            });
            
            frame.open();
        });
    });
    </script>
    <?php
}

/**
 * ============================================================================
 * PAGES CONTENT EDITOR
 * ============================================================================
 */

/**
 * Pages Content admin page
 */
function eatisfamily_pages_content_page() {
    // Handle form submission
    if (isset($_POST['eatisfamily_pages_content_nonce']) && 
        wp_verify_nonce($_POST['eatisfamily_pages_content_nonce'], 'save_pages_content')) {
        
        $pages_content = array(
            'homepage' => array(
                'hero' => array(
                    'title' => sanitize_text_field($_POST['homepage_hero_title'] ?? ''),
                    'subtitle' => sanitize_text_field($_POST['homepage_hero_subtitle'] ?? ''),
                    'cta_text' => sanitize_text_field($_POST['homepage_hero_cta_text'] ?? ''),
                    'cta_link' => esc_url_raw($_POST['homepage_hero_cta_link'] ?? ''),
                    'background_image' => esc_url_raw($_POST['homepage_hero_bg'] ?? ''),
                ),
            ),
            'about' => array(
                'hero' => array(
                    'title' => sanitize_text_field($_POST['about_hero_title'] ?? ''),
                    'subtitle' => sanitize_text_field($_POST['about_hero_subtitle'] ?? ''),
                    'background_image' => esc_url_raw($_POST['about_hero_bg'] ?? ''),
                ),
                'intro_section' => array(
                    'title' => sanitize_text_field($_POST['about_intro_title'] ?? ''),
                    'content' => wp_kses_post($_POST['about_intro_content'] ?? ''),
                ),
                'timeline_title' => sanitize_text_field($_POST['about_timeline_title'] ?? 'Our History'),
            ),
            'contact' => array(
                'hero' => array(
                    'title' => sanitize_text_field($_POST['contact_hero_title'] ?? ''),
                    'subtitle' => sanitize_text_field($_POST['contact_hero_subtitle'] ?? ''),
                ),
                'form_title' => sanitize_text_field($_POST['contact_form_title'] ?? ''),
                'form_subtitle' => sanitize_text_field($_POST['contact_form_subtitle'] ?? ''),
            ),
            'careers' => array(
                'hero' => array(
                    'title' => sanitize_text_field($_POST['careers_hero_title'] ?? ''),
                    'subtitle' => sanitize_text_field($_POST['careers_hero_subtitle'] ?? ''),
                ),
                'benefits_title' => sanitize_text_field($_POST['careers_benefits_title'] ?? ''),
                'benefits' => array_filter(array_map('sanitize_text_field', $_POST['careers_benefits'] ?? array())),
            ),
            'events' => array(
                'hero' => array(
                    'title' => sanitize_text_field($_POST['events_hero_title'] ?? ''),
                    'subtitle' => sanitize_text_field($_POST['events_hero_subtitle'] ?? ''),
                ),
            ),
        );
        
        update_option('eatisfamily_pages_content', $pages_content);
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Pages content saved successfully!', 'eatisfamily') . '</p></div>';
    }
    
    // Get current values
    $pages_content = get_option('eatisfamily_pages_content', array());
    $homepage = $pages_content['homepage'] ?? array();
    $about = $pages_content['about'] ?? array();
    $contact = $pages_content['contact'] ?? array();
    $careers = $pages_content['careers'] ?? array();
    $events = $pages_content['events'] ?? array();
    
    ?>
    <div class="wrap">
        <h1><?php _e('Pages Content Settings', 'eatisfamily'); ?></h1>
        <p class="description"><?php _e('Edit content for each page of your website.', 'eatisfamily'); ?></p>
        
        <form method="post" action="">
            <?php wp_nonce_field('save_pages_content', 'eatisfamily_pages_content_nonce'); ?>
            
            <h2 class="nav-tab-wrapper">
                <a href="#homepage" class="nav-tab nav-tab-active"><?php _e('Homepage', 'eatisfamily'); ?></a>
                <a href="#about" class="nav-tab"><?php _e('About', 'eatisfamily'); ?></a>
                <a href="#contact" class="nav-tab"><?php _e('Contact', 'eatisfamily'); ?></a>
                <a href="#careers" class="nav-tab"><?php _e('Careers', 'eatisfamily'); ?></a>
                <a href="#events" class="nav-tab"><?php _e('Events', 'eatisfamily'); ?></a>
            </h2>
            
            <!-- Homepage Tab -->
            <div id="homepage" class="tab-content" style="display: block;">
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th colspan="2"><h3><?php _e('Hero Section', 'eatisfamily'); ?></h3></th>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_hero_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_hero_title" id="homepage_hero_title" value="<?php echo esc_attr($homepage['hero']['title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_hero_subtitle"><?php _e('Subtitle', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_hero_subtitle" id="homepage_hero_subtitle" value="<?php echo esc_attr($homepage['hero']['subtitle'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_hero_cta_text"><?php _e('CTA Button Text', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_hero_cta_text" id="homepage_hero_cta_text" value="<?php echo esc_attr($homepage['hero']['cta_text'] ?? ''); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_hero_cta_link"><?php _e('CTA Button Link', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_hero_cta_link" id="homepage_hero_cta_link" value="<?php echo esc_attr($homepage['hero']['cta_link'] ?? ''); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_hero_bg"><?php _e('Background Image', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_hero_bg" id="homepage_hero_bg" value="<?php echo esc_attr($homepage['hero']['background_image'] ?? ''); ?>" class="regular-text">
                                <button type="button" class="button eatisfamily-upload-media" data-target="homepage_hero_bg"><?php _e('Select Image', 'eatisfamily'); ?></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- About Tab -->
            <div id="about" class="tab-content" style="display: none;">
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th colspan="2"><h3><?php _e('Hero Section', 'eatisfamily'); ?></h3></th>
                        </tr>
                        <tr>
                            <th scope="row"><label for="about_hero_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="about_hero_title" id="about_hero_title" value="<?php echo esc_attr($about['hero']['title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="about_hero_subtitle"><?php _e('Subtitle', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="about_hero_subtitle" id="about_hero_subtitle" value="<?php echo esc_attr($about['hero']['subtitle'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="about_hero_bg"><?php _e('Background Image', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="about_hero_bg" id="about_hero_bg" value="<?php echo esc_attr($about['hero']['background_image'] ?? ''); ?>" class="regular-text">
                                <button type="button" class="button eatisfamily-upload-media" data-target="about_hero_bg"><?php _e('Select Image', 'eatisfamily'); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2"><h3><?php _e('Intro Section', 'eatisfamily'); ?></h3></th>
                        </tr>
                        <tr>
                            <th scope="row"><label for="about_intro_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="about_intro_title" id="about_intro_title" value="<?php echo esc_attr($about['intro_section']['title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="about_intro_content"><?php _e('Content', 'eatisfamily'); ?></label></th>
                            <td>
                                <?php 
                                wp_editor($about['intro_section']['content'] ?? '', 'about_intro_content', array(
                                    'textarea_name' => 'about_intro_content',
                                    'textarea_rows' => 8,
                                    'media_buttons' => true,
                                ));
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2"><h3><?php _e('Timeline Section', 'eatisfamily'); ?></h3></th>
                        </tr>
                        <tr>
                            <th scope="row"><label for="about_timeline_title"><?php _e('Timeline Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="about_timeline_title" id="about_timeline_title" value="<?php echo esc_attr($about['timeline_title'] ?? 'Our History'); ?>" class="large-text">
                                <p class="description"><?php _e('Timeline events are managed in the "Timeline" custom post type.', 'eatisfamily'); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Contact Tab -->
            <div id="contact" class="tab-content" style="display: none;">
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th colspan="2"><h3><?php _e('Hero Section', 'eatisfamily'); ?></h3></th>
                        </tr>
                        <tr>
                            <th scope="row"><label for="contact_hero_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="contact_hero_title" id="contact_hero_title" value="<?php echo esc_attr($contact['hero']['title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="contact_hero_subtitle"><?php _e('Subtitle', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="contact_hero_subtitle" id="contact_hero_subtitle" value="<?php echo esc_attr($contact['hero']['subtitle'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2"><h3><?php _e('Contact Form', 'eatisfamily'); ?></h3></th>
                        </tr>
                        <tr>
                            <th scope="row"><label for="contact_form_title"><?php _e('Form Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="contact_form_title" id="contact_form_title" value="<?php echo esc_attr($contact['form_title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="contact_form_subtitle"><?php _e('Form Subtitle', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="contact_form_subtitle" id="contact_form_subtitle" value="<?php echo esc_attr($contact['form_subtitle'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Careers Tab -->
            <div id="careers" class="tab-content" style="display: none;">
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th colspan="2"><h3><?php _e('Hero Section', 'eatisfamily'); ?></h3></th>
                        </tr>
                        <tr>
                            <th scope="row"><label for="careers_hero_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="careers_hero_title" id="careers_hero_title" value="<?php echo esc_attr($careers['hero']['title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="careers_hero_subtitle"><?php _e('Subtitle', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="careers_hero_subtitle" id="careers_hero_subtitle" value="<?php echo esc_attr($careers['hero']['subtitle'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2"><h3><?php _e('Benefits Section', 'eatisfamily'); ?></h3></th>
                        </tr>
                        <tr>
                            <th scope="row"><label for="careers_benefits_title"><?php _e('Benefits Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="careers_benefits_title" id="careers_benefits_title" value="<?php echo esc_attr($careers['benefits_title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label><?php _e('Benefits List', 'eatisfamily'); ?></label></th>
                            <td>
                                <div class="eatisfamily-repeater">
                                    <div class="repeater-items">
                                        <?php 
                                        $benefits = $careers['benefits'] ?? array('');
                                        foreach ($benefits as $benefit): 
                                        ?>
                                        <div class="repeater-item" style="display: flex; gap: 5px; margin-bottom: 8px;">
                                            <input type="text" name="careers_benefits[]" value="<?php echo esc_attr($benefit); ?>" class="regular-text" placeholder="<?php esc_attr_e('Enter a benefit...', 'eatisfamily'); ?>">
                                            <button type="button" class="button repeater-remove">✕</button>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="button" class="button repeater-add">+ <?php _e('Add Benefit', 'eatisfamily'); ?></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Events Tab -->
            <div id="events" class="tab-content" style="display: none;">
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th colspan="2"><h3><?php _e('Hero Section', 'eatisfamily'); ?></h3></th>
                        </tr>
                        <tr>
                            <th scope="row"><label for="events_hero_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="events_hero_title" id="events_hero_title" value="<?php echo esc_attr($events['hero']['title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="events_hero_subtitle"><?php _e('Subtitle', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="events_hero_subtitle" id="events_hero_subtitle" value="<?php echo esc_attr($events['hero']['subtitle'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <?php submit_button(__('Save Pages Content', 'eatisfamily')); ?>
        </form>
    </div>
    
    <style>
        .nav-tab-wrapper { margin-bottom: 20px; }
        .tab-content { padding: 20px; background: #fff; border: 1px solid #ccd0d4; border-top: none; }
        .repeater-item { display: flex; gap: 5px; margin-bottom: 8px; }
        .repeater-item input { flex: 1; }
        .repeater-remove { color: #d63638 !important; }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Tab switching
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            $('.tab-content').hide();
            $(target).show();
        });
        
        // Media uploader
        $('.eatisfamily-upload-media').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var targetId = button.data('target');
            
            var frame = wp.media({
                title: 'Select Image',
                button: { text: 'Use this image' },
                multiple: false
            });
            
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#' + targetId).val(attachment.url);
            });
            
            frame.open();
        });
        
        // Repeater functionality
        $(document).on('click', '.repeater-add', function() {
            var $container = $(this).closest('.eatisfamily-repeater');
            var $items = $container.find('.repeater-items');
            var $firstItem = $items.find('.repeater-item').first();
            var name = $firstItem.find('input').attr('name');
            var placeholder = $firstItem.find('input').attr('placeholder');
            
            var $newItem = $('<div class="repeater-item" style="display: flex; gap: 5px; margin-bottom: 8px;">' +
                '<input type="text" name="' + name + '" value="" class="regular-text" placeholder="' + placeholder + '">' +
                '<button type="button" class="button repeater-remove">✕</button>' +
                '</div>');
            
            $items.append($newItem);
        });
        
        $(document).on('click', '.repeater-remove', function() {
            var $items = $(this).closest('.repeater-items').find('.repeater-item');
            if ($items.length > 1) {
                $(this).closest('.repeater-item').remove();
            } else {
                $(this).siblings('input').val('');
            }
        });
    });
    </script>
    <?php
}

/**
 * Enqueue media uploader on admin pages
 */
function eatisfamily_admin_pages_scripts($hook) {
    if ($hook === 'toplevel_page_eatisfamily-site-content' || 
        $hook === 'site-content_page_eatisfamily-pages-content' ||
        $hook === 'site-content_page_eatisfamily-data-management') {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'eatisfamily_admin_pages_scripts');

/**
 * ============================================================================
 * DATA MANAGEMENT PAGE
 * ============================================================================
 */

/**
 * Data Management admin page - Manual JSON import
 */
function eatisfamily_data_management_page() {
    // Handle manual import
    if (isset($_POST['eatisfamily_import_nonce']) && 
        wp_verify_nonce($_POST['eatisfamily_import_nonce'], 'manual_import')) {
        
        $json_dir = get_template_directory() . '/data/';
        $results = array();
        
        if (isset($_POST['import_activities'])) {
            require_once get_template_directory() . '/functions.php';
            $count = eatisfamily_import_activities($json_dir . 'activities.json');
            $results[] = sprintf(__('Activities: %d imported', 'eatisfamily'), $count ?: 0);
        }
        
        if (isset($_POST['import_events'])) {
            $count = eatisfamily_import_events($json_dir . 'events.json');
            $results[] = sprintf(__('Events: %d imported', 'eatisfamily'), $count ?: 0);
        }
        
        if (isset($_POST['import_jobs'])) {
            $count = eatisfamily_import_jobs($json_dir . 'jobs.json');
            $results[] = sprintf(__('Jobs: %d imported', 'eatisfamily'), $count ?: 0);
        }
        
        if (isset($_POST['import_venues'])) {
            $count = eatisfamily_import_venues($json_dir . 'venues.json');
            $results[] = sprintf(__('Venues: %d imported', 'eatisfamily'), $count ?: 0);
        }
        
        if (isset($_POST['import_blog'])) {
            $count = eatisfamily_import_blog_posts($json_dir . 'blog-posts.json');
            $results[] = sprintf(__('Blog Posts: %d imported', 'eatisfamily'), $count ?: 0);
        }
        
        if (isset($_POST['import_site_content'])) {
            eatisfamily_import_site_content($json_dir . 'site-content.json');
            $results[] = __('Site Content: Updated', 'eatisfamily');
        }
        
        if (isset($_POST['import_pages_content'])) {
            eatisfamily_import_pages_content($json_dir . 'pages-content.json');
            $results[] = __('Pages Content: Updated', 'eatisfamily');
        }
        
        echo '<div class="notice notice-success is-dismissible"><p><strong>' . __('Import completed!', 'eatisfamily') . '</strong><br>' . implode('<br>', $results) . '</p></div>';
    }
    
    // Get stats
    $activities_count = wp_count_posts('activity')->publish;
    $events_count = wp_count_posts('event')->publish;
    $jobs_count = wp_count_posts('job')->publish;
    $venues_count = wp_count_posts('venue')->publish;
    $blog_count = wp_count_posts('post')->publish;
    
    $json_dir = get_template_directory() . '/data/';
    ?>
    <div class="wrap">
        <h1><?php _e('Data Management', 'eatisfamily'); ?></h1>
        
        <div class="card" style="max-width: 800px;">
            <h2><?php _e('Current Content', 'eatisfamily'); ?></h2>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php _e('Content Type', 'eatisfamily'); ?></th>
                        <th><?php _e('Count', 'eatisfamily'); ?></th>
                        <th><?php _e('Manage', 'eatisfamily'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong><?php _e('Activities', 'eatisfamily'); ?></strong></td>
                        <td><?php echo $activities_count; ?></td>
                        <td><a href="<?php echo admin_url('edit.php?post_type=activity'); ?>" class="button"><?php _e('View All', 'eatisfamily'); ?></a></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Events', 'eatisfamily'); ?></strong></td>
                        <td><?php echo $events_count; ?></td>
                        <td><a href="<?php echo admin_url('edit.php?post_type=event'); ?>" class="button"><?php _e('View All', 'eatisfamily'); ?></a></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Jobs', 'eatisfamily'); ?></strong></td>
                        <td><?php echo $jobs_count; ?></td>
                        <td><a href="<?php echo admin_url('edit.php?post_type=job'); ?>" class="button"><?php _e('View All', 'eatisfamily'); ?></a></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Venues', 'eatisfamily'); ?></strong></td>
                        <td><?php echo $venues_count; ?></td>
                        <td><a href="<?php echo admin_url('edit.php?post_type=venue'); ?>" class="button"><?php _e('View All', 'eatisfamily'); ?></a></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Blog Posts', 'eatisfamily'); ?></strong></td>
                        <td><?php echo $blog_count; ?></td>
                        <td><a href="<?php echo admin_url('edit.php'); ?>" class="button"><?php _e('View All', 'eatisfamily'); ?></a></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Timeline Events', 'eatisfamily'); ?></strong></td>
                        <td><?php echo wp_count_posts('timeline_event')->publish; ?></td>
                        <td><a href="<?php echo admin_url('edit.php?post_type=timeline_event'); ?>" class="button"><?php _e('View All', 'eatisfamily'); ?></a></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2><?php _e('Manual Import from JSON Files', 'eatisfamily'); ?></h2>
            <p class="description">
                <?php _e('Import data from JSON files located in the /data/ folder. This will NOT overwrite existing content - only new items will be added.', 'eatisfamily'); ?>
            </p>
            
            <form method="post" action="">
                <?php wp_nonce_field('manual_import', 'eatisfamily_import_nonce'); ?>
                
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><?php _e('Select Content to Import', 'eatisfamily'); ?></th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input type="checkbox" name="import_activities" value="1">
                                        <?php _e('Activities', 'eatisfamily'); ?>
                                        <?php if (file_exists($json_dir . 'activities.json')): ?>
                                            <span style="color: green;">✓</span>
                                        <?php else: ?>
                                            <span style="color: red;">✗ <?php _e('File not found', 'eatisfamily'); ?></span>
                                        <?php endif; ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="import_events" value="1">
                                        <?php _e('Events', 'eatisfamily'); ?>
                                        <?php if (file_exists($json_dir . 'events.json')): ?>
                                            <span style="color: green;">✓</span>
                                        <?php else: ?>
                                            <span style="color: red;">✗ <?php _e('File not found', 'eatisfamily'); ?></span>
                                        <?php endif; ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="import_jobs" value="1">
                                        <?php _e('Jobs', 'eatisfamily'); ?>
                                        <?php if (file_exists($json_dir . 'jobs.json')): ?>
                                            <span style="color: green;">✓</span>
                                        <?php else: ?>
                                            <span style="color: red;">✗ <?php _e('File not found', 'eatisfamily'); ?></span>
                                        <?php endif; ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="import_venues" value="1">
                                        <?php _e('Venues', 'eatisfamily'); ?>
                                        <?php if (file_exists($json_dir . 'venues.json')): ?>
                                            <span style="color: green;">✓</span>
                                        <?php else: ?>
                                            <span style="color: red;">✗ <?php _e('File not found', 'eatisfamily'); ?></span>
                                        <?php endif; ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="import_blog" value="1">
                                        <?php _e('Blog Posts', 'eatisfamily'); ?>
                                        <?php if (file_exists($json_dir . 'blog-posts.json')): ?>
                                            <span style="color: green;">✓</span>
                                        <?php else: ?>
                                            <span style="color: red;">✗ <?php _e('File not found', 'eatisfamily'); ?></span>
                                        <?php endif; ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="import_site_content" value="1">
                                        <?php _e('Site Content', 'eatisfamily'); ?>
                                        <?php if (file_exists($json_dir . 'site-content.json')): ?>
                                            <span style="color: green;">✓</span>
                                        <?php else: ?>
                                            <span style="color: red;">✗ <?php _e('File not found', 'eatisfamily'); ?></span>
                                        <?php endif; ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="import_pages_content" value="1">
                                        <?php _e('Pages Content', 'eatisfamily'); ?>
                                        <?php if (file_exists($json_dir . 'pages-content.json')): ?>
                                            <span style="color: green;">✓</span>
                                        <?php else: ?>
                                            <span style="color: red;">✗ <?php _e('File not found', 'eatisfamily'); ?></span>
                                        <?php endif; ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <?php submit_button(__('Import Selected Data', 'eatisfamily'), 'primary', 'submit', true, array('onclick' => 'return confirm("' . esc_js(__('This will import data from JSON files. Existing content will not be overwritten. Continue?', 'eatisfamily')) . '");')); ?>
            </form>
        </div>
        
        <div class="card" style="max-width: 800px; margin-top: 20px; background: #fff3cd; border-left: 4px solid #ffc107;">
            <h2><?php _e('⚠️ Important Notes', 'eatisfamily'); ?></h2>
            <ul>
                <li><?php _e('All content should be managed through WordPress admin panels (Activities, Jobs, Venues, Events, etc.).', 'eatisfamily'); ?></li>
                <li><?php _e('JSON import is only for initial data migration or restoring from backup.', 'eatisfamily'); ?></li>
                <li><?php _e('Changes made in WordPress will NOT update JSON files automatically.', 'eatisfamily'); ?></li>
                <li><?php _e('The Nuxt.js frontend will fetch data from WordPress REST API, not from JSON files.', 'eatisfamily'); ?></li>
            </ul>
        </div>
    </div>
    <?php
}

