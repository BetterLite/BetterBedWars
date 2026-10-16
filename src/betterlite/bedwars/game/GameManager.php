<?php

declare(strict_types=1);


namespace betterlite\bedwars\game;


use pocketmine\Server;
use pocketmine\world\World;
use betterlite\bedwars\BedWars;
use betterlite\bedwars\game\map\Map;
use betterlite\bedwars\game\map\MapFactory;
use betterlite\bedwars\game\map\Mode;
use betterlite\bedwars\game\shop\ShopFactory;
use betterlite\bedwars\game\stage\StartingStage;
use betterlite\bedwars\game\stage\WaitingStage;
use betterlite\bedwars\game\task\GenerateGameTask;
use betterlite\bedwars\game\task\RemoveGameTask;
use function array_rand;
use function count;

class GameManager {

    private int $next_game_id = 0;

    /** @var Game[] */
    private array $games = [];

    public function __construct() {
        ShopFactory::init();
        foreach(MapFactory::getMaps() as $map) { // Generate 3 games per map by default
            $this->generateGames($map);
        }
    }

    public function getNextGameId(): int {
        return $this->next_game_id++;
    }

    /**
     * @return Game[]
     */
    public function getGames(): array {
        return $this->games;
    }

    public function getGamesCount(Map $map): int {
        return count($this->getGamesByMap($map));
    }

    /**
     * @return Game[]
     */
    public function getGamesByMap(Map $map): array {
        $games = [];
        foreach($this->games as $game) {
            if($game->getMap()->getId() === $map->getId()) {
                $games[] = $game;
            }
        }
        return $games;
    }

    public function getGameByWorld(World $world): ?Game {
        foreach(BedWars::getInstance()->getGameManager()->getGames() as $game) {
            if($game->getWorld() === $world) {
                return $game;
            }
        }
        return null;
    }

    public function findRandomGame(Mode $players_per_team): ?Game {
        $maps = MapFactory::getMapsByPlayers($players_per_team);
        if($maps !== []) {
            $map = $maps[array_rand($maps)];

            return $this->findGame($map);
        }
        return null;
    }

    public function findGame(Map $map): ?Game {
        $games = [];
        foreach($this->getGamesByMap($map) as $game) {
            $stage = $game->getStage();
            if($stage instanceof WaitingStage or ($stage instanceof StartingStage and !$game->isFull())) {
                $games[] = $game;
            }
        }

        if(empty($games)) {
            $this->generateGames($map);
            return null;
        }

        $found = null;
        $index = PHP_INT_MIN;
        foreach($games as $game) {
            $count = $game->getPlayersCount();
            if($count > $index) {
                $index = $count;
                $found = $game;
            }
        }
        return $found;
    }

    public function generateGames(Map $map): void {
        for($i = 0; $i < 3; ++$i) {
            Server::getInstance()->getAsyncPool()->submitTask(new GenerateGameTask(
                $this->getNextGameId(), $map
            ));
        }
    }

    public function addGame(Game $game): void {
        $this->games[$game->getId()] = $game;
    }

    public function removeGame(int $id): void {
        unset($this->games[$id]);
    }

    public function removeGamesByMap(Map $map): void {
        foreach($this->getGamesByMap($map) as $game) {
            $game->unloadWorld();
            Server::getInstance()->getAsyncPool()->submitTask(new RemoveGameTask($game));
            $this->removeGame($game->getId());
        }
    }

}