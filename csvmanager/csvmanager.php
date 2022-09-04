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





function aad_render_admin() {
	?>
	<h1>ADMIN</h1>
	<?php
}
// create custom plugin settings menu
add_action('admin_menu', 'csv_manager_plugin_create_menu');

function csv_manager_plugin_create_menu() {
	//add_options_page(__('Admin Ajax Demo', 'aa'), __('Admin Ajax', 'aad'), 'manage_options', 'admin-ajax-demo', 'aad_render_admin');
    //create new top-level menu
    add_menu_page('CSV Manager', 'CSV Manager', 'administrator', __FILE__, 'csv_manager_plugin_main_page' , plugins_url('/images/favicon_index_32_16x16_1.png', __FILE__) );

    //call register settings function
    add_action( 'admin_init', 'register_csv_manager_plugin_settings' );
}


function register_csv_manager_plugin_settings() {
    //register our settings
    register_setting( 'csv-manager-plugin-settings-group', 'new_option_name' );
    register_setting( 'csv-manager-plugin-settings-group', 'some_other_option' );
    register_setting( 'csv-manager-plugin-settings-group', 'option_etc' );
}

function get_add_char() {
	$link_ = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$x = parse_url($link_);
	$base_ = explode("?", $link_)[0];
	//$x = parse_url($base_);
	$add_char = (array_key_exists('query', $x)) ? "&" : "?";
	return $add_char;
}

function get_link_back_url() {
	$link_ = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$x = parse_url($link_);
	$spl = explode("?", $link_);
	$base_ = $spl[0];
	$xpl_qry = explode("&", $spl[1]);
	$new_query = array();
	foreach( $xpl_qry as $qry_item ) {
		if(str_contains($qry_item, "dir=")) {
			$new_query[] = $qry_item;
		} else {
			$new_query[] = explode("/", $qry_item);
		}
	}
	return $new_query;
	// return $base_."?".implode("&", $new_query);
}

function get_link_home_url() {
	$link_ = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$x = parse_url($link_);
	$spl = explode("?", $link_);
	$base_ = $spl[0];
	$xpl_qry = explode("&", $spl[1]);
	$new_query = array();
	foreach( $xpl_qry as $qry_item ) {
		if(!str_contains($qry_item, "dir=")) {
			$new_query[] = $qry_item;
		}
	}
	
	return $base_."?".implode("&", $new_query);
}

function get_link_url($entry) {
	$link_ = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$x = parse_url($link_);
	$base_ = explode("?", $link_)[0];
	//$x = parse_url($base_);
	$add_char = (array_key_exists('query', $x)) ? "&" : "?";
	return $link_.$add_char."dir=".$entry;
}

