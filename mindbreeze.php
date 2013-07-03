<?php
/*
Plugin Name: Mindbreeze InSite
Plugin URI: http://www.mindbreeze.com/
Description: The Mindbreeze InSite plugin replaces the standard WordPress search by linking to your Mindbreeze InSite account.
Version: 13.2.7
Requires at least: 3.2
Author: Mindbreeze
Author URI: http://www.mindbreeze.com/
License: GPL2

Copyright 2013  Mindbreeze GmbH info@mindbreeze.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function mindbreeze_activate() {
	$mindbreeze_cfg = get_option( 'mindbreeze_cfg', false );
	if ( false === $mindbreeze_cfg ) {
		add_option( 'mindbreeze_cfg', array(), '', 'yes' );
	}
}

function mindbreeze_search_form( $form ) {
    $form = '<span id="mindbreezeSearchInput"></span>';
    return $form;
}

function mindbreeze_footer() {
	$mindbreeze_cfg = get_option( 'mindbreeze_cfg', false );
	if ( false === $mindbreeze_cfg ) {
		return;
	}
	if ( ! isset( $mindbreeze_cfg['config'] ) || ! $mindbreeze_cfg['config'] ) {
		return;
	}
	echo "<!-- Mindbreeze InSite start -->\n";
	echo html_entity_decode( $mindbreeze_cfg['config'] ) . "\n";
	echo "<!-- Mindbreeze InSite end -->\n";
}

function mindbreeze_options() {
	if ( ! current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$mindbreeze_cfg = get_option( 'mindbreeze_cfg' );
	if( isset( $_POST['mindbreeze_insite_configuration'] ) ) {
		// Read their posted value
		$config = htmlentities( trim( stripslashes( $_POST['mindbreeze_insite_configuration'] ) ) );
		$oldcfg = isset( $mindbreeze_cfg["config"] ) ? $mindbreeze_cfg["config"] : '';
		$mindbreeze_cfg["config"] = $config;
		// Save the posted value in the database
		update_option( 'mindbreeze_cfg', $mindbreeze_cfg );
		// Put an settings updated message on the screen
		if ( $oldcfg !== $config ) {
			echo '<div class="updated"><p><strong>Mindbreeze InSite Configuration saved.</strong></p></div>';
		}
	}
	if ( isset( $mindbreeze_cfg["config"] ) ) {
		$mindbreeze_config = html_entity_decode( $mindbreeze_cfg["config"] );
	}
	else {
		$mindbreeze_config = "";
	}
?>
	<div class="wrap" style="max-width:700px;">
		<div class="icon32" id="icon-options-general"><br /></div>
		<h2>Mindbreeze InSite for WordPress</h2>
		<p>Configure this WordPress site for Mindbreeze InSite. 
			Get your configuration from your demo page (step 2) and paste it in the field below.<br />
		</p>

		<form method="post" action=""> 
			<div class="metabox-holder">

				<div class="postbox" style="margin-bottom:0;">
					<h3 class="hndle"><span>Mindbreeze InSite Configuration</span></h3>
					<div class="inside">
						<table class="form-table">  
							<tr valign="top">
								<th scope="row"><label for="mindbreeze_insite_configuration">Mindbreeze InSite Snippet</label></th>
								<td><textarea name="mindbreeze_insite_configuration" id="mindbreeze_insite_configuration" rows="5" style="width:100%;"><?php echo esc_textarea( $mindbreeze_config ); ?></textarea></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div style="text-align: right;">
				<?php @submit_button(); ?>
			</div>
		</form>
	</div>
<?php
}

function mindbreeze_menu() {
	add_options_page( 'Mindbreeze InSite for WordPress', 'Mindbreeze InSite', 'manage_options', 'mindbreeze-options-page', 'mindbreeze_options' );
}

add_action( 'admin_menu', 'mindbreeze_menu' );

function mindbreeze_get_settings_link() {
	return '<a href="' . admin_url( 'options-general.php?page=mindbreeze-options-page' ) . '">';
}

// add a Settings link to the plugin entry in the Plugins page
function mindbreeze_add_action_link($links, $file) {
	if ( $file == basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) ) {
		$settings_link = mindbreeze_get_settings_link() . 'Settings</a>';
		array_unshift( $links, $settings_link );
	}
	return $links;
}

add_filter( 'plugin_action_links', 'mindbreeze_add_action_link', 10, 2 );
add_filter( 'get_search_form', 'mindbreeze_search_form' );
add_action( 'wp_footer', 'mindbreeze_footer' );
register_activation_hook( __FILE__, 'mindbreeze_activate' );
