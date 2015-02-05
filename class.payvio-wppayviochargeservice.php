<?php

// interacts with wordpress and PayvioServiceClient
class WpPayvioChargeService {
    
    public function __construct() {
    }
    
    public function ProcessChargeRequest($accessToken, $contentId, $price, $title, $redirectUrl, $wpUserId, $payvioUserSettings) {  
        // Process the charge against resource (Payvio Api) server
        $payvioServiceClient = new PayvioServiceClient();
        $userChargeResult = $payvioServiceClient->ProcessCharge(urlencode($accessToken), $contentId, $price, $title, $redirectUrl);
        
        // Successful charge
        if(strlen($userChargeResult->ConfirmationNumber) != 0)
        {
            // if the server payment capture succeeded then we continue on to marking it on the client 
            self::SetPurchasedInWpDatabase($contentId, $wpUserId,$payvioUserSettings);
        }          
    }   
    
    public function SetPurchasedInWpDatabase($contentId, $wpUserId, $payvioUserSettings) {
        $purchasedPostsList = $payvioUserSettings->getPurchasedPosts();
        $contentIsOwned = in_array($contentId, explode(',', $purchasedPostsList));
        if(!$contentIsOwned) {
            $purchasedPostsList .= ",";
            $purchasedPostsList .= $contentId;
        }
        $payvioUserSettings->setPurchasedPosts($purchasedPostsList);
        $payvioData = new PayvioData();
        $payvioData->saveUserSettings($wpUserId, $payvioUserSettings);
    }
}


?>