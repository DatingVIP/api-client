<?php
/**
 * API Response
 *
 * @package DatingVIP
 * @subpackage api
 * @category lib
 * @author Boris Momčilović <boris@firstbeatmedia.com>
 * @copyright All rights reserved
 * @version 1.0
 */

namespace DatingVIP\API;

class Response
{
    const KEY_RESULT = 'result';
    const KEY_TEXTS = 'texts';
    const KEY_META = 'meta';

/**
 * Array holding response data
 *
 * @var array [= []]
 * @access private
 */
    private $data = [];

/**
 * Default API response constructor
 * - optionally set response
 *
 * @param mixed $data [= '']
 * @access public
 */
    public function __construct($data = '')
    {
        $this->set ($data);
    }

/**
 * Get API response (JSON encoded)
 *
 * @param void
 * @access public
 * @return string
 */
    public function get()
    {
        return json_encode ($this->data, JSON_PRETTY_PRINT);
    }

/**
 * Set API response
 * - decode from JSON
 *
 * @param string $data
 * @access public
 * @return bool
 */
    public function set($data)
    {
        $this->data = json_decode ($data, true) ?: [];

        return json_last_error () == JSON_ERROR_NONE;
    }

/**
 * Set result
 *
 * @param mixed $data
 * @access public
 * @return mixed
 */
    public function setResult($data)
    {
        return $this->data[self::KEY_RESULT] = $data;
    }

/**
 * Set texts (translations)
 *
 * @param mixed $data
 * @access public
 * @return mixed
 */
    public function setTexts($data)
    {
        return $this->data[self::KEY_TEXTS] = $data;
    }

/**
 * Set errors
 *
 * @param mixed $errors
 * @access public
 * @return mixed
 */
    public function setErrors($errors)
    {
        return $this->setMeta (__FUNCTION__, $errors);
    }

/**
 * Set result
 *
 * @param mixed $messages
 * @access public
 * @return mixed
 */
    public function setMessages($messages)
    {
        return $this->setMeta (__FUNCTION__, $messages);
    }

/**
 * Set warnings
 *
 * @param mixed $warnings
 * @access public
 * @return mixed
 */
    public function setWarnings($warnings)
    {
        return $this->setMeta (__FUNCTION__, $warnings);
    }

/**
 * Set announcements
 *
 * @param mixed $announcements
 * @access public
 * @return mixed
 */
    public function setAnnouncements($announcements)
    {
        return $this->setMeta (__FUNCTION__, $announcements);
    }

/**
 * Set application (developer) errors
 *
 * @param mixed $app_errors
 * @access public
 * @return mixed
 */
    public function setAppErrors($app_errors)
    {
        return $this->setMeta (str_replace ('Errors', '', __FUNCTION__), $app_errors);
    }

/**
 * Get result
 *
 * @param void
 * @access public
 * @return mixed
 */
    public function getResult()
    {
        return $this->hasResult () ? $this->data[self::KEY_RESULT] : null;
    }

/**
 * Get texts
 *
 * @param void
 * @access public
 * @return mixed
 */
    public function getTexts()
    {
        return $this->hasTexts () ? $this->data[self::KEY_TEXTS] : null;
    }

/**
 * Get errors
 *
 * @param void
 * @access public
 * @return mixed
 */
    public function getErrors()
    {
        return $this->getMeta (__FUNCTION__);
    }

/**
 * Get messages
 *
 * @param void
 * @access public
 * @return mixed
 */
    public function getMessages()
    {
        return $this->getMeta (__FUNCTION__);
    }

/**
 * Get warnings
 *
 * @param void
 * @access public
 * @return mixed
 */
    public function getWarnings()
    {
        return $this->getMeta (__FUNCTION__);
    }

/**
 * Get announcements
 *
 * @param void
 * @access public
 * @return mixed
 */
    public function getAnnouncements()
    {
        return $this->getMeta (__FUNCTION__);
    }

/**
 * Get application (developer) errors
 *
 * @param void
 * @access public
 * @return mixed
 */
    public function getAppErrors()
    {
        return $this->getMeta (str_replace ('Errors', '', __FUNCTION__));
    }

/**
 * Do we have result?
 *
 * @param void
 * @access public
 * @return bool
 */
    public function hasResult()
    {
        return array_key_exists (self::KEY_RESULT, $this->data);
    }

/**
 * Do we have translations (texts)?
 *
 * @param void
 * @access public
 * @return bool
 */
    public function hasTexts()
    {
        return array_key_exists (self::KEY_TEXTS, $this->data);
    }

/**
 * Do we have errors?
 *
 * @param void
 * @access public
 * @return bool
 */
    public function hasErrors()
    {
        return $this->hasMeta (__FUNCTION__);
    }

/**
 * Do we have messages?
 *
 * @param void
 * @access public
 * @return bool
 */
    public function hasMessages()
    {
        return $this->hasMeta (__FUNCTION__);
    }

/**
 * Do we have warnings?
 *
 * @param void
 * @access public
 * @return bool
 */
    public function hasWarnings()
    {
        return $this->hasMeta (__FUNCTION__);
    }

/**
 * Do we have announcements?
 *
 * @param void
 * @access public
 * @return bool
 */
    public function hasAnnouncements()
    {
        return $this->hasMeta (__FUNCTION__);
    }

/**
 * Do we have application (developer) errors?
 *
 * @param void
 * @access public
 * @return bool
 */
    public function hasAppErrors()
    {
        return $this->hasMeta (str_replace ('Errors', '', __FUNCTION__));
    }

/**
 * Set some type of meta data into response
 *
 * @param string $where
 * @param mixed $data
 * @access private
 * @return bool
 */
    private function setMeta($where, $data)
    {
        return $this->setInData (self::KEY_META, $this->methodToKey ($where), $data);
    }

/**
 * Get some type of meta data shorthand methods
 *
 * @param string $what
 * @access private
 * @return bool
 */
    private function getMeta($what)
    {
        return $this->getFromData (self::KEY_META, $this->methodToKey ($what));
    }

/**
 * Check existence of some type of meta data shorthand methods
 *
 * @param string $what
 * @access private
 * @return bool
 */
    private function hasMeta($what)
    {
        return $this->hasData (self::KEY_META, $this->methodToKey ($what));
    }

/**
 * Convert method name to apropriate data key
 *
 * @param string $method
 * @access private
 * @return string
 */
    private function methodToKey($method)
    {
        return strtolower (substr ($method, 3));
    }

/**
 * Get specific type of data from specific key from response
 *
 * @param string $type
 * @param string $key
 * @access private
 * @return mixed
 */
    private function getFromData($type, $key)
    {
        return $this->hasData ($type, $key) ? $this->data[$type][$key] : null;
    }

/**
 * Check existence of specific type of data from specific key from response
 *
 * @param string $type
 * @param string $key
 * @access private
 * @return mixed
 */
    private function hasData($type, $key)
    {
        return isset ($this->data[$type]) && isset ($this->data[$type][$key]);
    }

/**
 * Set specific category of data to specific key within response
 *
 * @param string $type
 * @param string $key
 * @param mixed $data
 * @access private
 * @return mixed
 */
    private function setInData($type, $key, $data)
    {
        return $this->data[$type][$key] = $data;
    }

}
