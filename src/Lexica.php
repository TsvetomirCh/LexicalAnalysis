<?php

namespace App;

class Lexica
{
    protected $expression;
    protected $logicalOperators;
    protected $mathOperator;
    protected $loopOperator;

    protected $number = '';
    protected $indent = '';

    public function __construct($expression)
    {
        $this->expression = $expression;
        $this->logicalOperators = Grammar::logicalOperators();
        $this->mathOperator = Grammar::mathOperators();
        $this->loopOperator = Grammar::loopOperator();
    }

    public function analyze()
    {
        $this->expression = str_split($this->expression);
        $step = 1;

        for ($i = 0; $i < count($this->expression); $i++) {
            $value = $this->expression[$i];

            if ($i == 0) {
                if ($value == Grammar::begin) {
                    Printer::success("Opening tag [ ", $step);
                    $step++; continue;
                } else {
                    Printer::kill("Syntax Error expected [ for start.", $step);
                }
            }

            if ($i == count($this->expression) - 1 ) {
                if (!empty($this->indent)) {
                    Printer::success($this->indent, $step); $step++;
                }

                if (!empty($this->number)) {
                    Printer::success($this->number, $step); $step++;
                }

                if ($value == Grammar::end) {
                    Printer::success("Closing tag ] ", $step);
                    continue;
                } else {
                    Printer::kill("Syntax Error expected ] for end got {$value}", $step);
                }
            }

            if ($value == ' ') {
                continue;
            }

            $operator = $this->isOperator($value . $this->expression[$i + 1]);
            if (!$operator) {
                $operator = $this->isOperator($value);
                if ($operator) {
                    if (!empty($this->indent)) {
                        Printer::success($this->indent, $step);
                        $this->indent = '';
                        $step++;
                    }

                    if (!empty($this->number)) {
                        Printer::success($this->number, $step);
                        $this->number = '';
                        $step++;
                    }
                    if ($value == '^') {
                        Printer::exit($step);
                    }
                    Printer::success("{$operator} : {$value}", $step);
                    $step++; continue;
                }
            } else {
                if (!empty($this->indent)) {
                    Printer::success($this->indent, $step);
                    $this->indent = '';
                    $step++;
                }

                if (!empty($this->number)) {
                    Printer::success($this->number, $step);
                    $this->number = '';
                    $step++;
                }
                Printer::success("{$operator} : {$value}{$this->expression[$i + 1]}", $step);
                $i++; $step++; continue;
            }

            if (ctype_alpha($value)) {
                if (!empty($this->number)) {
                    Printer::success($this->number, $step);
                    $this->number = '';
                    $this->indent .= $value;
                    $step++; continue;
                } else {
                    $this->indent .= $value;
                }
            }

            if (is_numeric($value)) {
                if (empty($this->indent)) {
                    $this->number .= $value;
                } else {
                    $this->indent .= $value;
                }
            }
            if ( !ctype_alpha($value) &&
                !is_numeric($value) &&
                !$operator
            ) {
                if (!empty($this->number)) {
                    Printer::success($this->number,  $step);
                }

                if (!empty($this->indent)) {
                    Printer::success($this->indent,  $step);
                }
                Printer::kill("Lexical Error: {$value}", $step);
            }
        }
    }

    private function isOperator($value)
    {
        if (array_key_exists($value, $this->logicalOperators)) {
            return $this->logicalOperators[$value];
        }

        if (array_key_exists($value, $this->mathOperator)) {
            return $this->mathOperator[$value];
        }

        if (array_key_exists($value, $this->loopOperator)) {
            return $this->loopOperator[$value];
        }

        return false;
    }
}
