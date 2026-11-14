<?php

declare(strict_types=1);


namespace betterlite\bedwars\game;


use pocketmine\scheduler\Task;
use betterlite\bedwars\BedWars;
use betterlite\bedwars\game\stage\PlayingStage;
use Throwable;

class GameHeartbeat extends Task {

    private int $current_tick = 0;

    /** @var true[] */
    private array $broken_games = [];

    public function onRun(): void {
        $this->current_tick++;

        foreach(BedWars::getInstance()->getGameManager()->getGames() as $id => $game) {
            if(isset($this->broken_games[$id])) {
                continue;
            }

            try {
                if($game->getStage() instanceof PlayingStage) {
                    $game->tickGenerators();
                }

                if($this->current_tick % 20 === 0) {
                    $game->getStage()->tick();
                }
            } catch(Throwable $exception) {
                // A broken game must never take the whole heartbeat (and with it every
                // other running game) down: log it once and stop ticking that game.
                $this->broken_games[$id] = true;
                BedWars::getInstance()->getLogger()->error("Game #" . $game->getId() . " on map \"" . $game->getMap()->getName() . "\" stopped ticking.");
                BedWars::getInstance()->getLogger()->logException($exception);
            }
        }
    }

}