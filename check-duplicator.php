<?php
require '/var/www/html/wp-load.php';

echo "=== ACTIVE PLUGINS ===\n";
$plugins = get_option('active_plugins');
print_r($plugins);

echo "\n=== DUPLICATOR PRO CHECK ===\n";
$duplicator_active = in_array('duplicator-pro/duplicator-pro.php', $plugins);
echo "Duplicator Pro active: " . ($duplicator_active ? 'YES' : 'NO') . "\n";

// Check if plugin file exists
$plugin_file = '/var/www/html/wp-content/plugins/duplicator-pro/duplicator-pro.php';
echo "Plugin file exists: " . (file_exists($plugin_file) ? 'YES' : 'NO') . "\n";

// Try to activate if not active
if (!$duplicator_active && file_exists($plugin_file)) {
    echo "\nTrying to activate Duplicator Pro...\n";
    $result = activate_plugin('duplicator-pro/duplicator-pro.php');
    if (is_wp_error($result)) {
        echo "Error: " . $result->get_error_message() . "\n";
    } else {
        echo "Duplicator Pro activated successfully!\n";
    }
}
