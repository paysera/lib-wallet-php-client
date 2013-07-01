<?php


class Paysera_WalletApi_Util_RequestInfo
{
    /**
     * @var array
     */
    protected $server;

    /**
     * Constructs object
     *
     * @param array $server server info, usually $_SERVER
     */
    public function __construct(array $server)
    {
        $this->server = $server;
    }

    /**
     * Gets current URI without provided query parameters
     *
     * @param array $removeParameters
     *
     * @return string
     */
    public function getCurrentUri(array $removeParameters = array())
    {
        if (
            isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
        ) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        $currentUri = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $parts = parse_url($currentUri);

        $port = isset($parts['port']) && (
            ($protocol === 'http://' && $parts['port'] !== 80)
            || ($protocol === 'https://' && $parts['port'] !== 443)
        ) ? ':' . $parts['port'] : '';

        if (empty($parts['query'])) {
            $query = '';
        } elseif (count($removeParameters) === 0) {
            $query = '?' . $parts['query'];
        } else {
            $queryParameters = array();
            foreach ($this->parseHttpQuery($parts['query']) as $key => $value) {
                if (!in_array($key, $removeParameters)) {
                    $queryParameters[$key] = $value;
                }
            }
            if (count($queryParameters) > 0) {
                $query = '?' . http_build_query($queryParameters, null, '&');
            } else {
                $query = '';
            }
        }

        return $protocol . $parts['host'] . $port . $parts['path'] . $query;
    }


    /**
     * Parses HTTP query to array
     *
     * @param string $query
     *
     * @return array
     */
    protected function parseHttpQuery($query) {
        $params = array();
        parse_str($query, $params);
        if (get_magic_quotes_gpc()) {
            $params = $this->stripSlashesRecursively($params);
        }
        return $params;
    }

    /**
     * Strips slashes recursively, so this method can be used on arrays with more than one level
     *
     * @param mixed $data
     *
     * @return mixed
     */
    protected function stripSlashesRecursively($data) {
        if (is_array($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[stripslashes($key)] = $this->stripSlashesRecursively($value);
            }
            return $result;
        } else {
            return stripslashes($data);
        }
    }
}