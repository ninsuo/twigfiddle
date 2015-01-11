<?php

namespace Fuz\AppBundle\Twig\Extension;

class PcreExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return array (
                new \Twig_SimpleFilter('preg_filter',
                   function($subject, $pattern, $replacement, $limit = -1, &$count = null)
                   {
                       return preg_filter($pattern, $replacement, $subject, $limit, $count);
                   }),
                new \Twig_SimpleFilter('preg_grep',
                   function($input, $pattern, $flags = 0)
                   {
                       return preg_grep($pattern, $input, $flags);
                   }),
                new \Twig_SimpleFilter('preg_match_all',
                   function($subject, $pattern, array &$matches = null, $flags = PREG_PATTERN_ORDER, $offset = 0)
                   {
                       return preg_match_all($pattern, $subject, $matches, $flags, $offset);
                   }),
                new \Twig_SimpleFilter('preg_match',
                   function($subject, $pattern, array &$matches = null, $flags = 0, $offset = 0)
                   {
                       return preg_match($pattern, $subject, $matches, $flags, $offset);
                   }),
                new \Twig_SimpleFilter('preg_quote', 'preg_quote'),
                new \Twig_SimpleFilter('preg_replace_callback',
                   function($subject, $pattern, $callback, $limit = -1, &$count = null)
                   {
                       return preg_replace_callback($pattern, $callback, $subject, $limit, $count);
                   }),
                new \Twig_SimpleFilter('preg_replace',
                   function($subject, $pattern, $replacement, $limit = -1, &$count = null)
                   {
                       return preg_replace($pattern, $replacement, $subject, $limit, $count);
                   }),
                new \Twig_SimpleFilter('preg_split',
                   function($subject, $pattern, $limit = -1, $flags = 0)
                   {
                       return preg_split($pattern, $subject, $limit, $flags);
                   }),
        );
    }

    public function getFunctions()
    {
        return array (
                new \Twig_SimpleFunction('preg_last_error', 'preg_last_error'),
        );
    }

    public function getName()
    {
        return 'FuzAppBundle:Pcre';
    }

}
