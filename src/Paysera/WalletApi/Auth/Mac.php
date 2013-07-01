<?php

/**
 * Signs requests before sending them to API
 */
class Paysera_WalletApi_Auth_Mac implements Paysera_WalletApi_Auth_SignerInterface
{
    /**
     * @var string
     */
    protected $macId;

    /**
     * @var string
     */
    protected $macSecret;

    /**
     * Constructs object
     *
     * @param string $macId
     * @param string $macSecret
     */
    public function __construct($macId, $macSecret)
    {
        $this->macId = $macId;
        $this->macSecret = $macSecret;
    }

    /**
     * Signs request - adds Authorization header with generated value
     *
     * @param Paysera_WalletApi_Http_Request $request
     */
    public function signRequest(Paysera_WalletApi_Http_Request $request)
    {
        $timestamp = $this->getTimestamp();
        $nonce = $this->generateNonce();
        $ext = $this->calculateExt($request);
        $mac = $this->calculateMac(
            $timestamp,
            $nonce,
            $request->getMethod(),
            $request->getUri(),
            $request->getHost(),
            $request->getPort(),
            $ext,
            $this->macSecret
        );

        $params = array(
            'id' => $this->macId,
            'ts' => $timestamp,
            'nonce' => $nonce,
            'mac' => $mac,
        );
        if ($ext != '') {
            $params['ext'] = $ext;
        }
        $parts = array();
        foreach ($params as $name => $value) {
            $parts[] = $name . '="' . $value . '"';
        }
        $authenticationHeader = 'MAC ' . implode(', ', $parts);
        $request->setHeader('Authorization', $authenticationHeader);
    }

    protected function getTimestamp()
    {
        return time();
    }

    /**
     * Generates pseudo-random nonce value
     *
     * @param integer $length
     *
     * @return string
     */
    protected function generateNonce($length = 32)
    {
        $nonce = '';
        for ($i = 0; $i < $length; $i++) {
            $rnd = mt_rand(0, 92);
            if ($rnd >= 2) {
                $rnd++;
            }
            if ($rnd >= 60) {
                $rnd++;
            }
            $nonce .= chr(32 + $rnd);
        }
        return $nonce;
    }

    /**
     * Calculates ext field for this request to be used in MAC authorization header
     *
     * @param Paysera_WalletApi_Http_Request $request
     *
     * @return string
     */
    protected function calculateExt(Paysera_WalletApi_Http_Request $request)
    {
        $content = $request->getContent();
        if ($content == '') {
            return '';
        } else {
            return hash('sha256', $content, false);
        }
    }

    /**
     * Calculates MAC value by provided arguments
     *
     * @param string $timestamp
     * @param string $nonce
     * @param string $method
     * @param string $uri
     * @param string $host
     * @param string $port
     * @param string $ext
     * @param string $secret
     *
     * @return string
     */
    protected function calculateMac($timestamp, $nonce, $method, $uri, $host, $port, $ext, $secret)
    {
        $normalizedRequest = implode("\n", array($timestamp, $nonce, $method, $uri, $host, $port, $ext, ''));
        return base64_encode(hash_hmac('sha256', $normalizedRequest, $secret, true));
    }
}