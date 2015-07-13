<?php

/**
 * Host
 *
 * @author Vytautas Gimbutas <vytautas@gimbutas.net>
 */
class Paysera_WalletApi_Entity_Client_Host
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var int|null
     */
    protected $port;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string|null
     */
    protected $protocol;

    /**
     * @var bool
     */
    protected $anyPort = false;

    /**
     * @var bool
     */
    protected $anySubdomain = false;

    /**
     * @return Paysera_WalletApi_Entity_Client_Host
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Set host
     *
     * @param string $host
     *
     * @return Paysera_WalletApi_Entity_Client_Host
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set port
     *
     * @param int $port
     *
     * @return Paysera_WalletApi_Entity_Client_Host
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get port
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Paysera_WalletApi_Entity_Client_Host
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set protocol
     *
     * @param null|string $protocol
     *
     * @return Paysera_WalletApi_Entity_Client_Host
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * Get protocol
     *
     * @return null|string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @return Paysera_WalletApi_Entity_Client_Host
     */
    public function markAsAnyPort()
    {
        $this->anyPort = true;
        return $this;
    }

    /**
     * @return Paysera_WalletApi_Entity_Client_Host
     */
    public function unmarkAsAnyPort()
    {
        $this->anyPort = false;
        return $this;
    }

    /**
     * @return Paysera_WalletApi_Entity_Client_Host
     */
    public function markAsAnySubdomain()
    {
        $this->anySubdomain = true;
        return $this;
    }

    /**
     * @return Paysera_WalletApi_Entity_Client_Host
     */
    public function unmarkAsAnySubdomain()
    {
        $this->anySubdomain = false;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAnyPort()
    {
        return $this->anyPort;
    }

    /**
     * @return bool
     */
    public function isAnySubdomain()
    {
        return $this->anySubdomain;
    }

    /**
     * Builds regexp from self
     *
     * @return string
     */
    public function buildRegexp()
    {
        $regexpParts = array();

        if ($this->getProtocol() !== null) {
            $regexpParts[] = preg_quote($this->getProtocol(), '#') . '\://';
        } else {
            $regexpParts[] = 'https?://';
        }

        $hostname = rtrim($this->getHost(), '/');
        if ($hostname) {
            $hostnameRegexp = preg_quote($hostname, '#');
            if ($this->isAnyPort()) {
                $hostnameRegexp .= '(\:\d+)?';
            } elseif ($this->getPort() !== null) {
                $hostnameRegexp .= ':' . $this->getPort();
            }

            if ($this->isAnySubdomain()) {
                $hostnameRegexp = '([a-zA-Z0-9\-]+\.)*' . $hostnameRegexp;
            }

            $hostnameRegexp .= '/';
            $regexpParts[] = $hostnameRegexp;
        }

        if ($this->getPath()) {
            $regexpParts[] = preg_quote(ltrim($this->getPath(), '/'), '#') . '([/?\\#]|$)';
        }

        $regexp = '#^' . implode('', $regexpParts) . '#';

        return $regexp;
    }
}
