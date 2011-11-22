<?php
/**
 *
 */

namespace Pants\Fileset;

/**
 *
 */
class Pattern
{

    /**
     *
     */
    protected $_pattern;

    /**
     *
     */
    public function getPattern()
    {
        return $this->_pattern;
    }

    /**
     *
     */
    public function setPattern($pattern)
    {
        $this->_pattern = $pattern;
        return $this;
    }

}
