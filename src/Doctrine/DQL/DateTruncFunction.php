<?php

namespace App\Doctrine\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;

final class DateTruncFunction extends FunctionNode
{
    public $firstDateExpression = null;
    public $secondDateExpression = null;

    public function getSql(SqlWalker $sqlWalker)
    {
        return 'date_trunc ('.$sqlWalker->walkStringPrimary($this->firstDateExpression).','.$sqlWalker->walkArithmeticPrimary($this->secondDateExpression).')';
    }

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->firstDateExpression = $parser->StringExpression();
        $parser->match(Lexer::T_COMMA);

        $this->secondDateExpression = $parser->ArithmeticExpression();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
