<?php

class Misc
{
    /**
     * prints the given text to cli and adds new lines for better readability
     *
     * @param   string  $text
     * @param   int     $new_lines  amount of new lines (default: 2)
     *
     * @return  void                [return description]
     */
    public static function my_print(string $text, int $new_lines = 2): void
    {
        print_r($text);
        self::nl($new_lines);
    }

    /**
     * adds a readline to cli and adds new lines for better readability
     *
     * @param   string  $text
     * @param   int     $new_lines  amount of new lines (default: 2)
     *
     * @return  void                [return description]
     */
    public static function my_read(string $text, int $new_lines = 1): string
    {
        $input = readline("    $text");
        self::nl($new_lines);

        return $input;
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
}
