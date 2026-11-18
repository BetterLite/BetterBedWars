<?php

declare(strict_types=1);


namespace betterlite\bedwars\game\task;


use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Filesystem;
use betterlite\bedwars\BedWars;
use betterlite\bedwars\game\Game;

class RemoveGameTask extends AsyncTask {

    private int $id;

    private string $world_path;

    public function __construct(Game $game) {
        $this->id = $game->getId();

        $this->world_path = Server::getInstance()->getDataPath() . "worlds/" . $game->getMap()->getName() . "-" . $this->id;
    }

    public function onRun(): void {
        if(is_dir($this->world_path)) {
            Filesystem::recursiveUnlink($this->world_path);
        }
    }

    public function onCompletion(): void {
        $gameManager = BedWars::getInstance()->getGameManager();
        // Only remove if the game still exists (it might have been removed already during reset)
        foreach($gameManager->getGames() as $game) {
            if($game->getId() === $this->id) {
                $gameManager->removeGame($this->id);
                break;
            }
        }
    }

}