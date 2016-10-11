<?php

if (php_sapi_name() !== 'cli') {
    throw new Exception("Run the process in the terminal!", 1);
}

require __DIR__ . '/vendor/autoload.php';

echo <<<EOT
\033[31m
_        _______          _________ _______  _______
( \      (  ____ \|\     /|\__   __/(  ____ \(  ___  )
| (      | (    \/( \   / )   ) (   | (    \/| (   ) |
| |      | (__     \ (_) /    | |   | |      | (___) |
| |      |  __)     ) _ (     | |   | |      |  ___  |
| |      | (       / ( ) \    | |   | |      | (   ) |
| (____/\| (____/\( /   \ )___) (___| (____/\| )   ( |
(_______/(_______/|/     \|\_______/(_______/|/     \|

by Tsvetomir Chervenkov

\033[34m
Grammar:
    Opening tag: [
    Closing tag: ]
    Math: + - * /
    Logical < > != ==
    Loop ??
    Exit ^
EOT;

echo <<<EOT
\033[32m
Enter the expression:
EOT;

$string = fopen('php://stdin', 'r');
$expression = trim(fgets($string));

if (empty($expression)) {
    echo <<<EOT
\033[31m

Empty expression! Bye!	( ︶︿︶)

EOT;
exit();
}

$lexica = new \App\Lexica($expression);
$lexica->analyze();
