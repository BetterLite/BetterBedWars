<?php

declare(strict_types=1);


namespace betterlite\bedwars\form\queue;


use dresnite\EasyUI\element\Button;
use dresnite\EasyUI\variant\SimpleForm;
use pocketmine\player\Player;
use betterlite\bedwars\BedWars;
use betterlite\bedwars\form\queue\element\PlayGameButton;
use betterlite\bedwars\game\map\Mode;
use betterlite\bedwars\utils\GameUtils;

class PlayBedwarsForm extends SimpleForm {

    private Mode $players_per_team;

    public function __construct(Mode $players_per_team) {
        $this->players_per_team = $players_per_team;
        parent::__construct("Play bedwars " . GameUtils::getMode($players_per_team));
    }

    protected function onCreation(): void {
        $this->addButton(new PlayGameButton("Random map", BedWars::getInstance()->getGameManager()->findRandomGame($this->players_per_team)));
        $this->addSelectMapButton();
    }

    private function addSelectMapButton(): void {
        $button = new Button("Select a map");
        $button->setSubmitListener(function(Player $player) {
            $player->sendForm(new SelectMapForm($this->players_per_team));
        });
        $this->addButton($button);
    }

}