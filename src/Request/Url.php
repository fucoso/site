<?php

namespace Fucoso\Site\Request;

/**
 * Url Class on script start parses the URL to extract all its elements.
 * 
 * This class is contained by {@link \Site} as Site::url and is also used by other
 * classes to make decisions based on url values.
 * 
 */
class Url
{

    /**
     *
     * @var string 
     */
    private $url = '';

    /**
     *
     * @var string 
     */
    private $scheme = '';

    /**
     *
     * @var string 
     */
    private $host = '';

    /**
     *
     * @var string 
     */
    private $fullHost = '';

    /**
     *
     * @var string 
     */
    private $subDomain = '';

    /**
     *
     * @var string 
     */
    private $domain = '';

    /**
     *
     * @var string 
     */
    private $domainExtension = '';

    /**
     *
     * @var int 
     */
    private $port = 80;

    /**
     *
     * @var string 
     */
    private $portPath = '';

    /**
     *
     * @var string 
     */
    private $path = '';

    /**
     *
     * @var string 
     */
    private $baseUrl = '';

    /**
     *
     * @var string 
     */
    private $baseUrlUnsecure = '';

    /**
     *
     * @var string 
     */
    private $qualifiedDomain = '';

    /**
     *
     * @var boolean 
     */
    private $qualifiedWebRequest = false;

    public function __construct()
    {
        $this->_initialize();
        $this->_parse();
        $this->_process();
    }

    private function _initialize()
    {
        
    }

    private function _parse()
    {
        $this->_parseUrl();
        $this->_parseHost();
    }

    private function _process()
    {
        $this->baseUrl = "{$this->scheme}://{$this->subDomain}.{$this->domain}.{$this->domainExtension}";
        $this->baseUrlUnsecure = "http://{$this->subDomain}.{$this->domain}.{$this->domainExtension}";
        $this->qualifiedDomain = "{$this->domain}.{$this->domainExtension}";
    }

    private function _parseUrl()
    {

        // Lets fetch the current url from the system and break it down in details.
        if (array_key_exists('SCRIPT_URI', $_SERVER)) {
            $this->url = $_SERVER['SCRIPT_URI'];
        } else if (array_key_exists('HTTP_HOST', $_SERVER) && array_key_exists('REQUEST_URI', $_SERVER)) {
            $this->url = $_SERVER['HTTP_HOST'] . '/' . $_SERVER['REQUEST_URI'];
            $this->url = "http://" . str_replace('//', '/', $this->url);
        } else {
            throw new Exception("Server not configured properly to run the application.");
        }

        $parts = parse_url($this->url);


        // Assign each part to related property.
        $this->scheme = $parts['scheme'];
        $this->host = $parts['host'];
        $this->path = $parts['path'];

        if (array_key_exists('port', $parts)) {
            $this->port = $parts['port'];
        }
    }

    private function _parseHost()
    {
        // First we check if host details were found or not.
        if ($this->host != '') {
            $this->portPath = (($this->port != 80) ? ":" . $this->port : '');

            // Set the full host property to represent the site url with or without domain as user specified.             
            $this->fullHost = "{$this->scheme}://{$this->host}" . $this->portPath;


            // Lets breakdown the host into parts              
            $host_tokens = explode('.', $this->host);
            $count = count($host_tokens);


            // We reverse the tokens array so that we can easily start from domain extension
            // and go to subdomain without extra calculations.            
            $host_tokens = array_reverse($host_tokens);


            // First we get the domain extension that will be com, org, info...            
            $this->domainExtension = $host_tokens[0];


            // second we get the actual domain name.             
            $this->domain = $host_tokens[1];


            // Now finally if the host had more than 2 parts then we take the third part
            // as sub domain.             
            if ($count > 2) {
                $this->subDomain = $host_tokens[2];
            } else {
                $this->subDomain = 'www';
            }
        }
    }

    /**
     * 
     * @return boolean
     */
    public function hasHost()
    {
        if ($this->host != '') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 
     * @return boolean
     */
    public function domainDetected()
    {
        if ($this->domain != '' && $this->domainExtension != '') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * 
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * 
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * 
     * @return string
     */
    public function getFullHost()
    {
        return $this->fullHost;
    }

    /**
     * 
     * @return string
     */
    public function getSubDomain()
    {
        return $this->subDomain;
    }

    /**
     * 
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * 
     * @return string
     */
    public function getDomainExtension()
    {
        return $this->domainExtension;
    }

    /**
     * 
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * 
     * @return string
     */
    public function getPortPath()
    {
        return $this->portPath;
    }

    /**
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * 
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * 
     * @return string
     */
    public function getBaseUrlUnsecure()
    {
        return $this->baseUrlUnsecure;
    }

    /**
     * 
     * @return string
     */
    public function getQualifiedDomain()
    {
        return $this->qualifiedDomain;
    }

    /**
     * 
     * @return string
     */
    public function getQualifiedWebRequest()
    {
        return $this->qualifiedWebRequest;
    }

    public function getQualifiedDomainWithSubDomain()
    {
        if ($this->subDomain != 'www') {
            return "{$this->subDomain}." . $this->getQualifiedDomain();
        } else {
            return $this->getQualifiedDomain();
        }
    }

}
