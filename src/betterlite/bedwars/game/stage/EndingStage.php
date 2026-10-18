<?php

declare(strict_types=1);


namespace betterlite\bedwars\game\stage;


use betterlite\bedwars\game\team\Team;
use betterlite\bedwars\session\Session;
use function count;

class EndingStage extends Stage {

    private int $time = 5;

    private bool $tie = false;

    private ?Team $winner = null;

    protected function onStart(): void {
        foreach($this->game->getPlayersAndSpectators() as $session) {
            $session->title("{RED}GAME OVER!");
        }
        $alive_teams = $this->game->getAliveTeams();
        $this->tie = count($alive_teams) > 1;

        // The last team standing won. Store it once so a member quitting
        // during the ending phase can't change the result.
        $this->winner = count($alive_teams) === 1 ? $alive_teams[0] : null;

        // Update scoreboards to show the EndingStage scoreboard
        $this->game->updateScoreboards();
    }

    public function getWinner(): ?Team {
        return $this->winner;
    }

    public function onJoin(Session $session): void {
        if(!$this->tie) {
            $session->addWin();
            $session->addCoins(140);
        }

        $session->title($this->getTitle(), "", 0, $this->time * 20);
        $session->resetSettings();
    }

    public function onQuit(Session $session): void {
        $this->game->despawnGeneratorsFrom($session);
    }

    public function tick(): void {
        $this->time--;
        $this->game->updateScoreboards(); // Update scoreboard during ending phase
        if($this->time <= 0) {
            $this->game->reset();
        }
    }

    public function getTitle(): string {
        return $this->tie ? "{ORANGE}TIE!" : "{GOLD}VICTORY!";
    }

}