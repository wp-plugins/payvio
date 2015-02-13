var pvo = {
    /// by default custom paywall is not enabled
    /// enabled this by setting jQuery('#enableCustomPaywall').val('true') on page
    /// this sets redirects links to custom paywall page as opposed to going directly into the Payvio flow.
    enableCustomPaywall: false,
    /// by default use price tags. If this option is set to false then the element with the pv-link is used directly
    usePriceTag: true,
    clientId: 'notset',


    init: function () {

        if (!(typeof enableCustomPaywall === "undefined")) {
            pvo.enableCustomPaywall = pvUseCustomPaywall;
        }
        else {
            pvo.enableCustomPaywall = false;
        }

        jQuery('.pv-link').each(function (index) {
            pvo.processPvoElement(this);
        });
    },
    processPvoElement: function (item) {
        var pvItemType = pvo.getItemType(pvSubscriptionId, pvContentId);
        var pvContentType = pvo.getDataItem(item, "pvContentType");
        var pvSubscriptionId = pvo.getDataItem(item, "pvSubscriptionId");
        var pvContentId = pvo.getDataItem(item, "pvContentId");
        var pvPrice = pvo.getDataItem(item, "pvPrice");
        var pvS = pvo.getDataItem(item, "pvS");
        var pvDescription = pvo.getDataItem(item, "pvDescription");
        var pvRedirectUri = pvo.getDataItem(item, "pvRedirectUri");
        var pvScope = pvo.getDataItem(item, "pvScope");
        var pvApplicationId = pvo.getDataItem(item, "pvApplicationId");
        var pvRenderDot = pvo.getDataItem(item, "pvRenderDot");
        var pvContentOwnership = pvo.getDataItem(item, "pvContentOwnership");
        var pvPayvioOAuthAuthUrl = pvo.getDataItem(item, "payvioOAuthAuthUrl");
        var currentHref = jQuery(item)[0].href;
        // todo : pull payvioClientId and payvioOAuthAuthUrl from config???
        // put another data elem on the ..
        var payvioClientId = pvo.getDataItem(item, "pvClientId");
        var payvioOAuthAuthUrl = pvPayvioOAuthAuthUrl == '' ? 'https://accounts.dev.payvio.com/oauth2/auth' : pvPayvioOAuthAuthUrl;
        var dotDiv = '';

        switch (pvItemType) {
            case 'subscription':
                pvo.processPvoSubscriptionElement(item, pvSubscriptionId, pvContentId, pvPrice, pvS, pvDescription, pvRenderDot, pvRedirectUri, pvScope, pvApplicationId, pvContentOwnership, currentHref, payvioClientId, payvioOAuthAuthUrl, dotDiv);
                break;
            case 'individual_content':
                pvo.processPvoContentElement(item, pvSubscriptionId, pvContentId, pvPrice, pvS, pvDescription, pvRenderDot, pvRedirectUri, pvScope, pvApplicationId, pvContentOwnership, currentHref, payvioClientId, payvioOAuthAuthUrl, dotDiv);
                break;
        }
    },
    processPvoContentElement: function (item, pvSubscriptionId, pvContentId, pvPrice, pvS, pvDescription, pvRenderDot, pvRedirectUri, pvScope, pvApplicationId, pvContentOwnership, currentHref, payvioClientId, payvioOAuthAuthUrl, dotDiv) {
        if (pvo.usePriceTag) {
            if (jQuery('#hiddenLoggedIn').val() == 1) {
                if (pvContentOwnership == 'Owned') {
                    // Green Dot: charge
                    pvo.addFlowLinkage('green', 'user.charge', 'individual_content', item, pvSubscriptionId, pvContentId, pvPrice, pvS, pvDescription, pvRenderDot, pvRedirectUri, pvScope, pvApplicationId, pvContentOwnership, currentHref, payvioClientId, payvioOAuthAuthUrl, dotDiv);
                }
                else {
                    // Red Dot: charge
                    pvo.addFlowLinkage('red', 'user.charge', 'individual_content', item, pvSubscriptionId, pvContentId, pvPrice, pvS, pvDescription, pvRenderDot, pvRedirectUri, pvScope, pvApplicationId, pvContentOwnership, currentHref, payvioClientId, payvioOAuthAuthUrl, dotDiv);
                }
            }
            else {
                // purple dot : info|charge
                pvo.addFlowLinkage('purple', 'user.info|user.charge', 'individual_content', item, pvSubscriptionId, pvContentId, pvPrice, pvS, pvDescription, pvRenderDot, pvRedirectUri, pvScope, pvApplicationId, pvContentOwnership, currentHref, payvioClientId, payvioOAuthAuthUrl, dotDiv);
            }
        }
        else {
            jQuery(item)[0].href = link;
        }
    },
    processPvoSubscriptionElement: function (item, pvSubscriptionId, pvContentId, pvPrice, pvS, pvDescription, pvRenderDot, pvRedirectUri, pvScope, pvApplicationId, pvContentOwnership, currentHref, payvioClientId, payvioOAuthAuthUrl, dotDiv) {
        if (jQuery('#hiddenLoggedIn').val() == 1) {
            if (pvContentOwnership == 'Owned') {
                // default on subscription (no dot) - color ignored
                pvo.addFlowLinkage('green', 'charge', 'subscription', item, pvSubscriptionId, pvContentId, pvPrice, pvS, pvDescription, pvRenderDot, pvRedirectUri, pvScope, pvApplicationId, pvContentOwnership, currentHref, payvioClientId, payvioOAuthAuthUrl, dotDiv);
            }
            else {
                // default on subscription (no dot) - color ignored
                pvo.addFlowLinkage('red', 'charge', 'subscription', item, pvSubscriptionId, pvContentId, pvPrice, pvS, pvDescription, pvRenderDot, pvRedirectUri, pvScope, pvApplicationId, pvContentOwnership, currentHref, payvioClientId, payvioOAuthAuthUrl, dotDiv);
            }
        }
        else {
            // default on subscription (no dot) - color ignored
            pvo.addFlowLinkage('purple', 'info|charge', 'subscription', item, pvSubscriptionId, pvContentId, pvPrice, pvS, pvDescription, pvRenderDot, pvRedirectUri, pvScope, pvApplicationId, pvContentOwnership, currentHref, payvioClientId, payvioOAuthAuthUrl, dotDiv);
        }
    },
    getDataItem: function (item, dataKey) {
        var value = jQuery(item).data(dataKey);
        if (value != 'undefined') {
            return value;
        }
        return "";
    },
    getItemType: function (pvSubscriptionId, pvContentId) {
        // something in SubscriptionIdentifier and not in ContentIdentifier
        if (pvSubscriptionId != '' && pvContentId == '') {
            return 'subscription';
        }
            // something in ContentIdentifier and not in SubscriptionIdentifier
        else if (pvContentId != '' && pvSubscriptionId == '') {
            return 'individual_content';
        }
        else {
            return 'individual_content';
        }
    },
    getChargeLink: function (pvContentId, pvPrice, pvS, pvDescription, pvRedirectUri, pvScope, pvApplicationId, pvContentOwnership, currentHref, payvioClientId, payvioOAuthAuthUrl) {
        var queryString = '';
        queryString += payvioOAuthAuthUrl;
        queryString += '?response_type=code&client_id=' + jQuery('<div/>').text(payvioClientId).html(); //html encode
        queryString += '&redirect_uri=' + jQuery('<div/>').text(pvRedirectUri).html();
        queryString += '&price=' + pvPrice; //encodeURIComponent(
        queryString += '&s=' + pvS; //encodeURIComponent(
        queryString += '&content_id=' + pvContentId;
        //queryString += '&check_sum=' + pvEncryptedPrice;
        queryString += '&description=' + encodeURIComponent(pvDescription);
        queryString += '&state=' + jQuery('<div/>').text(pvScope).html(); //html encode // state field is required
        queryString += '&scope=' + jQuery('<div/>').text(pvScope).html(); //html encode
        queryString += '&access_type=online';

        return queryString;
    },
    // pass string 'green', 'red', 'purple' for color
    // this 'flowlinkage' may or may not render as a dot depending on types and preferences
    addFlowLinkage: function (color, scope, itemType, item, pvSubscriptionId, pvContentId, pvPrice, pvS, pvDescription, pvRenderDot, pvRedirectUri, pvScope, pvApplicationId, pvContentOwnership, currentHref, payvioClientId, payvioOAuthAuthUrl, dotDiv) {

        var colorCodeToUse = '';

        var payvioPurple = '#04062A';
        var payvioRed = '#D9534f';
        var payvioGreen = '#5cb85c';

        switch (color) {
            case 'green':
                colorCodeToUse = payvioGreen;
                break;
            case 'red':
                colorCodeToUse = payvioRed;
                break;
            case 'purple':
                colorCodeToUse = payvioPurple;
                break;
        }

        if (color == 'red' || color == 'purple') {

            // redirect to what was passed in as redirect URI unless it isn't set in which case we redirect to whatever link the content previously linked to
            var redirectUri = "";
            if (pvRedirectUri == '') {
                redirectUri = jQuery(item)[0].href;
            }
            else {
                redirectUri = pvRedirectUri;
            }

            var link = '';
            // this is the case where the seller site wants to direct all purchases through a custom paywall. we will pass on the flow link to the next step
            if (pvo.enableCustomPaywall) {
                link = jQuery(item)[0].href;

                contentPurchaseLinkEncoded = '?link=' + encodeURIComponent(pvo.getChargeLink(pvContentId, pvPrice, pvS, pvDescription, redirectUri,
                   pvScope, pvApplicationId, pvContentOwnership, currentHref, payvioClientId, payvioOAuthAuthUrl));

                link += contentPurchaseLinkEncoded;
            }
                // hit the flow link directly
            else {
                link = pvo.getChargeLink(pvContentId, pvPrice, pvS, pvDescription, redirectUri,
                   pvScope, pvApplicationId, pvContentOwnership, currentHref, payvioClientId, payvioOAuthAuthUrl);
            }

            jQuery(item)[0].href = link;

            if (pvRenderDot !== 'None') {
                var dotItem = "<a href='" + link + "'>" + pvDescription;
                dotItem += "<span class='dot-container'><span class='base-dot'><span class='higher-dot4 dot-" + color + "'>";
                dotItem += "$" + (pvPrice  / 100.0); // don't display in pennies
                dotItem += "</span></span></span>";
                dotItem += "</a>";

                jQuery(item).after(dotItem);
                jQuery(item).remove();
            }
        }
        else if (color == 'green') {

            if (pvRenderDot !== 'None') {
                var dotItem = "<a href='" + jQuery(item)[0].href + "'>" + pvDescription;
                dotItem += "<span class='dot-container'><span class='base-dot'><span class='higher-dot4 dot-" + color + "'>";
                //dotItem += "$" + pvPrice;
                dotItem += "<span class=\"glyphicon glyphicon-ok\"></span></span></span></span>";
                dotItem += "</a>";

                jQuery(item).after(dotItem);
                jQuery(item).remove();
            }
        }
    }
}

// initialize all payvio tagged content in the system
jQuery(document).ready(function () {
    pvo.init();
});


