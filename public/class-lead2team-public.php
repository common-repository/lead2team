<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.lead2team.com
 * @since      1.0.0
 *
 * @package    Lead2team
 * @subpackage Lead2team/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Lead2team
 * @subpackage Lead2team/public
 * @author     Lead2Team <info@lead2team.com>
 */
class Lead2team_Public
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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		//wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/lead2team-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		//wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/lead2team-public.js', array('jquery'), $this->version, false);
	}

	public function lead2team_widget()
	{

		$profiles_arr = $teams_arr = $locations_arr = array();
		$profiles_str = $teams_str = $locations_str = '';
		$lead2team_widget_hide = 'no';
		$post_types = array('post', 'page');
		$allowed_post_types = apply_filters('lead2team_allowed_post_types', $post_types);

		$options = get_option($this->plugin_name . '-settings');
			$api_key = isset($options['api-key']) ? $options['api-key'] : '';
			$private_key = isset($options['private_key']) ? $options['private_key'] : '';
			$valid_key = isset($options['valid_key']) ? $options['valid_key'] : false;
			$l2tFilter = isset($options['l2tFilter']) ? $options['l2tFilter'] : 'all';
		
		$included_posts = array_filter(array_map('is_singular', $allowed_post_types));

		$chat_enable = true;

		if (!is_home() && !is_front_page() && is_array($included_posts) && !empty($included_posts)) {
			$post_id = get_the_id();
			$l2tFilter = get_post_meta($post_id, 'lead2team-filter', true);
			$lead2team_widget_hide = get_post_meta($post_id, $this->plugin_name . '-widget-hide', true);

			if ($l2tFilter == 'filter' && $lead2team_widget_hide !== 'yes') {
				$options = get_post_meta($post_id, $this->plugin_name . '-settings', true);
			}
		}

		$profiles = isset($options['profiles']) ? $options['profiles'] : array();
		$teams = isset($options['teams']) ? $options['teams'] : array();
		$locations = isset($options['locations']) ? $options['locations'] : array();

		if ($l2tFilter == 'filter') {
			if (!empty($profiles) || !empty($teams) || !empty($locations)) {
				$chat_enable = true;
			} 
		} else {
			$chat_enable = true;
		}	


		if ($lead2team_widget_hide !== 'yes') {
			if (isset($api_key)) {
				if (strlen($api_key) > 10) {//check widget_key>10chars

					if ($l2tFilter != 'all') {
						foreach (L2T_FILTER_KEYS as $filter) {
							if (is_array($options[$filter]) && !empty($options[$filter])) {
								${$filter . '_arr'} = $options[$filter];
							}
						}

						$profiles_str = implode(';', $profiles_arr);
						$teams_str = implode(';', $teams_arr);
						$locations_str = implode(';', $locations_arr);
					}

					if ($private_key && $valid_key) {
						if ($chat_enable) {

							?>
							<!-- XLEAD2TEAM -->
							<script type="text/javascript">
								(function(w, d, s, o, f, js, fjs, mpd) {
									w[o] = w[o] || function() {(w[o].q = w[o].q || []).push(arguments)};mpd = d.createElement('div');mpd.id = 'widget_' + o;d.body.appendChild(mpd);js = d.createElement(s), fjs = d.getElementById('widget_' + o);js.id = o;js.src = f;js.async = 1;fjs.parentNode.insertBefore(js, fjs);
								}(window, document, 'script', 'lead2team', 'https://online.lead2team.com/widget/widget.js'));
								lead2team('init', {apiKey: '<?php echo esc_attr($api_key) ?>'});
								lead2team('lead2team', {
									teams: '<?php echo esc_attr($teams_str); ?>',
									locations: '<?php echo esc_attr($locations_str); ?>',
									profiles: '<?php echo esc_attr($profiles_str); ?>'
								});
							</script>

						<?php 
						} //chat enable
					}  //private key & valid key
				} //api_key > 10
			}//api_key isset

		 } //lead2team_widget_hide==yes
	}
}
