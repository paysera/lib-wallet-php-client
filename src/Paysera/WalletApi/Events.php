<?php


/**
 * Events
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
final class Paysera_WalletApi_Events
{
    const BEFORE_REQUEST = 'paysera.wallet_api.http.before_request';
    const AFTER_RESPONSE = 'paysera.wallet_api.http.after_request';

    const ON_HTTP_EXCEPTION = 'paysera.wallet_api.exception.http';
    const ON_RESPONSE_EXCEPTION = 'paysera.wallet_api.exception.response';

    const AFTER_OAUTH_TOKEN_REFRESH = 'paysera.wallet_api.oauth.after_token_refresh';

} 