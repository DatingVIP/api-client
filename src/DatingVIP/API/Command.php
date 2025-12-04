<?php

declare(strict_types=1);

/**
 * API Command.
 *
 * @category lib
 *
 * @author Boris Momčilović <boris@firstbeatmedia.com>
 * @copyright All rights reserved
 *
 * @version 2.0
 */

namespace DatingVIP\API;

class Command
{
    const VAR_CONTROLLER = 'c';

    private string $name = '';

    /**
     * @var array<string, mixed>
     */
    private array $data = [];

    /**
     * Default command constructor
     * - shorthand to set method.
     *
     * @param array<string, mixed> $data
     */
    public function __construct(string $name = '', array $data = [])
    {
        $this->set($name, $data);
    }

    /**
     * Set command and optionally set data
     * - return if set command is valid.
     *
     * @param array<string, mixed> $data
     */
    public function set(string $name, array $data = []): bool
    {
        $this->setName($name);
        $this->setData($data);

        return $this->isValid();
    }

    /**
     * Get command name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get command data.
     *
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Check if set name is considered valid
     * - must not be empty
     * - must have a dot.
     */
    public function isValid(): bool
    {
        return !empty($this->name) && strpos($this->name, '.') !== false;
    }

    /**
     * Set command name
     * - return if set name is considered valid.
     */
    private function setName(string $name): bool
    {
        $this->name = $name;

        return $this->isValid();
    }

    /**
     * Set command data.
     *
     * @param array<string, mixed> $data
     */
    private function setData(array $data): bool
    {
        $this->data = $data;

        if (isset($this->data[self::VAR_CONTROLLER])) {
            unset($this->data[self::VAR_CONTROLLER]);
        }

        return !empty($this->data);
    }
}
