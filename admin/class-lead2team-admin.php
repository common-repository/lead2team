<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.lead2team.com
 * @since      1.0.0
 *
 * @package    Lead2team
 * @subpackage Lead2team/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Lead2team
 * @subpackage Lead2team/admin
 * @author     Lead2Team <info@lead2team.com>
 */

require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/lead2team-admin-post-metabox.php';

class Lead2team_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action('wp_ajax_l2t_get_api_key', array($this, 'l2t_get_api_key_ajax'));
		add_action('wp_ajax_l2t_get_configuration', array($this, 'l2t_get_configuration_ajax'));
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Lead2team_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Lead2team_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style('chosen_styles', plugin_dir_url(__FILE__) . 'css/chosen.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/lead2team-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Lead2team_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Lead2team_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$ajax_nonce = wp_create_nonce("l2t-ajax-nonce");
		wp_enqueue_script('chosen_js', plugin_dir_url(__FILE__) . 'js/chosen.min.js', array('jquery'), $this->version, true);
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/lead2team-admin.js', array('jquery', 'chosen_js'), $this->version, true);
		wp_localize_script($this->plugin_name, 'l2t_admin_ajax_object', array('ajax_url' => admin_url('admin-ajax.php'), 'ajax_nonce' => $ajax_nonce, 'all_filter_labels' => L2T_FILTER_KEYS));
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since 1.0.0
	 */
	public function register_settings_page()
	{
		add_submenu_page(
			'tools.php',
			"Lead2team Widget",
			"Lead2team",
			'manage_options',
			'lead2team-dashboard',
			array($this, 'display_settings_page')
		);
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since 1.0.0
	 */
	public function add_action_links($links)
	{
		$settings_link = array(
			'<a href="' . admin_url('admin.php?page=' . $this->plugin_name . '-dashboard') . '">' . esc_html(__('Settings', $this->plugin_name)) . '</a>',
		);
		return array_merge($settings_link, $links);
	}



	public function display_settings_page()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/lead2team-admin-display.php';
	}

	function lead2team_options_validate($input)
	{

		$valid = array();

		if ((strlen($input['api-key']) < 15) || (strlen($input['api-key']) > 40)) {
			add_settings_error('l2t_alert_messages', 'l2t_alert_message', esc_html(__('Wrong Widget Key.', 'lead2team')), 'error');
			$valid['api-key'] = sanitize_text_field('');
		} else {
			//check string contains just letters a-z A-Z and numbers 0-9
			if (preg_match("/^[a-zA-Z0-9]+$/", $input['api-key'])) {
				add_settings_error('l2t_alert_messages', 'l2t_alert_message', esc_html(__('Widget Key successfully saved.', 'lead2team')), 'success');
				$valid['api-key'] = sanitize_text_field($input['api-key']);
			} else {
				add_settings_error('l2t_alert_messages', 'l2t_alert_message', esc_html(__('Wrong Widget Key.', 'lead2team')), 'error');
				$valid['api-key'] = sanitize_text_field('');
			}
		}

		return $valid;
	}

	/**
	 * Lead2teram our section for the settings.
	 *
	 * @since    1.0.0
	 */
	public function lead2team_add_settings_section()
	{
		return;
	}


	function register_settings()
	{
		// Here we are going to register our setting.
		register_setting(
			$this->plugin_name . '-settings',
			$this->plugin_name . '-settings',
			array($this, 'lead2team_options_validate')
		);

		// Here we are going to add a section for our setting.
		add_settings_section(
			$this->plugin_name . '-settings-section',
			'',
			array($this, 'lead2team_add_settings_section'),
			$this->plugin_name . '-settings'
		);
	}

	public function l2t_get_api_key_ajax()
	{
		$form_data = $_POST['form_data'];
		$valid_nonce = false;
		$private_key = $api_key = $html = $post_id = '';
		$l2tFilter = 'all';
		$profiles = $teams = $locations = $profiles_filter = $teams_filter = $locations_filter = $selected_filters = $invalid_filters = array();
		$status = 'error';

		foreach ($form_data as $input_key => $input_val) {
			if ($input_val['name'] == '_wpnonce' && wp_verify_nonce($input_val['value'], 'l2t-ajax-nonce')) {
				$valid_nonce = true;
			}
			if ($input_val['name'] == 'l2t-private-key' && !empty($input_val['value'])) {
				$private_key = $input_val['value'];
			}
			if ($input_val['name'] == 'l2tFilter' && !empty($input_val['value'])) {
				$l2tFilter = $input_val['value'];
			}
			if ($input_val['name'] == 'profiles[]' && !empty($input_val['value'])) {
				$profiles[] = $input_val['value'];
			}
			if ($input_val['name'] == 'teams[]' && !empty($input_val['value'])) {
				$teams[] = $input_val['value'];
			}
			if ($input_val['name'] == 'locations[]' && !empty($input_val['value'])) {
				$locations[] = $input_val['value'];
			}
			if ($input_val['name'] == 'l2t_post_id' && !empty($input_val['value'])) {
				$post_id[] = $input_val['value'];
			}
		}

		if (!$valid_nonce) {
			die('Busted!');
		}

		if (empty($lead2team_settings) || !is_array($lead2team_settings)) {
			$lead2team_settings = array();
		}
		$lead2team_settings['valid_key']   = false;
		$lead2team_settings['private_key'] = $private_key;
		$lead2team_settings['l2tFilter']   = $l2tFilter;
		$lead2team_settings['profiles']    = $profiles;
		$lead2team_settings['teams']       = $teams;
		$lead2team_settings['locations']   = $locations;
		$lead2team_settings['api-key']     = '';

		if (!empty($private_key)) {
			$action = 'getWidgetID';
			$response_json = $this->lead2team_get_api_data($action, $private_key);
			if ($response_json) {
				$response = json_decode($response_json, true);
				if ($response['status'] == 'success') {
					$response_body = json_decode($response['api_res'], true);
					if ($response_body['status'] == 'success') {
						$api_key = $response_body['key'];
						$lead2team_settings['api-key']     = $api_key;
						$lead2team_settings['valid_key']   = true;
						update_option('lead2team-settings', $lead2team_settings);
						$all_select_html = json_encode($this->lead2team_select_html($private_key, $post_id));
						$select_html_arr = json_decode($all_select_html, true);
						$selected_filters = $select_html_arr['selected_filters'];
						$invalid_filters = count(array_filter(array_map('current', $select_html_arr['invalid_filters'])));
						$all_filters = $select_html_arr['all_filters'];

						if (!empty($selected_filters)) {
							foreach ($selected_filters as $value) {
								foreach ($value as $filter_key => $filter_val) {
									if ($filter_key == 'profiles') {
										$profiles_filter = $filter_val;
									}
									if ($filter_key == 'teams') {
										$teams_filter = $filter_val;
									}
									if ($filter_key == 'locations') {
										$locations_filter = $filter_val;
									}
								}
							}
						}

						$status = 'success';
						$code = 200;
						$message = 'API key is valid';
						$html = json_encode($select_html_arr['html']);
					} else {
						$code = 201;
						$message = $response_body['message'];
					}
				} else {
					$code = 202;
					$message = 'Something went wrong';
				}
			} else {
				$code = 203;
				$message = 'Error in API response';
			}
		} else {
			$code = 204;
			$message = 'Please enter private API key';
		}

		update_option('lead2team-settings', $lead2team_settings);
		update_option('lead2team-all-filters', $all_filters);



		echo json_encode(array('status' => $status, 'message' => $message, 'api_key' => $api_key, 'html' => $html, 'invalid_filters' => $invalid_filters, 'code' => $code));
		wp_die();
	}

	public function l2t_get_configuration_ajax()
	{

		if (!wp_verify_nonce($_POST['nonce'], 'l2t-ajax-nonce')) {
			die('Busted!');
		}

		$all_select_html = $only_select_arr = array();
		$invalid_filters = 0;
		$post_id = $_POST['post_id'];
		$l2tFilter = $_POST['l2tFilter'];

		if (isset($_POST['private_api']) && !empty($_POST['private_api'])) {
			$all_select_html = json_encode($this->lead2team_select_html($_POST['private_api'], $post_id));
			$only_select_arr = json_decode($all_select_html, true);
			$invalid_filters = ($l2tFilter == 'filter' ? count(array_filter(array_map('current', $only_select_arr['invalid_filters']))) : 0);
		}

		echo json_encode(array('status' => 'success', 'html' => json_encode($only_select_arr['html']), 'invalid_filters' => $invalid_filters, 'code' => 200));
		wp_die();
	}

	public function lead2team_select_html($private_key, $post_id)
	{
		$l2t_select_html = $selected_filters = $all_filters = $invalid_filters = array();

		foreach (L2T_FILTER_KEYS as $filter_key) {
			$action = 'get' . ucfirst($filter_key);
			$api_response = json_decode($this->lead2team_get_api_data($action, $private_key), true);
			$api_result = $this->lead2team_api_response_check($api_response, $filter_key, $post_id);
			$selected_filters[] = $api_result['selected_filters'];
			$invalid_filters[] = $api_result['invalid_filters'];
			$all_filters[$filter_key] = $api_result['all_filters'];
			if ($api_result['status'] == 'success') {
				$l2t_select_html[$filter_key . '_select'] = $api_result['html'];
			} else {
				$l2t_select_html[$filter_key . '_select'] = '';
			}
		}

		return array('html' => $l2t_select_html, 'selected_filters' => $selected_filters, 'invalid_filters' => $invalid_filters, 'all_filters' => $all_filters);
	}

	public function lead2team_api_response_check($response, $filter_key, $post_id)
	{
		$res_arr = $selected_filters = $all_filters = $invalid_filters = array();
		$status = 'error';
		$message = $html = '';
		if ($response['status'] == 'success') {
			$response_body = json_decode($response['api_res'], true);
			if ($response_body['status'] == 'success') {
				$res_arr[$filter_key] = $response_body['data'];

				$res_json_output = $this->lead2team_select_options($res_arr[$filter_key], $post_id, $filter_key);

				$res_arr_output = json_decode($res_json_output, true);
				$html = $res_arr_output['html'];
				$selected_filters = $res_arr_output['selected_filters'];
				$invalid_filters = $res_arr_output['invalid_filters'];
				$all_filters = $res_arr_output['all_filters'];

				$status = 'success';
				$message = 'Data found';
			} else {
				$html = $response_body;
				$message = 'Something went wrong';
			}
		} else {
			$html = $response;
			$message = 'No result found';
		}

		return array('html' => $html, 'selected_filters' => $selected_filters, 'invalid_filters' => $invalid_filters, 'all_filters' => $all_filters, 'status' => $status, 'message' => $message);
	}

	public function lead2team_select_options($data_arr, $post_id, $field_key)
	{
		$html = '';
		${'invalid_' . $field_key} = array();
		$selected_filters = $all_filters = $invalid_filters = array();

		if ($post_id == '') {
			$lead2team_settings = get_option('lead2team-settings');
		} else {
			$lead2team_settings = get_post_meta($post_id, 'lead2team-settings', true) ? get_post_meta($post_id, 'lead2team-settings', true) : array();
		}
		/**
		 * 
		 * LOOK FOR INVALID ID's
		 * 
		 * 
		 * */
    $removeKeyword = '$$';
		if (!empty($lead2team_settings) && isset($lead2team_settings['l2tFilter']) && $lead2team_settings['l2tFilter'] == 'filter') {

			foreach ($lead2team_settings[$field_key] as $filter_key => $filter_val) {
				$key = array_search($filter_val, array_column($data_arr, 'id'));
				if ($key === false) {
					$new_arr['id'] = $filter_val.$removeKeyword;
					$new_arr['name'] = "ID: ".$filter_val." (removed)";
					$new_arr['invalid'] = true;
					array_push(${'invalid_' . $field_key}, $new_arr);
				}
			}
		}



		$invalid_filters[$field_key] = ${'invalid_' . $field_key};
		$new_data_arr = array_merge($data_arr, ${'invalid_' . $field_key});

		if (is_array($new_data_arr) && !empty($new_data_arr)) {
			$check = 0;
			foreach ($new_data_arr as $key => $value) {
				$selected = $option_class = '';
				if (!empty($lead2team_settings) && is_array($lead2team_settings)) {
					if (isset($value['invalid'])) {
						$option_class = 'l2t_invalid_filter';
						$selected = 'selected';
						//$selected_filters[$field_key][$value['id']] = $value['name'];
					}
					if (in_array($value['id'], $lead2team_settings[$field_key])) {
						$selected = 'selected';
						$selected_filters[$field_key][$value['id']] = $value['name'];
					}
				}

				$html .= '<option class="' . $option_class . '" value="' . $value["id"] . '" ' . $selected . '>' . $value["name"] . '</option>';



				$check++;
				$all_filters[$value['id']] = $value['name'];
			}
		}

		return json_encode(array('html' => $html, 'selected_filters' => $selected_filters, 'invalid_filters' => $invalid_filters, 'all_filters' => $all_filters));
	}

	public function lead2team_get_api_data($action, $private_key)
	{
		$url = LEAD_2_TEAM_API_URL . $action;
		$headers = array(
			'Content-Type' => 'application/json',
			'X-API-KEY'    => $private_key
		);
		$response = wp_remote_post(
			$url,
			array(
				'method'      => 'POST',
				'headers'     => $headers,
				'timeout'     => 45,
				'body'        => array(),
				'cookies'     => array(),
			)
		);

		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			return json_encode(array('status' => 'error',   'code' => 400, 'api_res' => $error_message));
		} else {
			if (is_array($response) && !empty($response['body'])) {
				return json_encode(array('status' => 'success', 'code' => 200, 'api_res' => $response['body']));
			} else {
				return json_encode(array('status' => 'success', 'code' => 201, 'api_res' => $response));
			}
		}
	}

}
