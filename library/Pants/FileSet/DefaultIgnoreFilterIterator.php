<?php
/**
 *
 */

namespace Pants\FileSet;

use RecursiveFilterIterator,
    RecursiveIterator;

/**
 *
 */
class DefaultIgnoreFilterIterator extends RecursiveFilterIterator
{

    protected $_patterns = array(
        "^\.git$",
        "^\.svn$"
    );

    public function __construct(RecursiveIterator $iterator, $patterns = array())
    {
        if ($patterns) {
            $this->_patterns = $patterns;
        }

        parent::__construct($iterator);
    }

    public function accept()
    {
        $pattern = "";
        foreach ($this->getPatterns() as $p) {
            if ($pattern) {
                $pattern .= "|";
            }

            $pattern .= "({$p})";
        }

        return preg_match("#{$pattern}#", $this->getInnerIterator()->current()->getFilename()) == 0;
    }

    public function getPatterns()
    {
        return $this->_patterns;
    }

}
