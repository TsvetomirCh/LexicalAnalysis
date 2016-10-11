<?php

namespace App;

class Grammar
{
    const begin = '[';
    const end = ']';

    public static function mathOperators()
    {
        return [
            '=' => 'Assign Operator',
            '+' => 'Addition Operator',
            '-' => 'Subtraction Operator',
            '*' => 'Multiply Operator',
            '/' => 'Division Operator'
        ];
    }

    public static function logicalOperators()
    {
        return [
            '<' => 'Less Than Operator',
            '>' => 'Greater Than Operator',
            '==' => 'Equal Operator',
            '!=' => 'Not Equal Operator',
            '^'  => 'Exit Operator'
        ];
    }

    public static function loopOperator()
    {
        return [
            '??' => 'Loop Operator'
        ];
    }
}