<?php

declare(strict_types=1);


namespace betterlite\bedwars\provider\json;


use pocketmine\utils\Config;
use betterlite\bedwars\BedWars;
use betterlite\bedwars\provider\Provider;
use betterlite\bedwars\session\Session;
use betterlite\bedwars\session\settings\SpectatorSettings;
use function is_dir;
use function mkdir;

class JsonProvider extends Provider {

    public function __construct() {
        $users_dir = $this->getUsersDir();
        if(!is_dir($users_dir)) {
            mkdir($users_dir);
        }
    }

    public function loadSession(Session $session): void {
        $config = $this->getSessionConfig($session);
        $session->setCoins($config->get("coins", 0));
        $session->setKills($config->get("kills", 0));
        $session->setWins($config->get("wins", 0));
        $session->setSpectatorSettings(SpectatorSettings::fromData($session, $config->get("spectator_settings", [
            "flying_speed" => 0,
            "auto_teleport" => true,
            "night_vision" => true
        ])));
        $session->finishLoad();
    }

    public function saveSession(Session $session): void {
        $config = $this->getSessionConfig($session);
        $config->set("coins", $session->getCoins());
        $config->set("kills", $session->getKills());
        $config->set("wins", $session->getWins());
        $config->set("spectator_settings", [
            "flying_speed" => $session->getSpectatorSettings()->getFlyingSpeed(),
            "auto_teleport" => $session->getSpectatorSettings()->getAutoTeleport(),
            "night_vision" => $session->getSpectatorSettings()->getNightVision()
        ]);
        $config->save();
    }

    public function updateCoins(Session $session): void {}

    public function updateKills(Session $session): void {}

    public function updateWins(Session $session): void {}

    private function getSessionConfig(Session $session): Config {
        return new Config($this->getUsersDir() . $session->getPlayer()->getXuid() . ".json", Config::JSON, [
            "coins" => 0,
            "kills" => 0,
            "wins" => 0,
            "spectator_settings" => [
                "flying_speed" => 0,
                "auto_teleport" => true,
                "night_vision" => true
            ]
        ]);
    }

    private function getUsersDir(): string {
        return BedWars::getInstance()->getDataFolder() . "users/";
    }

}