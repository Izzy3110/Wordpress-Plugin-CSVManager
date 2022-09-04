<?php
ini_set('display_errors','true');
error_reporting(-1);

/**
 * CSV Manager
 *
 * @package           CSV Manager
 * @author            Sascha Frank
 * @copyright         08.2022 @ TabTeam GbR
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       CSV Manager
 * Plugin URI:        https://products.tabteam.media/php-csv-manager
 * Description:       CSV Manager/Exporter.
 * Version:           0.0.1
 * Requires at least: 5.2
 * Requires PHP:      8.0
 * Author:            Sascha Frank
 * Author URI:        https://whatsyourlanguage.world
 * Text Domain:       csv-manager
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://products.tabteam.media/php-csv-manager/update
 */
 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// create custom plugin settings menu
add_action('admin_menu', 'my_cool_plugin_create_menu');

function my_cool_plugin_create_menu() {
	global $csvmanager_settings;
	//create new top-level menu
	add_menu_page('CSV Manager', 'CSV Manager', 'administrator', __FILE__, 'csv_manager_plugin_main_page' , plugins_url('/images/favicon_index_32_16x16_1.png', __FILE__) );
	$csvmanager_settings = add_options_page(__('CSV Manager - Settings', 'csvmanager_settings'), __('CSV Manager - Settings', 'csv_manager'), 'manage_options', 'csv-manager-settings', 'csvmanager_render_options_admin');

	//call register settings function
	add_action( 'admin_init', 'register_my_cool_plugin_settings' );
}

function csvmanager_render_options_admin() {
	?>
	<div class="wrap">
		<h2><?php _e('CSV Manager', 'csv_manager'); ?></h2>
		<form id="csv-form" action="" method="POST">
			<div>
			<input type="submit" name="csvmanager_settings-submit" class="button-primary" value="<?php _e('Submit Form', 'csvmanager_settings'); ?>">
			</div>
		</form>
		<input type="file" id="csvmanager_file"><br>
		<div id="csvmanager_results">
		</div>
	</div>
	<?php
}

function csvmanager_load_scripts($hook) {
	global $csvmanager_settings;
	if($hook != $csvmanager_settings)
		return;
	wp_enqueue_script('csvmanager-ajax', plugin_dir_url(__FILE__).'js/csvmanager-ajax.js');
	wp_localize_script(
		'csvmanager-ajax', 
		'csvmanager_vars', 
		array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('nonce_name')
		)
	);
}
add_action('admin_enqueue_scripts', 'csvmanager_load_scripts');


function csvmanager_get_results() {
	check_ajax_referer('nonce_name');
	// echo $_POST["nonce_name"];
	$response["custom"] = "Do";
	$response["success"] = true;
	$response["n"] = count(array_keys($_FILES));
	$response["f"] = $_FILES['file']['tmp_name']; 
	$response["len"] = strlen($_FILES['file']['name']) > 0;
	$response["f_name"] = $_FILES['file']['name'];
	$response["_keys"] = array_keys($_POST);
	if(!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'nonce_name')) {
		die("Permission check failed!");
	}
	
	echo json_encode($response);
	die();
}
add_action('wp_ajax_csvmanager_get_results', 'csvmanager_get_results');


function register_my_cool_plugin_settings() {
	//register our settings
	register_setting( 'my-cool-plugin-settings-group', 'new_option_name' );
	register_setting( 'my-cool-plugin-settings-group', 'some_other_option' );
	register_setting( 'my-cool-plugin-settings-group', 'option_etc' );
}

function my_cool_plugin_settings_page() {
?>
<div class="wrap">
<h1>Your Plugin Name</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'my-cool-plugin-settings-group' ); ?>
    <?php do_settings_sections( 'my-cool-plugin-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">New Option Name</th>
        <td><input type="text" name="new_option_name" value="<?php echo esc_attr( get_option('new_option_name') ); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Some Other Option</th>
        <td><input type="text" name="some_other_option" value="<?php echo esc_attr( get_option('some_other_option') ); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Options, Etc.</th>
        <td><input type="text" name="option_etc" value="<?php echo esc_attr( get_option('option_etc') ); ?>" /></td>
        </tr>
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>