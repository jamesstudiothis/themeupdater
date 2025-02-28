<?php

function custom_theme_check_for_update($transient) {
	if (empty($transient->checked)) {
		return $transient;
	}

	$response = wp_remote_get(get_template_directory_uri() . '/theme-update.json');
	if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
		$theme_data = json_decode(wp_remote_retrieve_body($response), true);
		$current_theme = wp_get_theme();

		if (version_compare($current_theme->get('Version'), $theme_data['new_version'], '<')) {
			$transient->response[$current_theme->get_stylesheet()] = (object) [
				'theme'       => $theme_data['theme'],
				'new_version' => $theme_data['new_version'],
				'url'         => $theme_data['url'],
				'package'     => $theme_data['package']
			];
		}
	}
	return $transient;
}
add_filter('pre_set_site_transient_update_themes', 'custom_theme_check_for_update');
