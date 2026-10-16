<?php

declare(strict_types=1);


namespace betterlite\bedwars\game;


use pocketmine\scheduler\Task;
use betterlite\bedwars\BedWars;
use betterlite\bedwars\game\stage\PlayingStage;

class GameHeartbeat extends Task {

    private int $current_tick = 0;

    public function onRun(): void {
        $this->current_tick++;

        foreach(BedWars::getInstance()->getGameManager()->getGames() as $game) {
            if($game->getStage() instanceof PlayingStage) {
                $game->tickGenerators();
            }

            if($this->current_tick % 20 === 0) {
                $game->getStage()->tick();
            }
        }
    }

}