<?php
/**
 * API Response.
 *
 * @category lib
 *
 * @author Boris Momčilović <boris@firstbeatmedia.com>
 * @copyright All rights reserved
 *
 * @version 1.0
 */

namespace DatingVIP\API;

use RuntimeException;

class Response
{
    const KEY_RESULT = 'result';
    const KEY_TEXTS = 'texts';
    const KEY_META = 'meta';

    /**
     * Array holding response data.
     *
     * @var array [= []]
     */
    private $data = [];

    /**
     * Format of response.
     *
     * @var string [= 'json']
     */
    private $format = 'json';

    private $response = '';

    /**
     * Default API response constructor
     * - optionally set response.
     *
     * @param mixed $data   [= '']
     * @param mixed $format [= 'json']
     * @throws RuntimeException
     */
    public function __construct($data = '', $format = 'json')
    {
        if (!empty($format)) {
            $this->format = $format;
        }
        $this->response = $data;
        $this->set($this->response);
    }

    /**
     * Get API response.
     *
     * @param void
     *
     * @return string
     */
    public function get()
    {
        $result = null;
        switch ($this->format) {
            case 'nvp':
                $result = http_build_query($this->data);
                break;

            case 'json':
            default:
                $result = json_encode($this->data, JSON_PRETTY_PRINT);
                break;
        }

        return $result;
    }

    public function getRawResponse () {
        return $this->response;
    }

    /**
     * Set API response
     * - decode from JSON.
     *
     * @param string $data
     * @throws RuntimeException
     * @return bool
     */
    public function set($data)
    {
        $result = null;
        switch ($this->format) {
            case 'nvp':
                parse_str($data, $this->data);
                $result = !empty($this->data);
                break;

            case 'json':
            default:
                $this->data = empty($data) ? [] : (json_decode($data, true) ?: []);
                $result = json_last_error() == JSON_ERROR_NONE;
                if (empty ($result)) {
                    throw new RuntimeException ("Error decoding: " . json_last_error_msg ());
                }
                break;
        }

        return $result;
    }

    /**
     * Set result.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function setResult($data)
    {
        return $this->data[self::KEY_RESULT] = $data;
    }

    /**
     * Set texts (translations).
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function setTexts($data)
    {
        return $this->data[self::KEY_TEXTS] = $data;
    }

    /**
     * Set status.
     *
     * @param status $status
     *
     * @return mixed
     */
    public function setStatus($status)
    {
        return $this->setMeta(__FUNCTION__, $status);
    }

    /**
     * Set errors.
     *
     * @param mixed $errors
     *
     * @return mixed
     */
    public function setErrors($errors)
    {
        return $this->setMeta(__FUNCTION__, $errors);
    }

    /**
     * Set result.
     *
     * @param mixed $messages
     *
     * @return mixed
     */
    public function setMessages($messages)
    {
        return $this->setMeta(__FUNCTION__, $messages);
    }

    /**
     * Set warnings.
     *
     * @param mixed $warnings
     *
     * @return mixed
     */
    public function setWarnings($warnings)
    {
        return $this->setMeta(__FUNCTION__, $warnings);
    }

    /**
     * Set announcements.
     *
     * @param mixed $announcements
     *
     * @return mixed
     */
    public function setAnnouncements($announcements)
    {
        return $this->setMeta(__FUNCTION__, $announcements);
    }

    /**
     * Set application (developer) errors.
     *
     * @param mixed $app_errors
     *
     * @return mixed
     */
    public function setAppErrors($app_errors)
    {
        return $this->setMeta(str_replace('Errors', '', __FUNCTION__), $app_errors);
    }

    /**
     * Get result.
     *
     * @param void
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->hasResult() ? $this->data[self::KEY_RESULT] : null;
    }

    /**
     * Get texts.
     *
     * @param void
     *
     * @return mixed
     */
    public function getTexts()
    {
        return $this->hasTexts() ? $this->data[self::KEY_TEXTS] : null;
    }