function csv_manager_plugin_main_page() {
	
	add_options_page(
            'Settings Admin', 
            'My Settings', 
            'manage_options', 
            'my-setting-admin', 
            'aad_render_admin'
        );
	
    ?>
    <div class="wrap">
	<h1>CSV Manager</h1>
	<h4>Author: Sascha Frank @ TabTeam GbR</h4>
	<div class="csv_manager-dir_tree" style="padding: 2em; border: 1px solid #BCBCBCAF;">
	
	<?php
	if(!isset($_COOKIE['wp_csvm_directory_set'])) {
		setcookie('wp_csvm_directory_set',  false, time()+31556926);
	}
	
	$current_directory = "/";
	setcookie('wp_csvm_directory',  "/", time()+31556926);
	if(!empty($_GET["dir"])) {
		setcookie('wp_csvm_directory',  $_GET["dir"], time()+31556926);
		if(str_starts_with("/", $_GET["dir"])) {
			$current_directory = "/". $_GET["dir"];	
		} else {
			$current_directory = $_GET["dir"];
		}
		
	}
	
	
	if (is_file(getcwd()."/../wp-config.php")) {
		$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		if (mysqli_connect_errno()){
			// Connection Error
			exit("Couldn't connect to the database: ".mysqli_connect_error());
		}
		else {
			
			if(isset($_POST['upload'])) {
				
				var_dump("uploading!!!");
				if(is_array($_FILES)) 
{
	var_dump($_FILES);
	var_dump($_FILES['userImage']['tmp_name']);
}
			} else {
				?>
				<div style="width: 100%; color: #FFF; background: #000; height: 4vh; font-size: 28pt; display: table;">
					<div style="border: 1px solid #FFF; display: table-cell; vertical-align: middle; text-align: left; padding: 1em 0 1em 0.5em;">Upload Files</div>
				</div>
				<div id="upload_dragdrop_wrapper">
					
					<div id="drop-area">
						<div style="display: table-cell; vertical-align: middle; text-align: center">
						<h3 class="drop-text">Drag and Drop Files Here</h3>
						</div>
					</div>
					<!-- 
					
					http://test.tabteam.media/wp-admin/admin.php?page=csvmanager%2Fcsvmanager.php
					admin-post.php?action=upload_csv

					-->
					<form method="post" action="<?php echo admin_url( 'admin-post.php?action=upload_csv' ); ?>" id="upload_csv" class="validate" novalidate="novalidate">
					<input type="file"><br>
					<input type="submit" value="Hochladen">
					<?php
					var_dump($_POST);
					?>
					</form>
				</div>
				<script>
				(function($) {
	console.log("ready!");
	$("#drop-area").on('dragenter', function (e){
  e.preventDefault();
  $(this).css('background', '#BBD5B8');
 });

 $("#drop-area").on('dragover', function (e){
  e.preventDefault();
 });

 $("#drop-area").on('drop', function (e){
  $(this).css('background', '#D8F9D3');
  e.preventDefault();
  var image = e.originalEvent.dataTransfer.files;
  createFormData(image);
 });
 
 function createFormData(image)
{
 var formImage = new FormData();
 formImage.append('userImage', image[0]);
 uploadFormData(formImage);
}

function uploadFormData(formData) 
{
 $.ajax({
 url: "<?php echo admin_url( 'admin-post.php?action=upload_csv' ); ?>",
 type: "POST",
 data: formData,
 contentType:false,
 cache: false,
 processData: false,
 success: function(data){
  $('#drop-area').html(data);
 }});
}
 
})( jQuery );
				</script>
				
				<?php
			}
			
			
			if ($handle = opendir(getcwd()."/../".$current_directory)) {
				$dirs = array();
				$files = array();
				while (false !== ($entry = readdir($handle))) {
					if ($entry != "." && $entry != "..") {
						if(is_dir(getcwd()."/../".$current_directory."/".$entry)) {
							$dirs[] = $entry;
						}
						if(is_file(getcwd()."/../".$current_directory."/".$entry)) {
							$files[] = $entry;
						}
					}
				}
				
				?>
				<h3>Current Directory: <?php echo ((array_key_exists("dir", $_GET)) ? ((!str_starts_with("/", $_GET["dir"])) ? "/". $_GET["dir"] : $_GET["dir"]) : "/"); ?></h3>
				<?php
				
				
				
				
				
				if ($current_directory != "/") {
					?>
					<div style="margin: .5em; padding: 1em; border: 1px solid #ECECEC;">
						<a href="<?php
						echo get_link_home_url();
						var_dump(get_link_back_url());
						?>">&laquo; Home</a>
					</div>
					<?php
				}
				?>
				
				<div style="display: table; width: 40%">
				<?
					sort($dirs);
					foreach($dirs as $d) {
						?>
						<div style="display: table-row">
							<div style="display: table-cell; width: 2%; text-align: center; vertical-align: middle">
								<img src="/wp-content/plugins/csvmanager/images/Folder-Generic-Green-icon.png" style="heigth: auto; width: 16px;">
							</div>
						
							<div style="display: table-cell; width: auto; text-align: left; vertical-align: middle">
								<a href="<?php echo ((strlen($current_directory) > 1) ? get_link_url($current_directory)."/".$d : get_link_url($d)); ?>"><?php echo $d; ?></a>
							</div>
						</div>
					<?
					} 
					sort($files);
					foreach($files as $f) {
						?>
						<div style="display: table-row">
							<div style="display: table-cell; width: 2%; text-align: center; vertical-align: middle">
								<?
								echo "<img src=\"/wp-content/plugins/csvmanager/images/Document-Blank-icon.png\" style=\"heigth: auto; width: 16px;\">";
								?>
							</div>
							
							<div style="display: table-cell; width: auto; text-align: left; vertical-align: middle">
								<?php
								if (str_starts_with($f, ".")) {
									echo "<span class=\"hidden_file\">$f</span>";
								} else{
								echo "$f";
								}
								?>
							</div>
						</div>
					<?
					}
				?>
				</div>
				<?
				closedir($handle);
				
				
				
				
			}
		}
	} else {
		var_dump("no file");
	}
	?>
	</div>
	</div>
	<?php
	
	
}

/**
 * Admin-Backend Plugin CSS
 */
function wpdocs_enqueue_custom_admin_style() {
        wp_register_style( 'custom_wp_admin_css', plugin_dir_url( __FILE__ ) . 'css/style.css', false, '1.0.0' );
        wp_enqueue_style( 'custom_wp_admin_css' ); 
}
add_action( 'admin_enqueue_scripts', 'wpdocs_enqueue_custom_admin_style' ); 
