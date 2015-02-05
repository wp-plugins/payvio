<?php 

//get/set settings	
if(!empty($_REQUEST['savesettings']))
{                   		
    $payvioSettings = new PayvioSettings();
    
    if($_REQUEST[txtClientId] != null){
        $payvioSettings->setClientId($_REQUEST[txtClientId]); 
    }
    
    if($_REQUEST[txtClientSecret] != null){
        $payvioSettings->setClientSecret($_REQUEST[txtClientSecret]); 
    }  
     
    if($_REQUEST[txtPriceSalt] != null){
        $payvioSettings->setPriceSalt($_REQUEST[txtPriceSalt]); 
    }  
    
    $payvioSettings->isEnabled = true;
   
    $payvioSettings->setAllowNewUsersToRegisterUsingPayvio(true);
    
    PayvioHelpers::save_settings($payvioSettings);
    
    if($_REQUEST[cbClearUserPurchases] != null){
        if($_REQUEST[cbClearUserPurchases] == "on"){
            PayvioHelpers::clear_user_purchases();
        }
    }
    
    //assume success
    $msg = true;
    $msg_text = __("Your advanced settings have been updated.", "payvio");	
}

$payvioData = new PayvioData();
$settingsFromDb = $payvioData->getSettings();

$clientId = '';
$clientSecret = '';

if($settingsFromDb->getClientId() != null)
{
    $clientId = $settingsFromDb->getClientId();
}

if($settingsFromDb->getClientSecret() != null)
{
    $clientSecret = $settingsFromDb->getClientSecret();
}

if($settingsFromDb->getPriceSalt() != null)
{
    $priceSalt = $settingsFromDb->getPriceSalt();
}

// todo: under class settings below find a wordpress class that works for update msg
// I tried updated fade to no avail
if(!empty($msg))
{
?>
		<div id="message" class="<?php if($msg > 0) echo ""; else echo "error"; ?>"><p><?php echo $msg_text?></p></div>
<?php
}	

?>

<div class="wrap">
    <h1>Payvio Settings</h1>
    <form action="" method="post" enctype="multipart/form-data"> 
    
        <div style="width:700px;">

            <div class="container well" style="padding-top:5px;width:665px;">
                 
                <!-- TODO: come back later and add this (allow payvio user registration) functionality -->
                <div class="row" style="display:none;width:300px;border:solid 1px black;">
                    <div class="col-xs-12">
                        <label for="cbAllowPayvioUserRegistration">Allow New Users To Register Using Payvio:</label>
                        <input name="cbAllowPayvioUserRegistration" id="cbAllowPayvioUserRegistration" type="checkbox" />
                    </div>    
                </div>

                <div class="row" style="text-align:left;margin-top:6px;padding-left:6px;width:300px;">
                     <div class="col-sm-12">
                        <label for="txtClientId">Client Id:</label>
                        <input type="text" class="form-control" id="txtClientId" name="txtClientId" style="width:610px;"
                            placeholder="Enter Client Id" value="<?php echo $clientId?>" />
                     </div>               
                </div>

                <div class="row" style="text-align:left;margin-top:6px;padding-left:6px;width:300px;">
                    <div class="col-sm-12">
                        <label for="txtClientSecret">Client Secret:</label>
                        <input type="text" class="form-control" id="txtClientSecret" name="txtClientSecret" style="width:610px;"
                            placeholder="Enter Client Secret" value="<?php echo $clientSecret?>" />
                    </div>     
                </div>

                <div class="row" style="text-align:left;margin-top:6px;padding-left:6px;width:300px;">
                    <div class="col-sm-12">
                        <label for="txtPriceSalt">Price Salt:</label>
                        <input type="text" class="form-control" id="txtPriceSalt" name="txtPriceSalt" style="width:610px;"
                            placeholder="Enter Price Salt" value="<?php echo $priceSalt?>" />
                    </div>     
                </div>

                <div class="row" style="text-align:left;margin-top:6px;padding-left:6px;width:300px;">
                    <div class="col-sm-12">
                        <label class="checkbox">
                            <input type="checkbox" id="cbClearUserPurchases" name="cbClearUserPurchases"> Clear user purchases
                        </label>
                    </div>     
                </div>

            </div>

            <div class="container well" style="padding-top: 5px;width:665px;">
                <div class="row">
                    <div class="col-xs-12">
                        Instructions:
                    </div>
                </div>
                <div class="row" style="padding-left: 5px;">
                    <div class="col-xs-12">
                        Login to your Payvio Account <a href="<?php echo(PAYVIO_ACCOUNTS_URL); ?>/s/applications">Here</a> to retreive your Payvio user information.
                    </div>
                </div>
                <div class="row" style="padding-left: 10px;">
                    <div class="col-xs-12">
                        Step 1. 
                            Add a new application in Payvio Account. Pick a descriptive name for your entity. e.g. 'Home Blog Sales'
                    </div>
                </div>
                <div class="row" style="padding-left: 10px;">
                    <div class="col-xs-12">
                        Step 2. 
                            Copy your Client Id and Client Secret from the newly created Payvio application and paste on this page.
                    </div>
                </div>
            </div>
    
            <p class="submit">
                <input name="savesettings" type="submit" class="button button-primary" value="<?php _e('Save Settings', 'payvio');?>" />
            </p> 
        </div>

	</form>
</div>