    /**
     * Get status.
     *
     * @param void
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->getMeta(__FUNCTION__);
    }

    /**
     * Get errors.
     *
     * @param void
     *
     * @return mixed
     */
    public function getErrors()
    {
        return $this->getMeta(__FUNCTION__);
    }

    /**
     * Get messages.
     *
     * @param void
     *
     * @return mixed
     */
    public function getMessages()
    {
        return $this->getMeta(__FUNCTION__);
    }

    /**
     * Get warnings.
     *
     * @param void
     *
     * @return mixed
     */
    public function getWarnings()
    {
        return $this->getMeta(__FUNCTION__);
    }

    /**
     * Get announcements.
     *
     * @param void
     *
     * @return mixed
     */
    public function getAnnouncements()
    {
        return $this->getMeta(__FUNCTION__);
    }

    /**
     * Get application (developer) errors.
     *
     * @param void
     *
     * @return mixed
     */
    public function getAppErrors()
    {
        return $this->getMeta(str_replace('Errors', '', __FUNCTION__));
    }

    /**
     * Do we have result?
     *
     * @param void
     *
     * @return bool
     */
    public function hasResult()
    {
        return array_key_exists(self::KEY_RESULT, $this->data);
    }

    /**
     * Do we have translations (texts)?
     *
     * @param void
     *
     * @return bool
     */
    public function hasTexts()
    {
        return array_key_exists(self::KEY_TEXTS, $this->data);
    }

    /**
     * Do we have errors?
     *
     * @param void
     *
     * @return bool
     */
    public function hasErrors()
    {
        return $this->hasMeta(__FUNCTION__);
    }

    /**
     * Do we have messages?
     *
     * @param void
     *
     * @return bool
     */
    public function hasMessages()
    {
        return $this->hasMeta(__FUNCTION__);
    }

    /**
     * Do we have warnings?
     *
     * @param void
     *
     * @return bool
     */
    public function hasWarnings()
    {
        return $this->hasMeta(__FUNCTION__);
    }

    /**
     * Do we have announcements?
     *
     * @param void
     *
     * @return bool
     */
    public function hasAnnouncements()
    {
        return $this->hasMeta(__FUNCTION__);
    }

    /**
     * Do we have application (developer) errors?
     *
     * @param void
     *
     * @return bool
     */
    public function hasAppErrors()
    {
        return $this->hasMeta(str_replace('Errors', '', __FUNCTION__));
    }

    /**
     * Set some type of meta data into response.
     *
     * @param string $where
     * @param mixed  $data
     *
     * @return bool
     */
    private function setMeta($where, $data)
    {
        return $this->setInData(self::KEY_META, $this->methodToKey($where), $data);
    }

    /**
     * Get some type of meta data shorthand methods.
     *
     * @param string $what
     *
     * @return bool
     */
    private function getMeta($what)
    {
        return $this->getFromData(self::KEY_META, $this->methodToKey($what));
    }

    /**
     * Check existence of some type of meta data shorthand methods.
     *
     * @param string $what
     *
     * @return bool
     */
    private function hasMeta($what)
    {
        return $this->hasData(self::KEY_META, $this->methodToKey($what));
    }

    /**
     * Convert method name to apropriate data key.
     *
     * @param string $method
     *
     * @return string
     */
    private function methodToKey($method)
    {
        return strtolower(substr($method, 3));
    }

    /**
     * Get specific type of data from specific key from response.
     *
     * @param string $type
     * @param string $key
     *
     * @return mixed
     */
    private function getFromData($type, $key)
    {
        return $this->hasData($type, $key) ? $this->data[$type][$key] : null;
    }

    /**
     * Check existence of specific type of data from specific key from response.
     *
     * @param string $type
     * @param string $key
     *
     * @return mixed
     */
    private function hasData($type, $key)
    {
        return isset($this->data[$type]) && isset($this->data[$type][$key]);
    }

    /**
     * Set specific category of data to specific key within response.
     *
     * @param string $type
     * @param string $key
     * @param mixed  $data
     *
     * @return mixed
     */
    private function setInData($type, $key, $data)
    {
        return $this->data[$type][$key] = $data;
    }
}
