<?php

namespace Iodev\Whois\Modules\Tld;

use InvalidArgumentException;
use Iodev\Whois\Helpers\DomainHelper;

/**
 * Immutable data object
 */
class DomainInfo
{
    /**
     * @param DomainResponse $response
     * @param array $data
     * @throws InvalidArgumentException
     */
    public function __construct(DomainResponse $response, $data = [])
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException("Data must be an array");
        }
        $this->response = $response;
        $this->data = $data;
    }

    /** @var DomainResponse */
    private $response;

    /** @var array */
    private $data;

    /**
     * @return DomainResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getDomainName()
    {
        return $this->getval("domainName", "");
    }

    /**
     * @return string
     */
    public function getDomainNameUnicode()
    {
        return DomainHelper::toUnicode($this->getDomainName());
    }

    /**
     * @return string
     */
    public function getWhoisServer()
    {
        return $this->getval("whoisServer", "");
    }

    /**
     * @return string[]
     */
    public function getNameServers()
    {
        return $this->getval("nameServers", []);
    }

    /**
     * @return int
     */
    public function getCreationDate()
    {
        return $this->getval("creationDate", 0);
    }

    /**
     * @return int
     */
    public function getExpirationDate()
    {
        return $this->getval("expirationDate", 0);
    }

    /**
     * @return string[]
     */
    public function getStates()
    {
        return $this->getval("states", []);
    }

    /**
     * @return string
     */
    public function getOwner()
    {
        return $this->getval("owner", "");
    }

    /**
     * @return string
     */
    public function getRegistrar()
    {
        return $this->getval("registrar", "");
    }

    /**
     * @param $key
     * @param mixed $default
     * @return mixed
     */
    private function getval($key, $default = "")
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }
}
