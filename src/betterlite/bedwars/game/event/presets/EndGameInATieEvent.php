<?php

declare(strict_types=1);


namespace betterlite\bedwars\game\event\presets;


use betterlite\bedwars\game\event\Event;
use betterlite\bedwars\game\stage\EndingStage;

class EndGameInATieEvent extends Event {

    public function __construct() {
        parent::__construct("Tie", 10);
    }

    public function end(): void {
        $this->game->setStage(new EndingStage());
    }

    public function getNextEvent(): ?Event {
        return null;
    }

}