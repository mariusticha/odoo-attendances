<?php

use Services\Misc;

if (!function_exists('my_print')) {
    function my_print(string $text, int $new_lines = 2, $indent = false, string $type = '')
    {
        return Misc::my_print($text, $new_lines, $indent,  $type);
    }
}
if (!function_exists('my_read')) {
    function my_read(string $text, int $new_lines = 1, bool $indent = true, string $type = '')
    {
        return Misc::my_read($text, $new_lines, $indent,  $type);
    }
}
if (!function_exists('my_switch')) {
    function my_switch(string $text, array $options, ?int $debug = null, int $new_lines = 0)
    {
        return Misc::my_switch($text, $options, $debug, $new_lines);
    }
}
if (!function_exists('nl')) {
    function nl(int $amount = 1)
    {
        return Misc::nl($amount);
    }
}
if (!function_exists('dd')) {
    function dd(...$variables)
    {
        return Misc::dd(...$variables);
    }
}
if (!function_exists('style')) {
    function style(?string $text, string $type = 'success')
    {
        return Misc::style($text,  $type);
    }
}
if (!function_exists('italic')) {
    function italic(?string $text)
    {
        return Misc::italic($text);
    }
}
