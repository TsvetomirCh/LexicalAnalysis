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
    protected $identifierBuffer = '';

    /**
     * @var int
     */
    protected $step;

    /**
     * @var bool
     */
    protected $shouldFollowIndent = false;

    /**
     * @var bool
     */
    protected $shouldFollowOpeningBracket = false;

    /**
     * @var bool
     */
    protected $shouldFollowClosingBracket = false;

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

            if ($this->shouldFollowOpeningBracket == true) {
                if ($value != Grammar::openingBracket) {
                    Printer::kill("Syntax Error expected ( ", $this->step);
                } else {
                    $this->shouldFollowOpeningBracket = false;
                    Printer::success(Grammar::openingBracket, $this->step); $this->step++;
                    $this->shouldFollowIndent = true;
                    continue;
                }
            } else if ($this->shouldFollowClosingBracket == true) {
                if ($value != Grammar::closingBracket) {
                    Printer::kill("Syntax Error expected ) ", $this->step);
                }
                else {
                    $this->shouldFollowClosingBracket = false;
                    Printer::success(Grammar::closingBracket, $this->step); $this->step++;
                    continue;
                }
            }

            /*
             *  If last char dump the buffers and check for closing tag.
             */
            if ($i == count($this->expression) - 1 ) {

                if ($this->shouldFollowOpeningBracket) {
                    Printer::kill("Expected ( got end of expression.",$this->step);
                }

                if (!empty($this->identifierBuffer)) {
                    if ($this->shouldFollowIndent) {
                        $this->shouldFollowClosingBracket = true;
                    }
                    Printer::success($this->identifierBuffer, $this->step); $this->step++;
                } else if($this->shouldFollowIndent) {
                    Printer::kill("Expected identifier got end of expression.",$this->step);
                }

                if ($this->shouldFollowClosingBracket) {
                    Printer::kill("Expected ) got end of expression.",$this->step);
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
            } else {
                // last char
                if ($value == Grammar::end) {
                    Printer::kill("Got ] not in the end of the expression. ", $this->step);
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
                    if ($this->shouldFollowIndent == true && empty($this->identifierBuffer)) {
                        Printer::kill("Got {$operator} should have identifier. ", $this->step);
                    }
                    if (!empty($this->identifierBuffer)) {
                        if ($this->shouldFollowIndent) {
                            $this->shouldFollowClosingBracket = true;
                        }
                        Printer::success($this->identifierBuffer, $this->step);
                        $this->identifierBuffer = '';
                        $this->step++;
                        $this->shouldFollowIndent = false;
                    } else {
                        if ($this->shouldFollowIndent) {
                            Printer::kill("Expected identifier got {$value} ", $this->step);
                        }
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
                if ($this->shouldFollowIndent == true && empty($this->identifierBuffer)) {
                    Printer::kill("Got {$operator} should have identifier. ", $this->step);
                }
                if (!empty($this->identifierBuffer)) {
                    if ($this->shouldFollowIndent) {
                        $this->shouldFollowClosingBracket = true;
                    }
                    Printer::success($this->identifierBuffer, $this->step);
                    $this->identifierBuffer = '';
                    $this->step++;
                    $this->shouldFollowIndent = false;
                } else {
                    if ($this->shouldFollowIndent) {
                        Printer::kill("Expected identifier got {$value} ", $this->step);
                    }
                }

                if (!empty($this->numberBuffer)) {
                    Printer::success($this->numberBuffer, $this->step);
                    $this->numberBuffer = '';
                    $this->step++;
                }

                if ($value . $this->expression[$i + 1] == key($this->loopOperator)) {
                    $this->shouldFollowOpeningBracket = true;
                }

                if ($this->shouldFollowIndent == true) {
                    Printer::kill("Got {$operator} should have identifier. ", $this->step);
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
                    $this->identifierBuffer .= $value;
                    $this->step++; continue;
                } else {
                    $this->identifierBuffer .= $value;
                }
            }

            /*
             * Check for numeric character and put it in buffer
             */
            if (is_numeric($value)) {
                if (empty($this->identifierBuffer)) {
                    $this->numberBuffer .= $value;
                } else {
                    $this->identifierBuffer .= $value;
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

                if (!empty($this->identifierBuffer)) {
                    Printer::success($this->identifierBuffer,  $this->step); $this->step++;
                    $this->identifierBuffer = '';
                    if ($this->shouldFollowIndent) {
                        $this->shouldFollowIndent = false;
                        $this->shouldFollowClosingBracket = true;
                    }

                    if ($this->shouldFollowClosingBracket) {
                        if ($value == Grammar::closingBracket) {
                            $this->shouldFollowClosingBracket = false;
                            Printer::success(Grammar::closingBracket, $this->step); $this->step++;
                            continue;
                        } else {
                            Printer::kill("Syntax Error expected ) ", $this->step);
                        }
                    }
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
