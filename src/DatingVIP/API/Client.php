<?php

declare(strict_types=1);

/**
 * API Client.
 *
 * @category lib
 *
 * @author Boris Momčilović <boris@firstbeatmedia.com>
 * @copyright All rights reserved
 *
 * @version 2.0
 */

namespace DatingVIP\API;

use DatingVIP\cURL\Request as cURL;
use Exception;

class Client
{
    const COMMAND = 'cmd';

    protected string $url = '';

    protected string $user = '';

    protected string $pass = '';

    /**
     * Request timeout in seconds.
     */
    protected int $timeout = 5;

    /**
     * Cookie storage path.
     */
    protected string|false $cookie = false;

    protected string $user_agent = 'DatingVIP-API/2.0.0';

    private ?cURL $curl = null;

    /**
     * Set API url.
     */
    public function setUrl(string $url): bool
    {
        $this->url = $url;

        return !empty($this->url);
    }

    /**
     * Set auth data for API.
     */
    public function setAuth(string $user, string $pass): bool
    {
        $this->user = $user;
        $this->pass = $pass;

        return $this->hasAuth();
    }

    /**
     * Set request timeout value (in seconds).
     */
    public function setTimeout(int $timeout): int
    {
        return $timeout < 1
            ? $this->timeout
            : $this->timeout = $timeout;
    }

    /**
     * Set cookie storage file.
     */
    public function setCookieStorage(string $file): bool
    {
        $this->cookie = $file;

        return !empty($this->cookie);
    }

    /**
     * Execute API command.
     *
     * @throws Exception
     */
    public function execute(Command $command): Response
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
     */
    private function getResponseType(): string
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
     */
    private function curl(): cURL
    {
        if (!($this->curl instanceof cURL)) {
            $this->curl = new cURL();
        }

        if ($this->hasAuth()) {
            $this->curl->setCredentials($this->user, $this->pass);
        }

        if ($this->cookie && is_writable(dirname($this->cookie))) {
            $this->curl->setCookieStorage($this->cookie);
        }

        if ($this->user_agent) {
            $this->curl->setUseragent($this->user_agent);
        }

        return $this->curl->setTimeout($this->timeout);
    }

    /**
     * Get API URL for given command.
     */
    protected function getUrl(Command $command): string
    {
        return $this->url
            .(stripos($this->url, '?') !== false ? '&' : '?')
            .http_build_query([self::COMMAND => $command->getName()]);
    }

    /**
     * Check if API has auth data set
     * - checks if user and pass aren't empty.
     */
    private function hasAuth(): bool
    {
        return !empty($this->user) && !empty($this->pass);
    }
}
