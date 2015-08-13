<?php

/**
 * DoctrineExtensions Mysql Function Pack.
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so I can send you a copy immediately.
 *
 * @see https://github.com/beberlei/DoctrineExtensions
 */
namespace Fuz\AppBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

class FindInSet extends FunctionNode
{
    public $needle = null;
    public $haystack = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->needle = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->haystack = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'FIND_IN_SET('.
           $this->needle->dispatch($sqlWalker).', '.
           $this->haystack->dispatch($sqlWalker).
           ')';
    }
}
