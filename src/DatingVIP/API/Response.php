<?php

declare(strict_types=1);

/**
 * API Response.
 *
 * @category lib
 *
 * @author Boris Momčilović <boris@firstbeatmedia.com>
 * @copyright All rights reserved
 *
 * @version 2.0
 */

namespace DatingVIP\API;

class Response
{
    const KEY_RESULT = 'result';
    const KEY_TEXTS = 'texts';
    const KEY_META = 'meta';

    /**
     * @var array<string, mixed>
     */
    private array $data = [];

    private string $format = 'json';

    private string $response = '';

    /**
     * @var array<string, mixed>
     */
    private array $error = [];

    /**
     * Default API response constructor
     * - optionally set response.
     */
    public function __construct(string $data = '', string $format = 'json')
    {
        if (!empty($format)) {
            $this->format = $format;
        }
        $this->response = $data;
        $this->set($this->response);
    }

    /**
     * Get API response.
     */
    public function get(): string
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

    public function getRawResponse(): string
    {
        return $this->response;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRawError(): array
    {
        return $this->error;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRawData(): array
    {
        return $this->data;
    }

    /**
     * Set API response
     * - decode from JSON.
     */
    public function set(string $data): bool
    {
        $this->error = [];
        $result = null;
        switch ($this->format) {
            case 'nvp':
                parse_str($data, $this->data);
                $result = !empty($this->data);
                break;

            case 'json':
            default:
                $this->data = empty($data) ? [] : (json_decode($data, true) ?: []);
                $result = json_last_error() === JSON_ERROR_NONE;
                if (empty($result)) {
                    $this->error = [
                        'error' => json_last_error(),
                        'msg'   => json_last_error_msg(),
                    ];
                }
                break;
        }

        return $result;
    }

    /**
     * Set result.
     */
    public function setResult(mixed $data): mixed
    {
        return $this->data[self::KEY_RESULT] = $data;
    }

    /**
     * Set texts (translations).
     */
    public function setTexts(mixed $data): mixed
    {
        return $this->data[self::KEY_TEXTS] = $data;
    }

    /**
     * Set status.
     */
    public function setStatus(mixed $status): mixed
    {
        return $this->setMeta(__FUNCTION__, $status);
    }

    /**
     * Set errors.
     */
    public function setErrors(mixed $errors): mixed
    {
        return $this->setMeta(__FUNCTION__, $errors);
    }

    /**
     * Set messages.
     */
    public function setMessages(mixed $messages): mixed
    {
        return $this->setMeta(__FUNCTION__, $messages);
    }

    /**
     * Set warnings.
     */
    public function setWarnings(mixed $warnings): mixed
    {
        return $this->setMeta(__FUNCTION__, $warnings);
    }

    /**
     * Set announcements.
     */
    public function setAnnouncements(mixed $announcements): mixed
    {
        return $this->setMeta(__FUNCTION__, $announcements);
    }

    /**
     * Set application (developer) errors.
     */
    public function setAppErrors(mixed $app_errors): mixed
    {
        return $this->setMeta(str_replace('Errors', '', __FUNCTION__), $app_errors);
    }

    /**
     * Get result.
     */
    public function getResult(): mixed
    {
        return $this->hasResult() ? $this->data[self::KEY_RESULT] : null;
    }

    /**
     * Get texts.
     */
    public function getTexts(): mixed
    {
        return $this->hasTexts() ? $this->data[self::KEY_TEXTS] : null;
    }

    /**
     * Get status.
     */
    public function getStatus(): mixed
    {
        return $this->getMeta(__FUNCTION__);
    }

    /**
     * Get errors.
     */
    public function getErrors(): mixed
    {
        return $this->getMeta(__FUNCTION__);
    }

    /**
     * Get messages.
     */
    public function getMessages(): mixed
    {
        return $this->getMeta(__FUNCTION__);
    }

    /**
     * Get warnings.
     */
    public function getWarnings(): mixed
    {
        return $this->getMeta(__FUNCTION__);
    }

    /**
     * Get announcements.
     */
    public function getAnnouncements(): mixed
    {
        return $this->getMeta(__FUNCTION__);
    }

    /**
     * Get application (developer) errors.
     */
    public function getAppErrors(): mixed
    {
        return $this->getMeta(str_replace('Errors', '', __FUNCTION__));
    }

    /**
     * Do we have result?
     */
    public function hasResult(): bool
    {
        return array_key_exists(self::KEY_RESULT, $this->data);
    }

    /**
     * Do we have translations (texts)?
     */
    public function hasTexts(): bool
    {
        return array_key_exists(self::KEY_TEXTS, $this->data);
    }

    /**
     * Do we have errors?
     */
    public function hasErrors(): bool
    {
        return $this->hasMeta(__FUNCTION__);
    }

    /**
     * Do we have messages?
     */
    public function hasMessages(): bool
    {
        return $this->hasMeta(__FUNCTION__);
    }

    /**
     * Do we have warnings?
     */
    public function hasWarnings(): bool
    {
        return $this->hasMeta(__FUNCTION__);
    }

    /**
     * Do we have announcements?
     */
    public function hasAnnouncements(): bool
    {
        return $this->hasMeta(__FUNCTION__);
    }

    /**
     * Do we have application (developer) errors?
     */
    public function hasAppErrors(): bool
    {
        return $this->hasMeta(str_replace('Errors', '', __FUNCTION__));
    }

    /**
     * Set some type of meta data into response.
     */
    private function setMeta(string $where, mixed $data): mixed
    {
        return $this->setInData(self::KEY_META, $this->methodToKey($where), $data);
    }

    /**
     * Get some type of meta data shorthand methods.
     */
    private function getMeta(string $what): mixed
    {
        return $this->getFromData(self::KEY_META, $this->methodToKey($what));
    }

    /**
     * Check existence of some type of meta data shorthand methods.
     */
    private function hasMeta(string $what): bool
    {
        return $this->hasData(self::KEY_META, $this->methodToKey($what));
    }

    /**
     * Convert method name to apropriate data key.
     */
    private function methodToKey(string $method): string
    {
        return strtolower(substr($method, 3));
    }

    /**
     * Get specific type of data from specific key from response.
     */
    private function getFromData(string $type, string $key): mixed
    {
        return $this->hasData($type, $key) ? $this->data[$type][$key] : null;
    }

    /**
     * Check existence of specific type of data from specific key from response.
     */
    private function hasData(string $type, string $key): bool
    {
        return isset($this->data[$type]) && isset($this->data[$type][$key]);
    }

    /**
     * Set specific category of data to specific key within response.
     */
    private function setInData(string $type, string $key, mixed $data): mixed
    {
        return $this->data[$type][$key] = $data;
    }
}
