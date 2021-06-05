<?php

use Services\Misc;

if (!function_exists('my_print')) {
    function my_print($text, $new_lines, $indent,  $type)
    {
        Misc::my_print($text, $new_lines, $indent,  $type);
    }
}
if (!function_exists('my_read')) {
    function my_read($text, $new_lines, $indent,  $type)
    {
        Misc::my_read($text, $new_lines, $indent,  $type);
    }
}
if (!function_exists('my_switch')) {
    function my_switch($text, $options, $new_lines)
    {
        Misc::my_switch($text, $options, $new_lines);
    }
}
if (!function_exists('nl')) {
    function nl($amount)
    {
        Misc::nl($amount);
    }
}
if (!function_exists('dd')) {
    function dd(...$variables)
    {
        Misc::dd(...$variables);
    }
}
if (!function_exists('style')) {
    function style($text,  $type)
    {
        Misc::style($text,  $type);
    }
}
