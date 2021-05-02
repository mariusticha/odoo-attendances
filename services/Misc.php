<?php

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
    public static function my_print(string $text, int $new_lines = 2, $indent = false): void
    {
        $indented_text = $indent ? "    $text" : $text;
        print_r($indented_text);
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
    public static function my_read(string $text, int $new_lines = 1, bool $indent = true): string
    {
        $indented_text = $indent ? "    $text" : $text;
        $input = readline("$indented_text");
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
    public static function my_switch(string $text, array $options, int $new_lines = 0): array
    {
        // question
        self::my_print($text, 2, true);

        // options
        $keys = [];
        foreach ($options as $iterator => $option) {
            $key = $iterator + 1;
            $keys[] = $key;
            self::my_print("    $key. {$option['text']}", 1, true);
        }
        self::nl();

        $choice = self::my_read(
            "please choose your option (" . implode(', ', $keys) . "): ",
            1,
            false
        );

        $result = $options[$choice - 1];
        self::my_print("your choice: {$result['text']}");

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
}
