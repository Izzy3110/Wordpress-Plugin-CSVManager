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

if ( ! defined( 'WPINC' ) ) {
    die;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(dirname(__FILE__).'/lib/csvmanager.php');

register_activation_hook(__FILE__, 'add_defaults_fn');
// Define default option settings
function add_defaults_fn() {
	$tmp = get_option('plugin_options');
    if(($tmp['chkbox1']=='on')||(!is_array($tmp))) {
		$arr = array("dropdown1"=>"Orange", "text_area" => "Space to put a lot of information here!", "text_string" => "Some sample text", "pass_string" => "123456", "chkbox1" => "", "chkbox2" => "on", "option_set1" => "Triangle");
		update_option('plugin_options', $arr);
	}
}

add_action('admin_menu', 'csvmanager_plugin_create_menu');
function csvmanager_plugin_create_menu() {
	global $csvmanager_settings;
	add_menu_page('CSV Manager', 'CSV Manager', 'administrator', __FILE__, 'csvmanager_plugin_main_page' , plugins_url('/images/favicon_index_32_16x16_1.png', __FILE__) );
	$csvmanager_settings = add_options_page(__('CSV Manager - Settings', 'csvmanager_settings'), __('CSV Manager - Settings', 'csv_manager'), 'manage_options', 'csv-manager-settings', 'csvmanager_render_options_admin');

	//call register settings function
	add_action( 'admin_init', 'register_csvmanager_plugin_settings' );
}


function register_csvmanager_plugin_settings() {
	//register our settings
	register_setting( 'csvmanager-settings-group', 'files_sort_by' );
}

add_action('admin_init', 'sampleoptions_init_fn' );
// Register our settings. Add the settings section, and settings fields

/* ------------------------ */

// Add sub page to the Settings Menu
function sampleoptions_add_page_fn() {
// add optiont to main settings panel
 add_options_page('Big Bang Extra Settings', 'BigBang Settings', 'administrator', __FILE__, 'options_page_fn');

}

// ************************************************************************************************************

// Callback functions

// Init plugin options to white list our options

// Section HTML, displayed before the first option
function  section_text_fn() {
	echo '<p>Below are some examples of different option controls.</p>';
}

// DROP-DOWN-BOX - Name: plugin_options[dropdown1]
function  setting_dropdown_fn() {
	$options = get_option('plugin_options');
	$items = array("Red", "Green", "Blue", "Orange", "White", "Violet", "Yellow");
	echo "<select id='drop_down1' name='plugin_options[dropdown1]'>";
	foreach($items as $item) {
		$selected = ($options['dropdown1']==$item) ? 'selected="selected"' : '';
		echo "<option value='$item' $selected>$item</option>";
	}
	echo "</select>";
}

// TEXTAREA - Name: plugin_options[text_area]
function setting_textarea_fn() {
	$options = get_option('plugin_options');
	echo "<textarea id='plugin_textarea_string' name='plugin_options[text_area]' rows='7' cols='50' type='textarea'>{$options['text_area']}</textarea>";
}

// TEXTBOX - Name: plugin_options[text_string]
function setting_string_fn() {
	$options = get_option('plugin_options');
	echo "<input id='plugin_text_string' name='plugin_options[text_string]' size='40' type='text' value='{$options['text_string']}' />";
}

// PASSWORD-TEXTBOX - Name: plugin_options[pass_string]
function setting_pass_fn() {
	$options = get_option('plugin_options');
	echo "<input id='plugin_text_pass' name='plugin_options[pass_string]' size='40' type='password' value='{$options['pass_string']}' />";
}

// CHECKBOX - Name: plugin_options[chkbox1]
function setting_chk1_fn() {
	$options = get_option('plugin_options');
	if($options['chkbox1']) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='plugin_chk1' name='plugin_options[chkbox1]' type='checkbox' />";
}

// CHECKBOX - Name: plugin_options[chkbox2]
function setting_chk2_fn() {
	$options = get_option('plugin_options');
	if($options['chkbox2']) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='plugin_chk2' name='plugin_options[chkbox2]' type='checkbox' />";
}

// RADIO-BUTTON - Name: plugin_options[option_set1]
function setting_radio_fn() {
	$options = get_option('plugin_options');
	$items = array("Square", "Triangle", "Circle");
	foreach($items as $item) {
		var_dump($item);
		var_dump($options);
		$checked = ($options['option_set1']==$item) ? ' checked="checked" ' : '';
		echo "<label><input ".$checked." value='$item' name='plugin_options[option_set1]' type='radio' /> $item</label><br />";
	}
}
// WYSIWYG Visual Editor - Name: plugin_options[textarea_one]
function setting_visual_fn() {
	$options = get_option('plugin_options');
	$args = array("textarea_name" => "plugin_options[textarea_one]");
	wp_editor( $options['textarea_one'], "plugin_options[textarea_one]", $args );
	
// Add another text box
	$options = get_option('plugin_options');
	$args = array("textarea_name" => "plugin_options[textarea_two]");
	wp_editor( $options['textarea_two'], "plugin_options[textarea_two]", $args );				
	}		

// Sanitize and validate input. Accepts an array, return a sanitized array.
function wpet_validate_options($input) {
	// Sanitize textarea input (strip html tags, and escape characters)
	//$input['textarea_one'] = wp_filter_nohtml_kses($input['textarea_one']);
	//$input['textarea_two'] = wp_filter_nohtml_kses($input['textarea_two']);
	//$input['textarea_three'] = wp_filter_nohtml_kses($input['textarea_three']);
	return $input;
}
// Display the admin options page
function options_page_fn() {
?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>My Example Options Page</h2>
		Some optional text here explaining the overall purpose of the options and what they relate to etc.
		<form action="options.php" method="post">
					<?php
if ( function_exists('wp_nonce_field') ) 
	wp_nonce_field('plugin-name-action_' . "yep"); 
?>
		<?php settings_fields('plugin_options'); ?>
		<?php do_settings_sections(__FILE__); ?>
		<p class="submit">
			<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
		</p>
		</form>
	</div>
<?php
}

// Validate user data for some/all of your input fields
function plugin_options_validate($input) {
	// Check our textbox option field contains no HTML tags - if so strip them out
	$input['text_string'] =  wp_filter_nohtml_kses($input['text_string']);	
	return $input; // return validated input
}






/* ------------------------ */


function plugin_site_path() {
	$base_ = explode("/", WP_PLUGIN_DIR);
	$start = false;
	$items = array();
	foreach($base_ as $item) {
		if($item == "wp-content") {
			$start = true;
		}
		if($start == true) {
		$items[] = $item;
		}
	}
	$dir_ = implode("/", $items);
	if(!str_starts_with($dir_, "/")) {
		$dir_ = "/".$dir_."/".pathinfo(basename(__FILE__))["filename"]; 
	}
	return $dir_;
}


function csvmanager_load_admin_scripts(){ 
    wp_enqueue_media();
	$base_ = explode("/", WP_PLUGIN_DIR);
	$start = false;
	$items = array();
	foreach($base_ as $item) {
		if($item == "wp-content") {
			$start = true;
		}
		if($start == true) {
		$items[] = $item;
		}
	}
	
	$dir_ = implode("/", $items);
	
    wp_register_script('csvmanager_ajax',plugin_site_path().'/js/csvmanager-ajax.js', array('jquery'), '1.0.0', false);
    wp_enqueue_script('csvmanager_ajax');
	
	wp_localize_script( 'csvmanager_ajax', 'csvmanager_ajax', array(
	 'ajax_url' => admin_url( 'admin-ajax.php' ), 
	 'we_value' => 1234
	));
}
add_action( 'admin_enqueue_scripts', 'csvmanager_load_admin_scripts'); 



function menu_item()
{
  add_submenu_page("options-general.php", "Demo csvmanager_page 1", "Demo csvmanager_page", "manage_options", "csvmanager_page", "demo_page");
}
 
add_action("admin_menu", "menu_item");

// Register our settings. Add the settings section, and settings fields
function sampleoptions_init_fn(){
	register_setting('plugin_options', 'plugin_options' );
	add_settings_section('main_section', 'Main Settings', 'section_text_fn', __FILE__);
	add_settings_field('radio_buttons', 'Select Shape', 'setting_radio_fn', __FILE__, 'main_section');
}

function csvmanager_plugin_main_page() {	
	$csv_manager = new CSVManager(getcwd()."/../wp-content/plugins/csvmanager/uploads/Liste_Artikel_.csv");
	$header = $csv_manager->get_csv_header();
	?>
	<select name="csv_file" source-directory="uploads">
	<?php
	foreach($csv_manager->files_in_dir as $file_entry) {
	?>
		<option value="<?php echo $file_entry["file"]; ?>"><?php echo $file_entry["file"]; ?></option>
	<?php
	}
	?>
	</select>
	<?php
	
		?>
	<div class="wrap">
		<h2><?php _e('CSV Manager', 'csv_manager'); ?></h2>
		
		<!-- 
		<form id="csv-form" method="POST">
		<input type="checkbox" name="csvmanager_settings_cb_sort_by_unixtimestamp" id="csvmanager_settings_cb_sort_by_unixtimestamp"><br>
			<div>
			<input id="csvmanager_settings_submit" type="submit" name="csvmanager_settings-submit" class="button-primary" value="<?php // _e('Submit Form', 'csvmanager_settings'); ?>">
			</div>
		</form>
		-->
		<form method="post" action="options.php">

           <?php
		     settings_fields("plugin_options");
 
               do_settings_sections(__FILE__);
                 
               submit_button();
           ?>

        </form>
		<div id="csvmanager_results">
		</div>
	</div>
	<script>
	jQuery(document).ready(function($) {

		var form_element = $("body").find($("input#csvmanager_settings_submit"))
		$(form_element).on('click', function(event) {
		 
			event.preventDefault();
			var data = {
				"action": "my_custom_action",
				"csvmanager_settings_cb_sort_by_unixtimestamp": $("#csvmanager_settings_cb_sort_by_unixtimestamp").prop('checked')
			};
			console.log(csvmanager_ajax)
			jQuery.post(csvmanager_ajax.ajax_url, data, function(response) {
				alert(response);
			});
			return false;
		})
		
	});
	</script> 
	<?php
}


// Same handler function...
add_action( 'wp_ajax_my_custom_action', 'my_custom_action' );
function my_custom_action() {
	echo $_POST['csvmanager_settings_cb_sort_by_unixtimestamp'];
	wp_die();
}
