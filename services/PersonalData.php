<?php

namespace Services;

class PersonalData
{
    public function __construct()
    {
    }

    public function check(array $personal_data): array
    {
        $person_correct = my_read(
            "wait.. - {$personal_data['name']} - this is you, right? (Y/n) "
        );

        if ($person_correct === 'n') {

            my_print("d'oh. but no problem, let's set up your personal data again.");

            return $this->set_up_personal_data();
        }

        $contract_correct = my_read("... and your contract type is still {$personal_data['contract']['text']}? (Y/n) ");

        if ($contract_correct === 'n') {

            my_print("d'oh. so let's update your contract.");

            $personal_data['contract'] = $this->set_up_contract();

            $this->store($personal_data);
        }

        my_print("nice, let's go on.");

        return $personal_data;
    }

    public function set_up_personal_data(): array
    {
        $first_name = my_read('whats your first name? ', 0);
        $last_name = my_read('whats your last name? ', 0);
        $contract = $this->set_up_contract();
        $name = "$first_name $last_name";

        $personal_data = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'name' => $name,
            'contract' => $contract,
        ];

        my_print("nice to meet you $name 👋");

        return $this->store($personal_data);
    }

    private function set_up_contract(): array
    {
        return my_switch('whats your contract type? ', [
            [
                'value' => 40,
                'text' => 'full time /40h',
            ],
            [
                'value' => 36,
                'text' => 'reduced full time /36h',
            ],
            [
                'value' => 20,
                'text' => 'part time /20h',
            ],
            [
                'value' => 15,
                'text' => 'student assistant /15h',
            ],
        ]);
    }

    private function store(array $personal_data): array
    {
        $store = my_read("do you want to store your personal data for the next time? (Y/n) ");

        if ($store === 'n') {
            my_print("ok, your data won't be stored. let's move on.");

            return [];
        }

        file_put_contents('personal_data.json', json_encode($personal_data));
        my_print("great, your data has been stored for the next session. so, let's move on.");

        return $personal_data;
    }
}
