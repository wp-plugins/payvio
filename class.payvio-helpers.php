<?php

/// interactions with the payvio service
class PayvioHelpers {

    public static function GetSParam($price, $contentId, $priceSalt) {
        $result = sha1($price . '|' . $contentId . '|' . $priceSalt);
        return $result;
    } 
    
    public static function save_settings($payvioSettings) {
        $payvioData = new PayvioData();
        $payvioData->saveSettings($payvioSettings);
    }

    public static function clear_user_purchases(){
        $payvioData = new PayvioData();
        $payvioData->clearAllUserPurchases();
    }
    
    ///oauth2callback/payvio
    /// this method is used to process the loading of content
    /// the content may be requested in several different situations
    ///  1. non protected types like posts .. currently we only protect pages.. 
    ///  2. Loading of Paywalled content (owned and not owned)
    ///  3. Loading of Non-paywalled content
    ///  4. oath2 callback after redirect of purchase (flow callback)
    public function oauth2callback_payvio($content, $code = null, $state = null, $error = null, $content_id = null, $price = null,
        $clientId = null, $clientSecret = null, $redirectUri = null, $title, $wpUserId, $payvioUserSettings){    
        
        // I need to grab all of the posted params and validate them fully to determine if this is infact a valid oath2 callback...
        
        // Check if they have already purchased it
        if(100 == $error)
        {
            return;
        }
        
        // Get access token from previous Authorization Grant
        $payvioWebClient = new PayvioServiceClient();
        $tokenResponse = $payvioWebClient->GetAccessToken($code, $clientId, $clientSecret, $redirectUri);
        
        /************ PROCESS USER.INFO ************/
        if (strpos($state, 'user.info') !== FALSE) {
            // TODO : process user.info 
            //$infoService = new PayvioInfoService(authModel, registrationModel, consumer, tokenResponse.AccessToken);
            //$username = $infoService.ProcessRequest();
            //if (user == null)
            //    user = Membership.GetUser(username);
        }
        
        /************ PROCESS USER.CHARGE ************/
        if (strpos($state, 'user.charge') !== FALSE) {
            // When price is null, no charge or subscription is necessary or they already own it
            if (strlen($price) != 0) {
                $wpPayvioChargeService = new WpPayvioChargeService();
                $wpPayvioChargeService->ProcessChargeRequest($tokenResponse->AccessToken, $content_id, $price, $title, $redirectUri, $wpUserId, $payvioUserSettings);
            }
            
            return $content;
            //PayvioServiceClient::WpPayvioChargeServiceProcessCharge($tokenResponse->AccessToken);   
        }
        
        /************ PROCESS USER.SUBSCRIBE ************/
        if (strpos($state, 'user.subscribe') !== FALSE) {
            // TODO : process user.subscribe  
        }
    }
    
    function admin_pages_add() {
        add_menu_page( 'Payvio', 'Payvio', 'manage_options', 'payvio.php', array( 'PayvioHelpers', 'display_settings_page' ), PAYVIO_PLUGIN_IMAGES_DIR . '/payvio-favicon.ico');
        //add_menu_page( 'Payvio', 'Payvio', 'manage_options', 'payvio.php', 'payvio_mode_settings', PAYVIO_PLUGIN_IMAGES_DIR . '/payvio-favicon.ico');
        //add_submenu_page( 'payvio.php', 'Settings', 'Settings', 'manage_options', 'PayvioSettings', array( 'PayvioHelpers', 'display_settings_page' ) );
    }
     
    function admin_scripts_add( $hook ) {
        if ( preg_match( '/Payvio|payvio/', $hook ) ) {
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui' );
            wp_enqueue_script( 'payvio_admin', PAYVIO_PLUGIN_DIR . '/includes/payvio_admin.js', array(), false, false );
            wp_enqueue_style( 'payvio.css', PAYVIO_PLUGIN_DIR . '/includes/payvio.css' );
            wp_enqueue_script( 'bootstrap', PAYVIO_PLUGIN_URL . '/includes/bootstrap/js/bootstrap.js', array(), false, false );
            wp_enqueue_style( 'bootstrap.css', PAYVIO_PLUGIN_URL . '/includes/bootstrap/css/bootstrap.css' );
        }
    }
    
    public static function view( $name, array $args = array() ) {
		$args = apply_filters( 'payvio_view_arguments', $args, $name );
		
		foreach ( $args AS $key => $val ) {
			$$key = $val;
		}
		
		load_plugin_textdomain( 'payvio' );

		$file = PAYVIO_PLUGIN_DIR . '/views/'. $name . '.php';

		include( $file );
	}
    
    public static function display_settings_page(){
        PayvioHelpers::view( 'settings' );
    }
    
    function add_meta_boxes() {
        add_meta_box('payvio_page_options', '<img src="' . PAYVIO_PLUGIN_IMAGES_DIR . '/payvio-favicon.ico' . '">&nbsp;' . __('Payvio Options'), array( 'Payvio', 'payvio_page_options_display' ), 'page', 'side');
    }
    
    /** On page save save payvio page settings */
    function payvio_save_post($postID) {   
        include_once( PAYVIO_PLUGIN_DIR . '/class.payvio-data.php');	
        
        $payvioData = new PayvioData();
        
        $payvioPostSettings = $payvioData->getPostSettings($postID);
        
        if (isset($_POST['payvio']) && isset($_POST['payvio']['enabled'])) {
            $payvioPostSettings->setIsProtected(true);
        } else {
            $payvioPostSettings->setIsProtected(false);
        }
        
        if (isset($_POST['payvio']) && isset($_POST['payvio']['price'])) {
            $payvioPostSettings->setPrice($_POST[payvio][price]);
        }    

        $payvioData->savePostSettings($postID, $payvioPostSettings);     
    }
    /*END PAGE OPTIONS*/ 
  
}

?>