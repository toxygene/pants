<?php
/**
 *
 */

namespace Pants\FileSet;

/**
 *
 */
class Pattern
{

    /**
     * Pattern
     * @var string
     */
    protected $_pattern;

    /**
     * Get the pattern
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->_pattern;
    }

    /**
     * Check if an input is a match to this pattern
     *
     * @param string $input
     * @return boolean
     */
    public function isMatch($input)
    {
        return true;
    }

    /**
     * Set the pattern
     *
     * @param string $pattern
     * @return Pattern
     */
    public function setPattern($pattern)
    {
        $this->_pattern = $pattern;
        return $this;
    }

}
