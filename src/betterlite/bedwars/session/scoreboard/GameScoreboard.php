<?php

declare(strict_types=1);


namespace betterlite\bedwars\session\scoreboard;


use betterlite\bedwars\game\stage\EndingStage;
use betterlite\bedwars\game\stage\PlayingStage;
use betterlite\bedwars\game\team\Team;
use betterlite\bedwars\session\Session;
use betterlite\bedwars\utils\ColorUtils;
use function date;
use function gmdate;

class GameScoreboard extends Scoreboard {

    protected function getLines(Session $session): array {
        if(!$session->hasGame()) {
            return [];
        }

        $stage = $session->getGame()->getStage();

        // Show scoreboard during both PlayingStage and EndingStage
        if(!$stage instanceof PlayingStage && !$stage instanceof EndingStage) {
            return [];
        }

        $lines = [
            14 => "{GRAY}" . date("m/d/y"),
            13 => " ",
        ];

        if($stage instanceof PlayingStage) {
            $event = $stage->getNextEvent();
            $lines[12] = "{WHITE}" . $event->getName() . " in: {GREEN}" . gmdate("i:s", $event->getTimeRemaining()) . "   ";
            $lines[11] = "  ";
        } elseif($stage instanceof EndingStage) {
            $winner = $stage->getWinner();

            // The winning team gets a dedicated victory scoreboard.
            if($winner !== null && $session->hasTeam() && $session->getTeam() === $winner) {
                return $this->getVictoryLines($session, $winner) + $lines;
            }

            // Losers and spectators: show WHICH team won instead of a
            // generic "VICTORY!" which wouldn't make sense for them.
            if($winner !== null) {
                $lines[12] = $winner->getColor() . $winner->getName() . " {GOLD}VICTORY";
                $lines[11] = "  ";
            } else {
                // Tie: no team won.
                $lines[12] = $stage->getTitle() . "   ";
                $lines[11] = "  ";
            }
        }

        return $lines + $this->getTeams($session);
    }

    /**
     * Scoreboard shown to the members of the winning team.
     *
     * @return string[]
     */
    private function getVictoryLines(Session $session, Team $winner): array {
        return [
            12 => "{GOLD}{BOLD}VICTORY",
            11 => " ",
            10 => $winner->getColor() . $winner->getName() . " {WHITE}Team",
            9 => "{WHITE}has won the game!",
            8 => " ",
            7 => "{WHITE}Kills: {GREEN}" . $session->getKills(),
            6 => "{WHITE}Coins earned: {GREEN}" . $session->getCoins(),
        ];
    }

    private function getTeams(Session $session): array {
        $teams = [];
        $score = 10;
        foreach($session->getGame()->getTeams() as $team) {
            $teams[$score] = ColorUtils::translate(
                $team->getColor() . $team->getFirstLetter() . " {WHITE}" . $team->getName() . ": " .
                $this->getBedStatus($team) . ($team->hasMember($session) ? " {GRAY}YOU" : " ")
            );
            $score--;
        }

        return $teams;
    }

    private function getBedStatus(Team $team): string {
        return !$team->isAlive() ? "{RED}X" : "{GREEN}" . ($team->isBedDestroyed() ? $team->getMembersCount() : "Alive");
    }

}