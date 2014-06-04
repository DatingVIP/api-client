<?php
/**
 * API Command
 *
 * @package DatingVIP
 * @subpackage api
 * @category lib
 * @author Boris Momčilović <boris@firstbeatmedia.com>
 * @copyright All rights reserved
 * @version 1.0
 */

namespace DatingVIP\API;

class Command
{
    const VAR_CONTROLLER = 'c';

/**
 * Command name
 *
 * @var string
 * @access private
 */
    private $name;

/**
 * Command data
 *
 * @var array
 * @access private
 */
    private $data;

/**
 * Default command constructor
 * - shorthand to set method
 *
 * @param string $name [= '']
 * @param array $data [= []]
 * @access public
 */
    public function __construct($name = '', array $data = [])
    {
        $this->set ($name, $data);
    }

/**
 * Set command and optionally set data
 * - return if set command is valid
 *
 * @param	string	$name
 * @param	array	$data	[= []]
 * @access	public
 * @return	bool
 */
    public function set($name, array $data = [])
    {
        $this->setName ($name);
        $this->setData ($data);
        return $this->isValid ();
    }

/**
 * Get command name
 *
 * @param void
 * @access public
 * @return string
 */
    public function getName()
    {
        return (string) $this->name;
    }

/**
 * Get command data
 *
 * @param void
 * @access public
 * @return array
 */
    public function getData()
    {
        return (array) $this->data;
    }

/**
 * Check if set name is considered valid
 * - must not be empty
 * - must have a dot
 *
 * @param void
 * @access public
 * @return bool
 */
    public function isValid()
    {
        return !empty ($this->name) && strpos ($this->name, '.') !== false;
    }

/**
 * Set command name
 * - return if set name is considered valid
 *
 * @param string $name
 * @access private
 * @return bool
 */
    private function setName($name)
    {
        $this->name = (string) $name;
        return $this->isValid ();
    }

/**
 * Set command data
 *
 * @param array $data
 * @access private
 * @return bool
 */
    private function setData(array $data)
    {
        $this->data = (array) $data;

        // remove reserved stuff
        if (isset ($this->data[self::VAR_CONTROLLER]))		{ unset ($this->data[self::VAR_CONTROLLER]); }

        return !empty ($this->data);
    }

}
