<?php
/**
 * API Client
 *
 * @package DatingVIP
 * @subpackage api
 * @category lib
 * @author Boris Momčilović <boris@firstbeatmedia.com>
 * @copyright All rights reserved
 * @version 1.0
 */

namespace DatingVIP\API;

use DatingVIP\cURL\Request as cURL;
use Exception;

class Client
{
    const COMMAND = 'cmd';

/**
 * API URL
 *
 * @var string
 * @access protected
 */
    protected $url;

/**
 * API authorization user
 *
 * @var string
 * @access protected
 */
    protected $user;

/**
 * API authorization pass
 *
 * @var string $pass
 * @access protected
 */
    protected $pass;

/**
 * API authorization pass
 *
 * @var int $timeout [= 5]
 * @access protected
 */
    protected $timeout = 5;

/**
 * Instance of DatingVIP\cURL\Request lib
 *
 * @var Request
 * @access private
 */
    private $curl;

/**
 * Set API url
 *
 * @param string $url
 * @access public
 * @return bool
 */
    public function setUrl($url)
    {
        $this->url = (string) $url;

        return !empty ($this->url);
    }

/**
 * Set auth data for API
 *
 * @param string $user
 * @param string $pass
 * @access public
 * @return bool
 */
    public function setAuth($user, $pass)
    {
        $this->user	= (string) $user;
        $this->pass	= (string) $pass;

        return $this->hasAuth ();
    }

/**
 * Set request timeout value (in seconds)
 *
 * @param int $timeout
 * @access public
 * @return int
 */
    public function setTimeout($timeout)
    {
        $timeout = is_scalar ($timeout) ? (int) $timeout : 0;
        return $timeout < 1
            ? $this->timeout
            : $this->timeout = $timeout;
    }

/**
 * Execute API command
 *
 * @param Command $command
 * @access public
 * @return \DatingVIP\API\Response
 * @throws \Exception
 */
    public function execute(Command $command)
    {
        if (!$command->isValid ()) {
            throw new Exception ('Invalid API command supplied');
        }

        $result = $this->curl()->post ($this->getUrl ($command), $command->getData ());

        return new Response ($result->getData ());
    }

/**
 * Get browser for making API requests
 * - create an instance
 * - assign auth data if we have it
 *
 * @param void
 * @access private
 * @return cURL
 */
    private function curl()
    {
        if (! ($this->curl instanceof cURL)) {
            $this->curl = new cURL ();
        }

        if ($this->hasAuth ()) {
            $this->curl->setCredentials ($this->user, $this->pass);
        }

        return $this->curl->setTimeout ($this->timeout);
    }

/**
 * Get API URL for given command
 *
 * @param Command $command
 * @access private
 * @return string
 */
    private function getUrl(Command $command)
    {
        return $this->url
            . (stripos ($this->url, '?') !== false ? '&' : '?')
            . http_build_query ([self::COMMAND => $command->getName ()]);
    }

/**
 * Check if API has auth data set
 * - checks if user and pass aren't empty
 *
 * @param void
 * @access private
 * @return bool
 */
    private function hasAuth()
    {
        return !empty ($this->user) && !empty ($this->pass);
    }

}
