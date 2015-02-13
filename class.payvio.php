<?php

/// Any Wordpress Plugin point for the Payvio Wordpress plugin
/// (e.g. actions, filters) point to methods defined in this class
class Payvio {
    
	private static $initiated = false;
	
    /// Wordpress action : init 
    /// Called on every postback to init the plugin
    public static function init() {

        if ( !self::$initiated ) { 
            $payvioData = new PayvioData();
            $payvioSettings = $payvioData->getSettings();
            add_action('add_meta_boxes', array( 'PayvioHelpers', 'add_meta_boxes' ));
            add_action('save_post', array( 'PayvioHelpers', 'payvio_save_post' ));
            
            if ( $payvioSettings->isEnabled() && !is_admin() ) {
                wp_enqueue_script('jquery');
                wp_enqueue_script('pvo.js', PAYVIO_PLUGIN_PVO_JS);
                wp_enqueue_script( 'bootstrap', PAYVIO_PLUGIN_URL . '/includes/bootstrap/js/bootstrap.js', array(), false, false );
                wp_enqueue_style( 'bootstrap.css', PAYVIO_PLUGIN_URL . '/includes/bootstrap/css/bootstrap.css' );
                add_filter('the_content', array( 'Payvio', 'intercept_content' ), 5);
                add_filter('the_title', array( 'Payvio', 'render_title' ), 5);
            }  
        }
          
        self::$initiated = true;        
	}
    
    /// Wordpress action : init 
    /// Called on every postback (Where is_admin()) to init the plugin
    public static function init_admin(){ 
        add_action( "admin_menu", array( 'PayvioHelpers', 'admin_pages_add' ) );
        add_action( 'admin_enqueue_scripts', array( 'PayvioHelpers', 'admin_scripts_add' ) );
    }
    
    /// Wordpress action : wp_footer
    /// Called on render of footer for each post on non admin pages
    public static function footer() {
        echo "
                <script type=\"text/javascript\">
                <!--payvio footer -->
                </script>
                /n";
    }
 
    public static function activate() {
    }

    public static function deactivate() {
    }

    public static function uninstall() { 
        //clear up options
        //delete_option( 'some_option' );
    }
    
    function render_title($title) {
        $post =  get_page_by_title( $title );
        $payvioData = new PayvioData();
        
        if($post->post_type == "post")    
            return '';
        
        $payvioPostSettings = $payvioData->getPostSettings($post->ID);
        $is_content_protected = $payvioPostSettings->getIsProtected();
        
        if($is_content_protected) {
            return  $title . ' $$';
        }
        
        return $title;
    }
    
