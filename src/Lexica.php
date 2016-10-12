<?php

namespace App;

class Lexica
{
    /**
     * @var string
     */
    protected $expression;

    /**
     * @var array
     */
    protected $logicalOperators;

    /**
     * @var array
     */
    protected $mathOperator;

    /**
     * @var array
     */
    protected $loopOperator;

    /**
     * @var string
     */
    protected $numberBuffer = '';

    /**
     * @var string
     */
    protected $indentBuffer = '';

    protected $step;

    /**
     * Lexica constructor.
     *
     * @param $expression
     */
    public function __construct($expression)
    {
        $this->expression = $expression;
        $this->logicalOperators = Grammar::logicalOperators();
        $this->mathOperator = Grammar::mathOperators();
        $this->loopOperator = Grammar::loopOperator();
        $this->step = 1;
    }

    /**
     * Do the analyze.
     */
    public function analyze()
    {
        $this->expression = str_split($this->expression);

        for ($i = 0; $i < count($this->expression); $i++) {
            $value = $this->expression[$i];

            // Check if starts with opening tag.
            if ($i == 0) {
                if ($value == Grammar::begin) {
                    Printer::success("Opening tag [ ", $this->step);
                    $this->step++; continue;
                } else {
                    Printer::kill("Syntax Error expected [ for start.", $this->step);
                }
            }

            /*
             *  If last char dump the buffers and check for closing tag.
             */
            if ($i == count($this->expression) - 1 ) {
                if (!empty($this->indentBuffer)) {
                    Printer::success($this->indentBuffer, $this->step); $this->step++;
                }

                if (!empty($this->numberBuffer)) {
                    Printer::success($this->numberBuffer, $this->step); $this->step++;
                }

                if ($value == Grammar::end) {
                    Printer::success("Closing tag ] ", $this->step);
                    continue;
                } else {
                    Printer::kill("Syntax Error expected ] for end got {$value}", $this->step);
                }
            }

            /*
             *  Empty space is ignored.
             */
            if ($value == ' ') {
                continue;
            }

            /*
             * Check the current combined with the next char for valid operator.
             * If fails check the current char alone.
             */
            $operator = $this->isOperator($value . $this->expression[$i + 1]);
            if (!$operator) {
                $operator = $this->isOperator($value);
                if ($operator) {
                    if (!empty($this->indentBuffer)) {
                        Printer::success($this->indentBuffer, $this->step);
                        $this->indentBuffer = '';
                        $this->step++;
                    }

                    if (!empty($this->numberBuffer)) {
                        Printer::success($this->numberBuffer, $this->step);
                        $this->numberBuffer = '';
                        $this->step++;
                    }
                    if ($value == '^') {
                        Printer::exit($this->step);
                    }
                    Printer::success("{$operator} : {$value}", $this->step);
                    $this->step++; continue;
                }
            } else {
                if (!empty($this->indentBuffer)) {
                    Printer::success($this->indentBuffer, $this->step);
                    $this->indentBuffer = '';
                    $this->step++;
                }

                if (!empty($this->numberBuffer)) {
                    Printer::success($this->numberBuffer, $this->step);
                    $this->numberBuffer = '';
                    $this->step++;
                }
                Printer::success("{$operator} : {$value}{$this->expression[$i + 1]}", $this->step);
                $i++; $this->step++; continue;
            }

            /*
             * Check for alphabetic character and put it in buffer
             */
            if (ctype_alpha($value)) {
                if (!empty($this->numberBuffer)) {
                    Printer::success($this->numberBuffer, $this->step);
                    $this->numberBuffer = '';
                    $this->indentBuffer .= $value;
                    $this->step++; continue;
                } else {
                    $this->indentBuffer .= $value;
                }
            }

            /*
             * Check for numeric character and put it in buffer
             */
            if (is_numeric($value)) {
                if (empty($this->indentBuffer)) {
                    $this->numberBuffer .= $value;
                } else {
                    $this->indentBuffer .= $value;
                }
            }
            /*
             * If its not number or letter or valid operator its lexical error.
             */
            if (!ctype_alpha($value) &&
                !is_numeric($value) &&
                !$operator
            ) {
                if (!empty($this->numberBuffer)) {
                    Printer::success($this->numberBuffer,  $this->step);
                }

                if (!empty($this->indentBuffer)) {
                    Printer::success($this->indentBuffer,  $this->step);
                }
                Printer::kill("Lexical Error: {$value}", $this->step);
            }
        }
    }

    /**
     * @param $value
     * @return bool|mixed
     */
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
