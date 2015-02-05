<?php

class PayvioSettings {

    // USER_ID is actually the CLIENT_ID for the application client id and needs to be changed..
    
    const CLIENT_ID = 'client_id';
    const CLIENT_SECRET = 'client_secret';
    const PRICE_SALT = 'price_salt';
    const ALLOW_NEW_USERS_TO_REGISTER_USING_PAYVIO = 'allow_new_users_to_register_using_payvio';
	const ENABLED = true;
	const PAYVIO_SETTINGS = 'payvio_settings';
    
    private $settings;
    
	public function __construct($settings = null) {
        
        if($settings == null){
            $this->settings = array(
                        PayvioSettings::CLIENT_SECRET => '',
                        PayvioSettings::CLIENT_ID => '',
                        PayvioSettings::PRICE_SALT => '',
                        PayvioSettings::ALLOW_NEW_USERS_TO_REGISTER_USING_PAYVIO => false,
                );
        }
        else{
            $this->settings = $settings;
        }  
        
	}
    
    public function getSettings()
    {
        return $this->settings;
    }
    
	public function isEnabled() {
		return self::ENABLED;
	}

	public function setEnabled($enabled = 1) {
		$this->settings[self::ENABLED] = $enabled;
	}

	public function getClientSecret() {
        return $this->settings['client_secret'];
	}

    public function setClientSecret($client_secret) {
		$this->settings[self::CLIENT_SECRET] = $client_secret;
	}
    
    public function getPriceSalt() {
        return $this->settings['price_salt'];
	}

    public function setPriceSalt($price_salt) {
		$this->settings[self::PRICE_SALT] = $price_salt;
	}
    
    public function getClientId() {
        return $this->settings['client_id'];
	}

    public function setClientId($client_id) {
		$this->settings[self::CLIENT_ID] = $client_id;
	}
    
    public function getAllowNewUsersToRegisterUsingPayvio() {
		return $this->settings->val(self::ALLOW_NEW_USERS_TO_REGISTER_USING_PAYVIO, 'ALLOW_NEW_USERS_TO_REGISTER_USING_PAYVIO');
	}

    public function setAllowNewUsersToRegisterUsingPayvio($allow_new_users_to_register_using_payvio) {
		$this->settings[self::ALLOW_NEW_USERS_TO_REGISTER_USING_PAYVIO] = $allow_new_users_to_register_using_payvio;
	}
     
    public function isValEnabled($field) {

		if ($this->val($field) == null)
			return false;
		$val = $this->val($field);

		if (is_string($val)) {
			$val = strtolower($val);
			if ($val == "true" || $val == "on")
				return true;
			if (is_numeric($val) && intval($val) > 0)
				return true;
		}else if (is_numeric($val)) {
			return $val > 0;
		} else {
			return ($val == true);
		}
		return false;
	}   
}

class PayvioPostSettings {
    
    const ISPROTECTED = 'is_protected';
    const PRICE = 'price';
    
    private $settings;
      
    public function __construct($data = null) {
		if ($data == null || $data == '') {
            $this->setIsProtected(false);
            $this->setPrice(0);
        }
        else{
            $this->settings[self::ISPROTECTED] =  $data[self::ISPROTECTED];
            $this->settings[self::PRICE] =  $data[self::PRICE];
        }
	}
    
    public function getSettings()
    {
        return $this->settings;
    }
    
    public function getIsProtected() {
        return $this->settings[is_protected];
	}

    public function setIsProtected($is_protected) {
		$this->settings[self::ISPROTECTED] = $is_protected;
	}
    
    public function getPrice() {
        return $this->settings[price];
	}

    public function setPrice($price) {
		$this->settings[self::PRICE] = $price;
	}
}

class PayvioUserSettings {
    const PURCHASED_POSTS = 'purchased_posts';
    
    private $settings;
    
    public function __construct($data = null) {
		if ($data == null || $data == '') {
            $this->setPurchasedPosts('');
        }
        else{
            $this->settings[self::PURCHASED_POSTS] =  $data[self::PURCHASED_POSTS];
        }
	}
    
    public function getSettings()
    {
        return $this->settings;
    }
    
    public function getPurchasedPosts() {
        return $this->settings[self::PURCHASED_POSTS];
	}

    // pass in comma separated list of post ids representing the list of purchased posts for this user
    public function setPurchasedPosts($purchasedPostsList) {
		$this->settings[self::PURCHASED_POSTS] = $purchasedPostsList;
	}
}

?>