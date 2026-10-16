<?php


namespace betterlite\bedwars\game\stage\trait;


use betterlite\bedwars\game\Game;
use betterlite\bedwars\session\scoreboard\WaitingScoreboard;
use betterlite\bedwars\session\Session;
use betterlite\bedwars\utils\ConfigGetter;
use function strtoupper;

trait JoinableTrait {

    public function start(Game $game): void {
        $this->game = $game;
    }

    public function onJoin(Session $session): void {
        $session->showBossBar("{YELLOW}Playing {WHITE}BED WARS {YELLOW}on {GREEN}" . strtoupper(ConfigGetter::getIP()));
        $session->getPlayer()->getEffects()->clear();
        $session->giveWaitingItems();
        $session->setGame($this->game);
        $session->setScoreboard(new WaitingScoreboard());
        $session->teleportToWaitingWorld();

        $this->game->broadcastMessage(
            "{GRAY}" . $session->getUsername() . " {YELLOW}has joined ({AQUA}" .
            $this->game->getPlayersCount() . "{YELLOW}/{AQUA}" . $this->game->getMap()->getMaxCapacity() . "{YELLOW})!"
        );
    }

    public function onQuit(Session $session): void {
        $this->game->broadcastMessage("{GRAY}" . $session->getUsername() . " {YELLOW}has quit!");
    }

}