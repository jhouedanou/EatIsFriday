<?php
/**
 * Admin Customization for EIF Backend Theme
 * Additional admin interface improvements
 *
 * @package EIFBackend
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add custom columns to Activities admin list
 */
function eatisfamily_activity_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['activity_date'] = __('Date', 'eatisfamily');
    $new_columns['category'] = __('Category', 'eatisfamily');
    $new_columns['price'] = __('Price', 'eatisfamily');
    $new_columns['spots'] = __('Available Spots', 'eatisfamily');
    $new_columns['status'] = __('Status', 'eatisfamily');
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}
add_filter('manage_activity_posts_columns', 'eatisfamily_activity_columns');

/**
 * Fill custom columns with data
 */
function eatisfamily_activity_custom_column($column, $post_id) {
    switch ($column) {
        case 'activity_date':
            $date = get_post_meta($post_id, 'activity_date', true);
            echo $date ? esc_html(date('M j, Y', strtotime($date))) : '—';
            break;
            
        case 'category':
            echo esc_html(get_post_meta($post_id, 'category', true) ?: '—');
            break;
            
        case 'price':
            echo esc_html(get_post_meta($post_id, 'price', true) ?: '—');
            break;
            
        case 'spots':
            $available = get_post_meta($post_id, 'available_spots', true);
            $capacity = get_post_meta($post_id, 'capacity', true);
            if ($available && $capacity) {
                echo esc_html($available . ' / ' . $capacity);
            } else {
                echo '—';
            }
            break;
            
        case 'status':
            $status = get_post_meta($post_id, 'status', true) ?: 'open';
            $colors = array(
                'open' => 'green',
                'closed' => 'red',
                'full' => 'orange',
            );
            $color = $colors[$status] ?? 'gray';
            echo '<span style="color: ' . esc_attr($color) . '; font-weight: bold;">' . esc_html(ucfirst($status)) . '</span>';
            break;
    }
}
add_action('manage_activity_posts_custom_column', 'eatisfamily_activity_custom_column', 10, 2);

/**
 * Add custom columns to Jobs admin list
 */
function eatisfamily_job_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['department'] = __('Department', 'eatisfamily');
    $new_columns['job_type'] = __('Type', 'eatisfamily');
    $new_columns['venue'] = __('Venue', 'eatisfamily');
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}
add_filter('manage_job_posts_columns', 'eatisfamily_job_columns');

/**
 * Fill Job custom columns
 */
function eatisfamily_job_custom_column($column, $post_id) {
    switch ($column) {
        case 'department':
            echo esc_html(get_post_meta($post_id, 'department', true) ?: '—');
            break;
            
        case 'job_type':
            echo esc_html(get_post_meta($post_id, 'job_type', true) ?: '—');
            break;
            
        case 'venue':
            echo esc_html(get_post_meta($post_id, 'venue_id', true) ?: '—');
            break;
    }
}
add_action('manage_job_posts_custom_column', 'eatisfamily_job_custom_column', 10, 2);

// Note: Meta boxes for activity, job, and venue are registered in inc/meta-boxes.php
// via eatisfamily_register_meta_boxes(). Save hooks are handled per-post-type there.

/**
 * Add admin notice after theme activation
 */
function eatisfamily_activation_notice() {
    global $pagenow;
    
    if ($pagenow == 'themes.php' && isset($_GET['activated'])) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><strong>EIF Backend theme activated!</strong></p>
            <p>Don't forget to:</p>
            <ol>
                <li>Go to <a href="<?php echo admin_url('options-permalink.php'); ?>">Settings > Permalinks</a> and save to refresh permalinks</li>
                <li>Check your <a href="<?php echo rest_url('eatisfamily/v1/'); ?>" target="_blank">API endpoints</a></li>
                <li>Import your data if needed</li>
            </ol>
        </div>
        <?php
    }
}
add_action('admin_notices', 'eatisfamily_activation_notice');
