<?php
if (!class_exists('HTTP_Request2')) {
    /** @noinspection PhpIncludeInspection */
    require_once trailingslashit(dirname(dirname(__FILE__)) . 'Net/URL.php');

    /**
     * Class HTTP_Request2
     *
     * Fallback for the PEAR "HTTP_Request2" package, using CURL.
     *
     * @author Koen Van den Wijngaert <koen@siteoptimo.com>
     */
    class HTTP_Request2
    {
        /**
         * Constants
         */
        const METHOD_OPTIONS = 'OPTIONS';
        const METHOD_GET = 'GET';
        const METHOD_HEAD = 'HEAD';
        const METHOD_POST = 'POST';
        const METHOD_PUT = 'PUT';
        const METHOD_DELETE = 'DELETE';
        const METHOD_TRACE = 'TRACE';
        const METHOD_CONNECT = 'CONNECT';

        const AUTH_BASIC = 'basic';
        const AUTH_DIGEST = 'digest';
        /**#@-*/

        /**
         * Regular expression used to check for invalid symbols in RFC 2616 tokens
         * @link http://pear.php.net/bugs/bug.php?id=15630
         */
        const REGEXP_INVALID_TOKEN = '![\x00-\x1f\x7f-\xff()<>@,;:\\\\"/\[\]?={}\s]!';

        /**
         * Regular expression used to check for invalid symbols in cookie strings
         * @link http://pear.php.net/bugs/bug.php?id=15630
         * @link http://web.archive.org/web/20080331104521/http://cgi.netscape.com/newsref/std/cookie_spec.html
         */
        const REGEXP_INVALID_COOKIE = '/[\s,;]/';

        /**
         * Fileinfo magic database resource
         * @var  resource
         * @see  detectMimeType()
         */
        private static $_fileinfoDb;

        /**
         * Observers attached to the request (instances of SplObserver)
         * @var  array
         */
        protected $observers = array();

        /**
         * Request URL
         * @var  NET_URL2
         */
        protected $url;

        /**
         * Request method
         * @var  string
         */
        protected $method = self::METHOD_GET;

        /**
         * Authentication data
         * @var  array
         * @see  getAuth()
         */
        protected $auth;

        /**
         * Request headers
         * @var  array
         */
        protected $headers = array();

        /**
         * Configuration parameters
         * @var  array
         * @see  setConfig()
         */
        protected $config = array(
            'adapter' => 'HTTP_Request2_Adapter_Socket',
            'connect_timeout' => 10,
            'timeout' => 0,
            'use_brackets' => true,
            'protocol_version' => '1.1',
            'buffer_size' => 16384,
            'store_body' => true,
            'local_ip' => null,
            'proxy_host' => '',
            'proxy_port' => '',
            'proxy_user' => '',
            'proxy_password' => '',
            'proxy_auth_scheme' => self::AUTH_BASIC,
            'proxy_type' => 'http',
            'ssl_verify_peer' => true,
            'ssl_verify_host' => true,
            'ssl_cafile' => null,
            'ssl_capath' => null,
            'ssl_local_cert' => null,
            'ssl_passphrase' => null,
            'digest_compat_ie' => false,
            'follow_redirects' => false,
            'max_redirects' => 5,
            'strict_redirects' => false
        );

        /**
         * Last event in request / response handling, intended for observers
         * @var  array
         * @see  getLastEvent()
         */
        protected $lastEvent = array(
            'name' => 'start',
            'data' => null
        );

        /**
         * Adapter used to perform actual HTTP request
         * @var
         */
        protected $adapter;

        /**
         * Request body
         * @var  string|resource
         * @see  setBody()
         */
        protected $body = '';

        /**
         * Array of POST parameters
         * @var  array
         */
        protected $postParams = array();

        /**
         * Array of file uploads (for multipart/form-data POST requests)
         * @var  array
         */
        protected $uploads = array();

        /**
         * Cookie jar to persist cookies between requests
         * @var string
         */
        protected $cookieJar = null;

        /**
         * Sets the URL for this request
         *
         * If the URL has userinfo part (username & password) these will be removed
         * and converted to auth data. If the URL does not have a path component,
         * that will be set to '/'.
         *
         * @param string|Net_URL2 $url Request URL
         *
         * @return   HTTP_Request2
         * @throws   HTTP_Request2_LogicException
         */
        public function setUrl($url)
        {
            if (is_string($url)) {
                $url = new Net_URL2(
                    $url, array(Net_URL2::OPTION_USE_BRACKETS => $this->config['use_brackets'])
                );
            }
            if (!$url instanceof Net_URL2) {
                throw new HTTP_Request2_LogicException(
                    'Parameter is not a valid HTTP URL',
                    HTTP_Request2_Exception::INVALID_ARGUMENT
                );
            }
            // URL contains username / password?
            if ($url->getUserinfo()) {
                $username = $url->getUser();
                $password = $url->getPassword();
                $this->setAuth(rawurldecode($username), $password? rawurldecode($password): '');
                $url->setUserinfo('');
            }
            if ('' == $url->getPath()) {
                $url->setPath('/');
            }
            $this->url = $url;

            return $this;
        }

        /**
         * Returns the request URL
         *
         * @return   Net_URL2
         */
        public function getUrl()
        {
            return $this->url;
        }

        /**
         * Sets the request method
         *
         * @param string $method one of the methods defined in RFC 2616
         *
         * @return   HTTP_Request2
         * @throws   Exception if the method name is invalid
         */
        public function setMethod($method)
        {
            // Method name should be a token: http://tools.ietf.org/html/rfc2616#section-5.1.1
            if (preg_match(self::REGEXP_INVALID_TOKEN, $method)) {
                throw new Exception(
                    "Invalid request method '{$method}'"
                );
            }
            $this->method = $method;

            return $this;
        }

        /**
         * Returns the request method
         *
         * @return   string
         */
        public function getMethod()
        {
            return $this->method;
        }

        /**
         * Sets the configuration parameter(s)
         *
         * The following parameters are available:
         * <ul>
         *   <li> 'adapter'           - adapter to use (string)</li>
         *   <li> 'connect_timeout'   - Connection timeout in seconds (integer)</li>
         *   <li> 'timeout'           - Total number of seconds a request can take.
         *                              Use 0 for no limit, should be greater than
         *                              'connect_timeout' if set (integer)</li>
         *   <li> 'use_brackets'      - Whether to append [] to array variable names (bool)</li>
         *   <li> 'protocol_version'  - HTTP Version to use, '1.0' or '1.1' (string)</li>
         *   <li> 'buffer_size'       - Buffer size to use for reading and writing (int)</li>
         *   <li> 'store_body'        - Whether to store response body in response object.
         *                              Set to false if receiving a huge response and
         *                              using an Observer to save it (boolean)</li>
         *   <li> 'local_ip'          - Specifies the IP address that will be used for accessing
         *                              the network (string)</li>
         *   <li> 'proxy_type'        - Proxy type, 'http' or 'socks5' (string)</li>
         *   <li> 'proxy_host'        - Proxy server host (string)</li>
         *   <li> 'proxy_port'        - Proxy server port (integer)</li>
         *   <li> 'proxy_user'        - Proxy auth username (string)</li>
         *   <li> 'proxy_password'    - Proxy auth password (string)</li>
         *   <li> 'proxy_auth_scheme' - Proxy auth scheme, one of HTTP_Request2::AUTH_* constants (string)</li>
         *   <li> 'proxy'             - Shorthand for proxy_* parameters, proxy given as URL,
         *                              e.g. 'socks5://localhost:1080/' (string)</li>
         *   <li> 'ssl_verify_peer'   - Whether to verify peer's SSL certificate (bool)</li>
         *   <li> 'ssl_verify_host'   - Whether to check that Common Name in SSL
         *                              certificate matches host name (bool)</li>
         *   <li> 'ssl_cafile'        - Cerificate Authority file to verify the peer
         *                              with (use with 'ssl_verify_peer') (string)</li>
         *   <li> 'ssl_capath'        - Directory holding multiple Certificate
         *                              Authority files (string)</li>
         *   <li> 'ssl_local_cert'    - Name of a file containing local cerificate (string)</li>
         *   <li> 'ssl_passphrase'    - Passphrase with which local certificate
         *                              was encoded (string)</li>
         *   <li> 'digest_compat_ie'  - Whether to imitate behaviour of MSIE 5 and 6
         *                              in using URL without query string in digest
         *                              authentication (boolean)</li>
         *   <li> 'follow_redirects'  - Whether to automatically follow HTTP Redirects (boolean)</li>
         *   <li> 'max_redirects'     - Maximum number of redirects to follow (integer)</li>
         *   <li> 'strict_redirects'  - Whether to keep request method on redirects via status 301 and
         *                              302 (true, needed for compatibility with RFC 2616)
         *                              or switch to GET (false, needed for compatibility with most
         *                              browsers) (boolean)</li>
         * </ul>
         *
         * @param string|array $nameOrConfig configuration parameter name or array
         *                                   ('parameter name' => 'parameter value')
         * @param mixed        $value        parameter value if $nameOrConfig is not an array
         *
         * @return   HTTP_Request2
         * @throws   Exception If the parameter is unknown
         */
        public function setConfig($nameOrConfig, $value = null)
        {
            if (is_array($nameOrConfig)) {
                foreach ($nameOrConfig as $name => $value) {
                    $this->setConfig($name, $value);
                }

            } elseif ('proxy' == $nameOrConfig) {
                $url = new Net_URL2($value);
                $this->setConfig(array(
                        'proxy_type'     => $url->getScheme(),
                        'proxy_host'     => $url->getHost(),
                        'proxy_port'     => $url->getPort(),
                        'proxy_user'     => rawurldecode($url->getUser()),
                        'proxy_password' => rawurldecode($url->getPassword())
                    ));

            } else {
                if (!array_key_exists($nameOrConfig, $this->config)) {
                    throw new Exception(
                        "Unknown configuration parameter '{$nameOrConfig}'"
                    );
                }
                $this->config[$nameOrConfig] = $value;
            }

            return $this;
        }

        /**
         * Returns the value(s) of the configuration parameter(s)
         *
         * @param string $name parameter name
         *
         * @return   mixed   value of $name parameter, array of all configuration
         *                   parameters if $name is not given
         * @throws   HTTP_Request2_LogicException If the parameter is unknown
         */
        public function getConfig($name = null)
        {
            if (null === $name) {
                return $this->config;
            } elseif (!array_key_exists($name, $this->config)) {
                throw new HTTP_Request2_LogicException(
                    "Unknown configuration parameter '{$name}'",
                    HTTP_Request2_Exception::INVALID_ARGUMENT
                );
            }
            return $this->config[$name];
        }

        /**
         * Sets the autentification data
         *
         * @param string $user     user name
         * @param string $password password
         * @param string $scheme   authentication scheme
         *
         * @return   HTTP_Request2
         */
        public function setAuth($user, $password = '', $scheme = self::AUTH_BASIC)
        {
            if (empty($user)) {
                $this->auth = null;
            } else {
                $this->auth = array(
                    'user'     => (string)$user,
                    'password' => (string)$password,
                    'scheme'   => $scheme
                );
            }

            return $this;
        }

        /**
         * Returns the authentication data
         *
         * The array has the keys 'user', 'password' and 'scheme', where 'scheme'
         * is one of the HTTP_Request2::AUTH_* constants.
         *
         * @return   array
         */
        public function getAuth()
        {
            return $this->auth;
        }

        /**
         * Sets request header(s)
         *
         * The first parameter may be either a full header string 'header: value' or
         * header name. In the former case $value parameter is ignored, in the latter
         * the header's value will either be set to $value or the header will be
         * removed if $value is null. The first parameter can also be an array of
         * headers, in that case method will be called recursively.
         *
         * Note that headers are treated case insensitively as per RFC 2616.
         *
         * <code>
         * $req->setHeader('Foo: Bar'); // sets the value of 'Foo' header to 'Bar'
         * $req->setHeader('FoO', 'Baz'); // sets the value of 'Foo' header to 'Baz'
         * $req->setHeader(array('foo' => 'Quux')); // sets the value of 'Foo' header to 'Quux'
         * $req->setHeader('FOO'); // removes 'Foo' header from request
         * </code>
         *
         * @param string|array      $name    header name, header string ('Header: value')
         *                                   or an array of headers
         * @param string|array|null $value   header value if $name is not an array,
         *                                   header will be removed if value is null
         * @param bool              $replace whether to replace previous header with the
         *                                   same name or append to its value
         *
         * @return   HTTP_Request2
         * @throws   HTTP_Request2_LogicException
         */
        public function setHeader($name, $value = null, $replace = true)
        {
            if (is_array($name)) {
                foreach ($name as $k => $v) {
                    if (is_string($k)) {
                        $this->setHeader($k, $v, $replace);
                    } else {
                        $this->setHeader($v, null, $replace);
                    }
                }
            } else {
                if (null === $value && strpos($name, ':')) {
                    list($name, $value) = array_map('trim', explode(':', $name, 2));
                }
                // Header name should be a token: http://tools.ietf.org/html/rfc2616#section-4.2
                if (preg_match(self::REGEXP_INVALID_TOKEN, $name)) {
                    throw new HTTP_Request2_LogicException(
                        "Invalid header name '{$name}'",
                        HTTP_Request2_Exception::INVALID_ARGUMENT
                    );
                }
                // Header names are case insensitive anyway
                $name = strtolower($name);
                if (null === $value) {
                    unset($this->headers[$name]);

                } else {
                    if (is_array($value)) {
                        $value = implode(', ', array_map('trim', $value));
                    } elseif (is_string($value)) {
                        $value = trim($value);
                    }
                    if (!isset($this->headers[$name]) || $replace) {
                        $this->headers[$name] = $value;
                    } else {
                        $this->headers[$name] .= ', ' . $value;
                    }
                }
            }

            return $this;
        }

        /**
         * Returns the request headers
         *
         * The array is of the form ('header name' => 'header value'), header names
         * are lowercased
         *
         * @return   array
         */
        public function getHeaders()
        {
            return $this->headers;
        }

        /**
         * Adds a cookie to the request
         *
         * If the request does not have a CookieJar object set, this method simply
         * appends a cookie to "Cookie:" header.
         *
         * If a CookieJar object is available, the cookie is stored in that object.
         * Data from request URL will be used for setting its 'domain' and 'path'
         * parameters, 'expires' and 'secure' will be set to null and false,
         * respectively. If you need further control, use CookieJar's methods.
         *
         * @param string $name  cookie name
         * @param string $value cookie value
         *
         * @return   HTTP_Request2
         * @throws   HTTP_Request2_LogicException
         * @see      setCookieJar()
         */
        public function addCookie($name, $value)
        {
            if (!empty($this->cookieJar)) {
                $this->cookieJar->store(
                    array('name' => $name, 'value' => $value), $this->url
                );

            } else {
                $cookie = $name . '=' . $value;
                if (preg_match(self::REGEXP_INVALID_COOKIE, $cookie)) {
                    throw new HTTP_Request2_LogicException(
                        "Invalid cookie: '{$cookie}'",
                        HTTP_Request2_Exception::INVALID_ARGUMENT
                    );
                }
                $cookies = empty($this->headers['cookie'])? '': $this->headers['cookie'] . '; ';
                $this->setHeader('cookie', $cookies . $cookie);
            }

            return $this;
        }

        /**
         * Sets the request body
         *
         * If you provide file pointer rather than file name, it should support
         * fstat() and rewind() operations.
         *
         * @param string $body       A  string with the body
         * @param bool               $isFilename Whether first parameter is a
         *                          filename
         *
         * @return   HTTP_Request2
         */
        public function setBody($body, $isFilename = false)
        {
            $this->body = (string)$body;

            return $this;
        }

        /**
         * Returns the request body
         *
         * @return   string
         */
        public function getBody()
        {
            return $this->body;
        }





    }
}