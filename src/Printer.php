<?php

namespace App;

class Printer
{
    /**
     * Print error and kill the script.
     *
     * @color red
     * @param $value
     * @param $step
     */
    public static function kill($value, $step)
    {
        echo <<<EOT
\033[31m

Step: [$step] -> $value

EOT;
        exit();
    }

    /**
     * Print success msg.
     *
     * @color green
     * @param $value
     * @param $step
     * @return bool
     */
    public static function success($value, $step)
    {
        echo <<<EOT
\033[32m

Step: [$step] -> $value
EOT;
        return true;
    }

    /**
     * Print when exit char ^ and kill the script
     *
     * @color blue
     * @param $step
     */
    public static function exit($step)
    {
        echo <<<EOT
\033[34m

Step: [$step] -> EXIT with ^

EOT;
        exit();
    }
}