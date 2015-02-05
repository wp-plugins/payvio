<?php
require_once( PAYVIO_PLUGIN_DIR . '/includes/web_client.php');
require_once( PAYVIO_PLUGIN_DIR . '/class.payvio-wppayviochargeservice.php');
require_once( PAYVIO_PLUGIN_DIR . '/class.payvio-dto.php');

/// interactions with the payvio service
// note GetAccessToken calls "accounts_api" while ProcessCharge,Info and Subscribe call the "resources_api"
class PayvioServiceClient extends WebClient {
    
    public function __construct($settings = null) {
	}
     
    public function ProcessGetInfo() {
    }
    
    public function ProcessCharge($accessToken, $contentId, $price,  $title, $redirectUrl) {
           
        if(strlen($contentId) == 0)
            throw new Exception('Unable to process user.charge.  Content Id missing');    
        
        if(strlen($accessToken) == 0)
            throw new Exception('Unable to process user.charge.  AccessToken missing');    
        
        $method = 'POST';
        $data = '';
        
        $url = Format(PAYVIO_API_CHARGE_URL, $accessToken);
        
        $data = Format("price=[0]&", $price);
        $data .= Format("content_id=[0]&", $contentId);  
        $data .= "content_type=wppage&";
        $data .= Format("title=[0]&", $title);  
        $data .= Format("permanent_link=[0]&", $redirectUrl);
        
        $chargeResult = $this->CallAPI($method, $url, $data);   
        
        $result = json_decode($chargeResult);
        
        $charge_response = new UserChargeResponse();
        
        $charge_response->ConfirmationNumber = $result->confirmation_number;
        
        return $charge_response;
    }
    
    public function ProcessSubscribe() {
    }
    
    public function GetAccessToken($code, $clientId, $clientSecret, $redirectUri) {
        if(strlen($code) == 0)
            throw new Exception('Unable to get access token from Payvio.  Code missing');    
        
        $method = 'POST';
        $data = '';
        
        $url = PAYVIO_OAUTH_TOKEN_URL;
        //$url .= '/';
        
        $data = Format("code=[0]&", urlencode($code));
        $data .= Format("client_id=[0]&", $clientId);
        $data .= Format("client_secret=[0]&", $clientSecret);
        $data .= Format("redirect_uri=[0]&", $redirectUri);
        $data .= Format("grant_type=authorization_code");

        $getAccessTokenResult = $this->CallAPI($method, $url, $data);   
        
        $result = json_decode($getAccessTokenResult);
        
        $token_response = new AccessTokenResponse();
      
        $token_response->AccessToken = urldecode($result->access_token);
        $token_response->ExpiresIn = $result->expires_in;
        $token_response->RefreshToken = $result->refresh_token;
        $token_response->TokenType = $result->token_type;

        return $token_response;
    }   
}


?>