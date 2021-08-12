<?php

namespace Services;

class Misc
{
    /**
     * prints the given text to cli and adds styling
     *
     * @param   string  $text
     * @param   int     $new_lines  amount of new lines (default: 2)
     *
     * @return  void                [return description]
     */
    public static function my_print(string $text, int $new_lines = 2, $indent = false, string $type = ''): void
    {
        $indented_text = $indent ? "    $text" : $text;

        print_r(
            $type ?
                self::style($indented_text, $type) :
                $indented_text
        );
        self::nl($new_lines);
    }

    /**
     * adds a readline to cli and adds styling
     *
     * @param   string  $text
     * @param   int     $new_lines  amount of new lines (default: 1)
     *
     * @return  void                [return description]
     */
    public static function my_read(string $text, int $new_lines = 1, bool $indent = false, string $type = ''): string
    {
        // show question in style
        self::my_print($text, 2, $indent, $type);

        // read answer
        $input = readline('    > ');
        self::nl($new_lines);

        return $input;
    }

    /**
     * adds a choice of options and adds styling
     *
     * @param   string  $text
     * @param   array   $options    [['value' => $val, 'text' => $text]]
     * @param   int     $new_lines  0
     *
     * @return  array               ['value' => $val, 'text' => $text]
     */
    public static function my_switch(string $text, array $options, ?int $debug = null, int $new_lines = 0,): array
    {
        // question
        self::my_print($text, 2);

        // show options
        $keys = [];
        foreach ($options as $iterator => $option) {
            $key = $iterator + 1;
            $keys[] = $key;
            self::my_print("    $key. {$option['text']}", 1, true);
        }
        self::nl();

        // ask for choice
        $choice = self::my_read(
            "please choose your option " . italic("(" . implode(', ', $keys) . ")"),
            1,
            false
        );

        if (!is_null($debug) && $choice === '') {

            return $options[$debug];
        }

        $chosen_key = intval($choice) - 1;

        if (!array_key_exists($chosen_key, $options)) {
            self::my_print(
                "your input '$choice' was invalid, please choose only from these options: " . implode(', ', $keys),
                2,
                false,
                'warning'
            );
            return self::my_switch($text, $options, $new_lines);
        }

        // print result
        $result = $options[$chosen_key];
        $styled = style($result['text']);
        self::my_print("your choice: $styled");
        self::nl($new_lines);

        return $result;
    }

    /**
     * adds the given amount as new lines to cli
     *
     * @param   int   $amount
     *
     * @return  void
     */
    public static function nl(int $amount = 1): void
    {
        $new_lines = '';
        while ($amount > 0) {
            $new_lines .= "\n";
            $amount -= 1;
        }
        print_r($new_lines);
    }

    /**
     * dump and die
     *
     * @param   mixed   $variables
     * @param   bool    $exit  exits app
     *
     * @return  string  styleed string
     */
    public static function dd(...$variables): void
    {
        foreach ($variables as $key => $variable) {
            self::my_print(" -- $key --", 1, false, 'warning');
            var_export($variable);
            self::nl(2);
        }
        exit;
    }

    /**
     * styles the given text
     *
     * @param   string  $text
     * @param   string  $type  styleing type
     *
     * @return  string  styleed string
     */
    public static function style(?string $text, string $type = 'success'): string
    {
        return match ($type) {
            'error' => "\033[31m$text\033[0m",
            'success' => "\033[32m$text\033[0m",
            'warning' => "\033[33m$text\033[0m",
            'info' => "\033[36m$text\033[0m",
            'bold' => "\033[1m$text\033[0m",
            'italic' => "\033[3m$text\033[0m",
            'underline' => "\033[4m$text\033[0m",
            'strikethrough' => "\033[9m$text\033[0m",
            default => $text,
        };
    }

    /**
     * styles the given text in italic
     *
     * @param   string  $text
     * @param   string  $type  styleing type
     *
     * @return  string  styleed string
     */
    public static function italic(?string $text): string
    {
        return "\033[3m$text\033[0m";
    }
}
