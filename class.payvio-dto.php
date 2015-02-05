<?php

class AccessTokenResponse
{
    /// <summary>
    /// TThe token that can be sent to an API
    /// </summary>
    /// <value>
    /// The access token.
    /// </value>
    public $AccessToken;

    /// <summary>
    /// A token that may be used to obtain a new access token. Refresh tokens are valid until the user revokes access. 
    /// This field is only present if access_type=offline is included in the authorization code request.
    /// </summary>
    /// <value>
    /// The refresh token.
    /// </value>
    public $RefreshToken;

    /// <summary>
    /// The remaining lifetime on the access token
    /// </summary>
    /// <value>
    /// The expires in.
    /// </value>
    public $ExpiresIn;

    /// <summary>
    /// Indicates the type of token returned. At this time, this field will always have the value Bearer
    /// </summary>
    /// <value>
    /// Bearer
    /// </value>
    public $TokenType;
}

class UserChargeResponse
{
    public $ConfirmationNumber;
}

?>