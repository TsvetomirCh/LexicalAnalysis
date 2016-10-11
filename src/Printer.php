<?php

namespace App;

class Printer
{
    public static function kill($value, $step)
    {
        echo <<<EOT
\033[31m

Step: [$step] -> $value

EOT;
        exit();
    }

    public static function success($value, $step)
    {
        echo <<<EOT
\033[32m

Step: [$step] -> $value
EOT;
        return true;
    }

    public static function exit($step)
    {
        echo <<<EOT
\033[34m

Step: [$step] -> EXIT with ^

EOT;
        exit();
    }
}