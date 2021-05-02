<?php
require_once('services/Misc.php');
class PersonalData
{
    public function __construct()
    {
    }

    public function check($personal_data): mixed
    {
        $personal_data_correct = Misc::my_read("wait.. this is you, right? (Y/n) ");

        if ($personal_data_correct === 'n') {

            Misc::my_print("d'oh. so let's setup your personal data.");

            return $this->set_up_personal_data();
        }

        Misc::my_print("nice, let's go on.");

        return $personal_data;
    }

    public function set_up_personal_data(): array
    {

        $first_name = Misc::my_read("whats your first name? ", 0);
        $last_name = Misc::my_read("whats your last name? ");
        $name = "$first_name $last_name";

        $personal_data = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'name' => $name,
            'contract' => '',
        ];

        Misc::my_print("nice to meet you $name");

        $store = Misc::my_read("do you want to store your personal data for the next time? (Y/n) ");

        if ($store === 'n') {
            Misc::my_print("ok, your data will not be stored. let's move on.");

            return $personal_data;
        }

        $this->store($personal_data);
        Misc::my_print("great, your data has been stored for the next session. so, let's move on.");

        return $personal_data;
    }

    public function store($personal_data): void
    {
        file_put_contents('personal_data.json', json_encode($personal_data));
    }
}
