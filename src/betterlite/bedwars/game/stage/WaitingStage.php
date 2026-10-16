<?php

declare(strict_types=1);


namespace betterlite\bedwars\game\stage;


use betterlite\bedwars\game\stage\trait\JoinableTrait;
use betterlite\bedwars\session\Session;

class WaitingStage extends Stage {
    use JoinableTrait {
        onJoin as onSessionJoin;
    }

    public function onJoin(Session $session): void {
        $this->onSessionJoin($session);
        $this->startIfReady();
    }

    private function startIfReady(): void {
        $map = $this->game->getMap();
        $count = $this->game->getPlayersCount();

        if($count > $map->getPlayersPerTeam()->value and $count >= ($map->getMaxCapacity() / 2)) {
            $this->game->setStage(new StartingStage());
        }
    }

    public function tick(): void {}

}