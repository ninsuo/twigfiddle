<?php

namespace Fuz\AppBundle\Twig\Extension;

/**
 * This extension makes results more human-readable:
 * - replaces multiple newlines by at last double newlines
 * - remove blank space at the top, left, bottom
 * - indents result with regular number of spaces
 */
class PrettifyExtension extends \Twig_Extension
{

    const INDENT = '   ';

    protected $result;

    public function getFilters()
    {
        return array (
                new \Twig_SimpleFilter('prettify', array ($this, 'prettify')),
        );
    }

    public function prettify($uglyResult)
    {
        $this->result = explode("\n", $uglyResult);
        $this
           ->prettifyVerticalSpace()
           ->prettifyHorizontalSpace()
        ;
        $prettyResult = implode("\n", $this->result);
        $this->result = null;
        return $prettyResult;
    }

    protected function prettifyVerticalSpace()
    {
        $blank = 0;
        $top = true;

        foreach ($this->result as $index => $line)
        {
            if (strlen(str_replace(array (" ", "\t", "\n", "\r"), '', $line)) == 0)
            {
                $this->result[$index] = '';
                $blank++;
                if (($top) || ($blank > 1))
                {
                    unset($this->result[$index]);
                }
            }
            else
            {
                $blank = 0;
                $top = false;
            }
        }

        foreach (array_reverse($this->result, true) as $index => $line)
        {
            if (strlen(str_replace(array (" ", "\t", "\n", "\r"), '', $line)) == 0)
            {
                unset($this->result[$index]);
            }
            else
            {
                break;
            }
        }

        return $this;
    }

    protected function prettifyHorizontalSpace()
    {
        $spaces = array ();
        foreach ($this->result as $index => $line)
        {
            if (strlen($line))
            {
                $matches = array ();
                preg_match("/^\s*/", $line, $matches);
                $spaces[$index] = strlen(reset($matches));
            }
        }

        $min = min($spaces);
        foreach ($this->result as $index => $line)
        {
            $this->result[$index] = substr($line, $min);
        }

        asort($spaces);
        $deeps = array_values(array_unique($spaces));

        foreach ($this->result as $index => $line)
        {
            if (strlen($line))
            {
                $deep = array_search($spaces[$index], $deeps);
                $this->result[$index] = preg_replace("/^\s*/", str_repeat(self::INDENT, $deep), $line);
            }
        }

        return $this;
    }

    public function getName()
    {
        return 'FuzAppBundle:Prettify';
    }

}
