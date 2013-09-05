<?php


/**
 * OAuth Client
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class Paysera_WalletApi_Client_OAuthClient extends Paysera_WalletApi_Client_BaseClient
{

    /**
     * Exchanges authorization code for access token. Use this method only if you make custom "code" parameter handling.
     * Use getOAuthAccessToken method instead for usual uses.
     *
     * @param string $authorizationCode
     * @param string $redirectUri
     *
     * @return Paysera_WalletApi_Entity_MacAccessToken
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     *
     * @see Paysera_WalletApi::getOAuthAccessToken
     */
    public function exchangeCodeForAccessToken($authorizationCode, $redirectUri = null)
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
            'code' => $authorizationCode,
        );
        if ($redirectUri !== null) {
            $parameters['redirect_uri'] = $redirectUri;
        }
        $responseData = $this->post('token', $parameters);
        return $this->mapper->decodeAccessToken($responseData);
    }

    /**
     * Exchanges resource owner password credentials for access token.
     * This method is only for Resource Owner Password Credentials Grant, which is disabled for most clients by default.
     * Use Authorization Code Grant by getAuthorizationUri and getOAuthAccessToken methods if available.
     *
     * @param string $username
     * @param string $password
     * @param array  $scopes   can contain Paysera_WalletApi_OAuth_Consumer::SCOPE_* constants
     *
     * @return Paysera_WalletApi_Entity_MacAccessToken
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function exchangePasswordForAccessToken($username, $password, array $scopes = array())
    {
        Paysera_WalletApi_Util_Assert::isScalar($username);
        Paysera_WalletApi_Util_Assert::isScalar($password);
        $parameters = array(
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
            'scope' => implode(' ', $scopes),
        );
        $responseData = $this->post('token', $parameters);
        return $this->mapper->decodeAccessToken($responseData);
    }

    /**
     * Exchanges resource owner password credentials for access token.
     * This method is only for Resource Owner Password Credentials Grant, which is disabled for most clients by default.
     * Use Authorization Code Grant by getAuthorizationUri and getOAuthAccessToken methods if available.
     *
     * @param string     $refreshToken
     * @param array|null $scopes       can contain Paysera_WalletApi_OAuth_Consumer::SCOPE_* constants
     *
     * @return Paysera_WalletApi_Entity_MacAccessToken
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function refreshAccessToken($refreshToken, $scopes = null)
    {
        Paysera_WalletApi_Util_Assert::isScalar($refreshToken);
        $parameters = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        );
        if ($scopes !== null) {
            $parameters['scope'] = implode(' ', $scopes);
        }
        $responseData = $this->post('token', $parameters);
        return $this->mapper->decodeAccessToken($responseData);
    }

    /**
     * Creates OAuth session. Used for passing confirmed user information, if available
     *
     * @param array                                    $parameters
     * @param Paysera_WalletApi_Entity_UserInformation $userInformation
     *
     * @return mixed|null
     */
    public function createSession(array $parameters, Paysera_WalletApi_Entity_UserInformation $userInformation)
    {
        $parameters['user'] = $this->mapper->encodeUserInformation($userInformation);
        return $this->post('session', $parameters);
    }

    /**
     * Makes POST request, uri can be relative to current context (without endpoint and API path)
     * Content is encoded to URL-encoded format
     *
     * @param string $uri
     * @param mixed  $content
     * @param array  $options
     *
     * @return mixed|null
     */
    public function post($uri, $content = null, $options = array())
    {
        return $this->makeRequest(new Paysera_WalletApi_Http_Request(
            $uri,
            Paysera_WalletApi_Http_Request::METHOD_POST,
            $content === null ? '' : http_build_query($content, null, '&'),
            array('Content-Type' => Paysera_WalletApi_Http_Request::CONTENT_TYPE_URLENCODED)
        ), $options);
    }

    /**
     * Makes PUT request, uri can be relative to current context (without endpoint and API path)
     * Content is encoded to URL-encoded format
     *
     * @param string $uri
     * @param mixed  $content
     * @param array  $options
     *
     * @return mixed|null
     */
    public function put($uri, $content = null, $options = array())
    {
        return $this->makeRequest(new Paysera_WalletApi_Http_Request(
            $uri,
            Paysera_WalletApi_Http_Request::METHOD_PUT,
            $content === null ? '' : http_build_query($content, null, '&'),
            array('Content-Type' => Paysera_WalletApi_Http_Request::CONTENT_TYPE_URLENCODED)
        ), $options);
    }


} 