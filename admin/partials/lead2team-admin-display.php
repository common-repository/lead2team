<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.lead2team.com
 * @since      1.0.0
 *
 * @package    Lead2team
 * @subpackage Lead2team/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
settings_errors('l2t_alert_messages');
$lead2team_settings = get_option($this->plugin_name . '-settings');
$private_key = $api_key = $l2tFilter = '';
$profiles = $teams = $locations = array();
if (is_array($lead2team_settings) && !empty($lead2team_settings)) {
	if (!empty($lead2team_settings['private_key'])) {
		$private_key = $lead2team_settings['private_key'];
	}
	if (!empty($lead2team_settings['api-key'])) {
		$api_key = $lead2team_settings['api-key'];
	}
	if (!empty($lead2team_settings['l2tFilter'])) {
		$l2tFilter = $lead2team_settings['l2tFilter'];
	}
	if (!empty($lead2team_settings['profiles'])) {
		$profiles = $lead2team_settings['profiles'];
	}
	if (!empty($lead2team_settings['teams'])) {
		$teams = $lead2team_settings['teams'];
	}
	if (!empty($lead2team_settings['locations'])) {
		$locations = $lead2team_settings['locations'];
	}
}

?>

<div class="wrap l2t-container">
	<div class="l2t-dashboard-container l2t-setting-main">
		<form action="#" class="box_form_one" name="l2tConfigForm" id="l2tConfigForm">
			<div class="block_validation">
				<h2><?php esc_html_e('Lead2Team Widget', 'lead2team'); ?></h2>
				<div class="setting_border_sec">
					<div class="widget_text">
						<p>
							<?php echo wp_kses( __( 'Log into your Lead2Team account and navigate to the <strong>Menu > Share > Widget Web</strong>. Click the "Generate API Key" button and proceed to copy and paste the API Key Code into the following field.', 'lead2team' ), 
																		array(  
																						'strong' => array() 
																			  	)); ?>
							<?php echo wp_kses( __( ' <a href="https://www.lead2team.com/wordpress-plugin-helper/" title="Where to find API Key?" target="_blank">Instructions</a>', 'lead2team' ), 
																		array(  'a' => array(
																			        'href' => array(),
																			        'target' => array(),
																			        'title' => array())
																			  	)); ?>

																			  
						</p>
					</div>
					<div class="validation_check_sec">
						<table class="form-table" role="presentation">
							<tbody>
								<tr>
									<th scope="row"><label for="l2t-private-key"><?php echo esc_html('Private APi Key', 'lead2team'); ?></label></th>
									<td class="api_input"><input type="text" name="l2t-private-key" id="l2t-private-key" value="<?php echo esc_attr($private_key) ?>"></td>
									<td class="validate_button">
										<div class="validation_check">
											<button type="button" class="button button-primary l2t-api-button" id="l2t-api-button"><?php _e('Validate', 'lead2team'); ?></button>
											<img src="<?php echo plugin_dir_url(__DIR__) . 'images/loder.gif' ?>" class="loading_img d_none">
										</div>
									</td>
								</tr>
								<tr class="l2t_hide">
									<th scope="row"><label for="l2t-api-key"><?php echo esc_html('Widget Key', 'lead2team'); ?></label></th>
									<td class="api_input"><input type="text" name="l2t-api-key" id="l2t-api-key" value="<?php echo esc_attr($api_key) ?>" readonly disabled></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div id="error_main_sec" class="error_main_sec l2t-error-msg  d_none">
					<div class="error_sec">
						<p><?php _e("ERROR: The API key is not valid. Please check if you have copied it correctly.", 'lead2team'); ?></p>
					</div>
				</div>

			</div>
			<div class="default_configuration_sec <?php echo ($api_key ? '' : 'd_none') ?>">
				<div class="configuration_overlay d_none"><img src="<?php echo esc_url(plugin_dir_url(__DIR__) . 'images/loading.svg'); ?>"></div>
				<div class="defult_confi_form">
					<div class="form_box">
						<div class="confi_heading">
							<h2 class="head_confi"><?php _e('Default Configuration', 'lead2team'); ?></h2>
						</div>
						<div class="box_form_full">
							<div class="box_division">
								<div class="one_input">
									<input type="radio" id="l2tFilter1" name="l2tFilter" value="all" <?php esc_attr_e($l2tFilter == 'all' || $l2tFilter == '' ? 'checked' : ''); ?>>
									<label for="l2tFilter1"><?php _e('Show all profiles', 'lead2team'); ?></label>
								</div>
								<div class="one_input">
									<input type="radio" id="l2tFilter2" name="l2tFilter" value="filter" <?php esc_attr_e($l2tFilter == 'filter' ? 'checked' : ''); ?>>
									<label for="l2tFilter2"><?php _e('Filter', 'lead2team'); ?></label>
								</div>
							</div>
						</div>
						<div class="filter_box l2t_animation5">
							<p class="filter_by"><?php _e('Filter by', 'lead2team'); ?></p>
							<div class="box_filter <?php echo esc_attr($l2tFilter == 'all' ? 'blocked2' : ''); ?>">
								<ul class="select_box">
									<li>
										<div class="first_select">
											<label><?php _e('Profiles', 'lead2team'); ?></label>
											<select data-placeholder="<?php esc_attr_e('Click to select', 'lead2team'); ?>" name="profiles[]" class="chosen-select" id="api_profiles" multiple>
											</select>
										</div>
									</li>
									<li>
										<div class="first_select">
											<label><?php _e('Teams', 'lead2team'); ?></label>
											<select data-placeholder="<?php esc_attr_e('Click to select', 'lead2team'); ?>" name="teams[]" class="chosen-select" id="api_teams" multiple>
											</select>
										</div>
									</li>
									<li>
										<div class="first_select">
											<label><?php _e('Locations', 'lead2team'); ?></label>
											<select data-placeholder="<?php esc_attr_e('Click to select', 'lead2team'); ?>" name="locations[]" class="chosen-select" id="api_locations" multiple>
											</select>
										</div>
									</li>
								</ul>

								<div id="helper_invalid_filter" class="helper_invalid_filter l2t_animation5 l2t-error-msg2">
									<div class="error_sec">
										<p><?php _e("This element has been removed from your Lead2Team account. We recommend removing it and then saving your changes.", 'lead2team'); ?></p>
									</div>
								</div>

							</div>
						</div>

						<div class="save_chanage">
							<input type="hidden" name="l2t-filter-type" id="l2t-filter-type" value="global">
							<button type="button" class="ltr-settings-save"><?php _e('Save Changes', 'lead2team'); ?></button>
						</div>
					
						<div id="error_invalid_filter" class="error_invalid_filter l2t-error-msg d_none">
							<div class="error_sec">
								<p><?php _e("Caution! The filter options marked in red are no longer accessible. Please, remove them before saving your changes.", 'lead2team'); ?></p>
							</div>
						</div>

					</div>





				</div>
				<?php wp_nonce_field('l2t-ajax-nonce'); ?>
		</form>
	</div>
</div>