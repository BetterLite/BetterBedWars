<?php

declare(strict_types=1);


namespace betterlite\bedwars\game\map\task;


use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Filesystem;
use betterlite\bedwars\BedWars;
use betterlite\bedwars\game\map\Map;
use betterlite\bedwars\game\map\MapFactory;
use betterlite\bedwars\game\task\RemoveGameTask;

class DeleteMapTask extends AsyncTask {

    private string $world_path;
    private string $waiting_world_folder;

    public function __construct(Map $map) {
        $this->storeLocal("map", $map);
        $this->world_path = BedWars::getInstance()->getDataFolder() . "worlds/" . $map->getName();
        $this->waiting_world_folder = $map->getWaitingWorld()->getFolderName();
    }

    public function onRun(): void {
        Filesystem::recursiveUnlink($this->world_path);
    }

    public function onCompletion(): void {
        $map = $this->fetchLocal("map");

        $data = MapFactory::readMapsData();
        $data = array_filter($data, function(array $map_data) use ($map): bool {
            return $map_data["name"] !== $map->getName();
        });
        MapFactory::writeMapsData($data);

        MapFactory::removeMap($map->getId());

        $world = Server::getInstance()->getWorldManager()->getWorldByName($this->waiting_world_folder);
        if($world !== null and $world->isLoaded()) {
            Server::getInstance()->getWorldManager()->unloadWorld($world);
        }

        BedWars::getInstance()->getGameManager()->removeGamesByMap($map);
    }

}