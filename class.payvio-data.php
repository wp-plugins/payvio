<?php

// settings in options
// settings on posts
// custom data.. (e.g. purchases)
class PayvioData {

	function getSettings() {
		$payvioSettings = new PayvioSettings(get_option(PayvioSettings::PAYVIO_SETTINGS));
		return $payvioSettings;
	}

	function saveSettings(PayvioSettings $payvioSettings) {
		wp_cache_delete(PayvioSettings::PAYVIO_SETTINGS);
		update_option(PayvioSettings::PAYVIO_SETTINGS, $payvioSettings->getSettings());
	}

    function deleteAll() {
		$payvioSettings = $this->getSettings();
		delete_option('payvio_settings');
	}
       
	function getPostSettings($postID) {
		$meta = get_post_meta($postID, 'payvio', true);
	    return new PayvioPostSettings($meta);
	}

	function savePostSettings($postID, $payvioPostSettings) {
		delete_post_meta($postID, 'payvio');
		update_post_meta($postID, 'payvio', $payvioPostSettings->getSettings(), true);
	}
    
    function getUserSettings($userId) {
		$meta = get_user_meta($userId, 'payvio', true);
	    return new PayvioUserSettings($meta);
	}

	function saveUserSettings($userId, $payvioUserSettings) {
		delete_user_meta($userId, 'payvio');
		update_user_meta($userId, 'payvio', $payvioUserSettings->getSettings(), true);
	}
    
    function clearAllUserPurchases() {
        $meta_type  = 'user';
        $user_id    = 0; // This will be ignored, since we are deleting for all users.
        $meta_key   = 'payvio';
        $meta_value = ''; // Also ignored. The meta will be deleted regardless of value.
        $delete_all = true;

        delete_metadata( $meta_type, $user_id, $meta_key, $meta_value, $delete_all );
    }
}

?>

