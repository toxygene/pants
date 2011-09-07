<?php
/**
 *
 */

namespace Pants\Getopt;

use Exception;

/**
 *
 */
class Exception extends Exception
{

    /**
     * Usage
     *
     * @var string
     */
    protected $usage = '';

    /**
     * Constructor
     *
     * @param string $message
     * @param string $usage
     * @return void
     */
    public function __construct($message, $usage = '')
    {
        $this->usage = $usage;
        parent::__construct($message);
    }

    /**
     * Returns the usage
     *
     * @return string
     */
    public function getUsageMessage()
    {
        return $this->usage;
    }

}
