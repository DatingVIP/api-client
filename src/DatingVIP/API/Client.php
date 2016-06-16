<?php
/**
 * API Client.
 *
 * @category lib
 *
 * @author Boris Momčilović <boris@firstbeatmedia.com>
 * @copyright All rights reserved
 *
 * @version 1.0
 */

namespace DatingVIP\API;

use DatingVIP\cURL\Request as cURL;
use Exception;

class Client
{
    const COMMAND = 'cmd';

    /**
     * API URL.
     *
     * @var string
     */
    protected $url;

    /**
     * API authorization user.
     *
     * @var string
     */
    protected $user;

    /**
     * API authorization pass.
     *
     * @var string
     */
    protected $pass;

    /**
     * API authorization pass.
     *
     * @var int [= 5]
     */
    protected $timeout = 5;

    /**
     * Instance of DatingVIP\cURL\Request lib.
     *
     * @var Request
     */
    private $curl;

    /**
     * Set API url.
     *
     * @param string $url
     *
     * @return bool
     */
    public function setUrl($url)
    {
        $this->url = (string) $url;

        return !empty($this->url);
    }

    /**
     * Set auth data for API.
     *
     * @param string $user
     * @param string $pass
     *
     * @return bool
     */
    public function setAuth($user, $pass)
    {
        $this->user = (string) $user;
        $this->pass = (string) $pass;

        return $this->hasAuth();
    }

    /**
     * Set request timeout value (in seconds).
     *
     * @param int $timeout
     *
     * @return int
     */
    public function setTimeout($timeout)
    {
        $timeout = is_scalar($timeout) ? (int) $timeout : 0;

        return $timeout < 1
            ? $this->timeout
            : $this->timeout = $timeout;
    }

    /**
     * Execute API command.
     *
     * @param Command $command
     *
     * @return \DatingVIP\API\Response
     *
     * @throws \Exception
     */
    public function execute(Command $command)
    {
        if (!$command->isValid()) {
            throw new Exception('Invalid API command supplied');
        }

        $result = $this->curl()->post($this->getUrl($command), $command->getData());
        $type = $this->getResponseType();

        return new Response($result->getData(), $type);
    }

    /**
     * Return expected response type based on URL.
     *
     * @param void
     *
     * @return string
     */
    private function getResponseType()
    {
        if (false === strpos($this->url, '.')) {
            return "";
        }
        list($path, $type) = explode('.', basename($this->url), 2);

        return $type;
    }

    /**
     * Get browser for making API requests
     * - create an instance
     * - assign auth data if we have it.
     *
     * @param void
     *
     * @return cURL
     */
    private function curl()
    {
        if (!($this->curl instanceof cURL)) {
            $this->curl = new cURL();
        }

        if ($this->hasAuth()) {
            $this->curl->setCredentials($this->user, $this->pass);
        }

        return $this->curl->setTimeout($this->timeout);
    }

    /**
     * Get API URL for given command.
     *
     * @param Command $command
     *
     * @return string
     */
    protected function getUrl(Command $command)
    {
        return $this->url
            .(stripos($this->url, '?') !== false ? '&' : '?')
            .http_build_query([self::COMMAND => $command->getName()]);
    }

    /**
     * Check if API has auth data set
     * - checks if user and pass aren't empty.
     *
     * @param void
     *
     * @return bool
     */
    private function hasAuth()
    {
        return !empty($this->user) && !empty($this->pass);
    }
}
