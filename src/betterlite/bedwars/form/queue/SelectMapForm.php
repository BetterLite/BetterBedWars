<?php

declare(strict_types=1);


namespace betterlite\bedwars\form\queue;


use dresnite\EasyUI\variant\SimpleForm;
use betterlite\bedwars\BedWars;
use betterlite\bedwars\form\queue\element\PlayGameButton;
use betterlite\bedwars\game\map\MapFactory;
use betterlite\bedwars\game\map\Mode;

class SelectMapForm extends SimpleForm {

    private Mode $players_per_team;

    public function __construct(Mode $players_per_team) {
        $this->players_per_team = $players_per_team;
        parent::__construct("Select a map!");
    }

    protected function onCreation(): void {
        foreach(MapFactory::getMapsByPlayers($this->players_per_team) as $map) {
            $this->addButton(new PlayGameButton($map->getName(), BedWars::getInstance()->getGameManager()->findGame($map)));
        }
    }

}