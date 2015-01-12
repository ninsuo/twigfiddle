<?php

// -------------------------------------------------
// Complete support of GROUP_CONCAT in Doctrine2
// -------------------------------------------------
// Original Article: http://habrahabr.ru/post/181666/
// Automated translation to English: http://sysmagazine.com/posts/181666/
// Original github commit: https://github.com/denisvmedia/DoctrineExtensions/blob/d1caf21cd7c71cc557e60c26e9bf25323a194dd1/lib/DoctrineExtensions/Query/Mysql/GroupConcat.php

/**
 * DoctrineExtensions Mysql Function Pack
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so I can send you a copy immediately.
 */

namespace Fuz\AppBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode,
    Doctrine\ORM\Query\Lexer;

/**
 * Full support for:
 *
 * GROUP_CONCAT([DISTINCT] expr [,expr ...]
 *              [ORDER BY {unsigned_integer | col_name | expr}
 *                  [ASC | DESC] [,col_name ...]]
 *              [SEPARATOR str_val])
 *
 */
class GroupConcat extends FunctionNode
{

    public $isDistinct = false;
    public $pathExp = null;
    public $separator = null;
    public $orderBy = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $lexer = $parser->getLexer();
        if ($lexer->isNextToken(Lexer::T_DISTINCT))
        {
            $parser->match(Lexer::T_DISTINCT);

            $this->isDistinct = true;
        }

        // first Path Expression is mandatory
        $this->pathExp = array ();
        $this->pathExp[] = $parser->SingleValuedPathExpression();

        while ($lexer->isNextToken(Lexer::T_COMMA))
        {
            $parser->match(Lexer::T_COMMA);
            $this->pathExp[] = $parser->StringPrimary();
        }

        if ($lexer->isNextToken(Lexer::T_ORDER))
        {
            $this->orderBy = $parser->OrderByClause();
        }

        if ($lexer->isNextToken(Lexer::T_IDENTIFIER))
        {
            if (strtolower($lexer->lookahead['value']) !== 'separator')
            {
                $parser->syntaxError('separator');
            }
            $parser->match(Lexer::T_IDENTIFIER);

            $this->separator = $parser->StringPrimary();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        $result = 'GROUP_CONCAT(' . ($this->isDistinct ? 'DISTINCT ' : '');

        $fields = array ();
        foreach ($this->pathExp as $pathExp)
        {
            $fields[] = $pathExp->dispatch($sqlWalker);
        }

        $result .= sprintf('%s', implode(', ', $fields));

        if ($this->orderBy)
        {
            $result .= ' ' . $sqlWalker->walkOrderByClause($this->orderBy);
        }

        if ($this->separator)
        {
            $result .= ' SEPARATOR ' . $sqlWalker->walkStringPrimary($this->separator);
        }

        $result .= ')';

        return $result;
    }

}
