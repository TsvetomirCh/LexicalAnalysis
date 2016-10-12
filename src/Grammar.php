<?php

namespace App;

class Grammar
{
    /**
     * Opening tag
     */
    const begin = '[';

    /**
     * Closing tag
     */
    const end = ']';

    /**
     * @return array
     */
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

    /**
     * @return array
     */
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

    /**
     * @return array
     */
    public static function loopOperator()
    {
        return [
            '??' => 'Loop Operator'
        ];
    }
}