    public function intercept_content($content) {  
        global $wpdb;
        global $post;
        global $current_user;
        get_currentuserinfo();
        $payvioData = new PayvioData();
        
        // 1. SKIP CASES: Content Other Than Pages... (e.g. Post...)
        if($post->post_type == "post")    
            return $content;
        
        $payvioPostSettings = $payvioData->getPostSettings($post->ID);
        $is_content_protected = $payvioPostSettings->getIsProtected();
        
        // 2. Content is protected and the a user is NOT logged in
        if($current_user->ID == 0 && $is_content_protected)
            return "You must log in to view protected content!";
        
        $payvioUserSettings = $payvioData->getUserSettings($current_user->ID); 
        $contentIsOwned = in_array($post->ID, explode(',', $payvioUserSettings->getPurchasedPosts()));
        
        // 2. OAUTH CALLBACK
        if($_REQUEST[code] != "") {
            $payvioSettings = $payvioData->getSettings();
            $clientSecret = $payvioSettings->getClientSecret();
            $clientId = $payvioSettings->getClientId();
            $redirectUri = urlencode(WORDPRESS_BASE_URL . "/?page_id=" . $post->ID);
            
            PayvioHelpers::oauth2callback_payvio($content, $_REQUEST[code], $_REQUEST[state], $_REQUEST[error], 
                $_REQUEST[content_id], $_REQUEST[price], $clientId, $clientSecret, $redirectUri, $post->post_title, $current_user->ID, $payvioUserSettings);
            
            return $content;
            //  return  "OAUTH CALLBACK PROCESSED";
        }
        
        /*Test pop up*/
        // not an OAUTH callback because of an error ..
        if($_REQUEST[state] == "user.charge" && $_REQUEST[error_reason] == 'Content already purchased') {
            // if already purchased and wp doesn't think so then fix it..
            $wpPayvioChargeService = new WpPayvioChargeService();
            $wpPayvioChargeService->SetPurchasedInWpDatabase($post->ID, $current_user->ID, $payvioUserSettings);
            
            return $content; // . '<div class="section"><div class="row"><div class="col-xs-4">Enjoy your content!</b></div><div class="col-xs-4">You either own this or it is free!</b></div></div></div>';
        }
        else if($contentIsOwned || !$payvioPostSettings->getIsProtected()){    
            return $content; // . '<div class="section"><div class="row"><div class="col-xs-4">Enjoy your content!</b></div><div class="col-xs-4">You either own this or it is free!</b></div></div></div>';
        }
        else{
            $payvioSettings = $payvioData->getSettings();
            $clientSecret = $payvioSettings->getClientSecret();
            $priceSalt = $payvioSettings->getPriceSalt(); 
            $clientId = $payvioSettings->getClientId();
            $contentId = $post->ID;
            $price = $payvioPostSettings->getPrice();   
            $price *= 100.0;
            $s = PayvioHelpers::GetSParam($price, $contentId, $priceSalt);
            
            $postDetails = array(
                "clientId" => $clientId,
                "contentId" => $post->ID,
                "title" => $post->post_title,
                "postType" => $post->post_type,
                "price" => $price,
                "s" => $s,
                "encryptedPrice" => 'check obsolete',   
                "description" => $post->post_title,
                "applicationId" => 'check obsolete',
                "payvioOAuthAuthUrl" => "peanuts"
            );
            
            PayvioHelpers::view( 'paywall', $postDetails);
            return "Content is Protected! Create a Payvio Account And Enjoy Some A-la-carte content!";
        }
    }   
    
    /**
     * BEGIN PAGE OPTIONS
     */
    public static function payvio_page_options_display($post) {
        include_once( PAYVIO_PLUGIN_DIR . '/class.payvio-data.php');	
        $payvioData = new PayvioData();
        $payvioPostSettings = $payvioData->getPostSettings($post->ID);
        
        $price = $payvioPostSettings->getPrice();
        if($price == '0')
            $price = '';
        
        ?>
	    <?php wp_nonce_field('payvio-post-save-nonce', 'payvio-post-save-nonce'); ?>
	 
        <script>
            jQuery(document).ready(function () {
                jQuery("#payvioPageProtected").click(function () {
                    if (jQuery("#payvioPageProtected").is(":checked")) {
                        jQuery("#payvioPagePrice")
                            .removeAttr("disabled")
                            .css("background-color", "white");
                    }
                    else {
                        jQuery("#payvioPagePrice")
                            .attr("disabled", "disabled")
                            .css("background-color", "#eee");
                    }
                });
            });
        </script>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <label for="payvioPageProtected">Protect this page:</label>
                    <input name="payvio[enabled]"  id="payvioPageProtected" type="checkbox" <?php checked($payvioPostSettings != '' && $payvioPostSettings->getIsProtected()) ?>/>
                </div>    
                <div class="row" style="text-align:left;">
                    <div class="col-xs-12;">
                    <label for="payvioPagePrice">Price: $</label>
                    <input id="payvioPagePrice" name="payvio[price]" type="text" class="form-control" style="width:87px;"
                        placeholder="Enter Price" value="<?php echo($price); ?>">
                    </div>               
                </div>
            </div>
        </div>
        <?php   
    }
}

?>