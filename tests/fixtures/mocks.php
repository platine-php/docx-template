<?php

declare(strict_types=1);

namespace Platine\DocxTemplate\Convertor;

$mock_function_exists_to_false = false;
$mock_function_exists_to_true = false;
$mock_passthru_to_exitcode_error = false;
$mock_passthru_to_empty = false;

function function_exists(string $key)
{
    global $mock_function_exists_to_false,
       $mock_function_exists_to_true;
    if ($mock_function_exists_to_false) {
        return false;
    }

    if ($mock_function_exists_to_true) {
        return true;
    }

    return \function_exists($key);
}

function passthru(string $cmd, &$return_var)
{
    global $mock_passthru_to_exitcode_error,
           $mock_passthru_to_empty;
    if ($mock_passthru_to_exitcode_error) {
        $return_var = 89999;
        return;
    }

    if ($mock_passthru_to_empty) {
        return;
    }

    return \passthru($cmd, $return_var);
}
