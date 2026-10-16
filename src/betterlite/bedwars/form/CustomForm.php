<?php

declare(strict_types=1);


namespace betterlite\bedwars\form;


use dresnite\EasyUI\element\Dropdown;
use dresnite\EasyUI\element\Option;
use dresnite\EasyUI\variant\CustomForm as EasyUICustomForm;
use betterlite\bedwars\game\map\Mode;
use betterlite\bedwars\utils\GameUtils;

class CustomForm extends EasyUICustomForm {

    protected function addSelectModeDropdown(): void {
        $dropdown = new Dropdown("Select the mode:");
        foreach(Mode::cases() as $mode) {
            $dropdown->addOption(new Option((string) $mode->value, $mode->toString()));
        }
        $this->addElement("players_per_team", $dropdown);
    }

}