<?php

declare(strict_types=1);


namespace betterlite\bedwars\game\task;


use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Filesystem;
use betterlite\bedwars\BedWars;
use betterlite\bedwars\game\map\Map;
use betterlite\bedwars\game\map\MapFactory;
use betterlite\bedwars\game\Game;

class GenerateGameTask extends AsyncTask {

    private int $id;

    private string $map_id;
    private string $map_name;

    private string $world_path;
    private string $destination_path;

    public function __construct(int $id, Map $map) {
        $this->id = $id;

        $this->map_id = $map->getId();
        $this->map_name = $map->getName();

        $this->world_path = BedWars::getInstance()->getDataFolder() . "worlds/" . $this->map_name;
        $this->destination_path = Server::getInstance()->getDataPath() . "worlds/" . $this->map_name . "-" . $id;
    }

    public function onRun(): void {
        Filesystem::recursiveCopy($this->world_path, $this->destination_path);
    }

    public function onCompletion(): void {
        BedWars::getInstance()->getGameManager()->addGame(new Game(MapFactory::getMapById($this->map_id), $this->id));
    }

}