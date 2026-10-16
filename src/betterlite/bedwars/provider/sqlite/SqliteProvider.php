<?php

declare(strict_types=1);


namespace betterlite\bedwars\provider\sqlite;


use betterlite\bedwars\BedWars;
use betterlite\bedwars\provider\Provider;
use betterlite\bedwars\session\Session;
use betterlite\bedwars\session\settings\SpectatorSettings;
use SQLite3;

class SqliteProvider extends Provider {

    private SQLite3 $sqlite;

    public function __construct() {
        $this->sqlite = new SQLite3(BedWars::getInstance()->getDataFolder() . "database.db");
        $this->sqlite->exec(
            "CREATE TABLE IF NOT EXISTS bedwars_users (
                xuid VARCHAR(16) PRIMARY KEY,
                coins INT,
                kills INT,
                wins INT,
                
                flying_speed INT,
                auto_teleport BOOL,
                night_vision BOOL
            );"
        );
    }

    public function loadSession(Session $session): void {
        $xuid = $session->getPlayer()->getXuid();
        $this->insertIfNotExists($xuid);

        $data = $this->fetchUserDetails($xuid);
        if($data === false) {
            $session->finishLoad();
            return;
        }

        $session->setCoins($data["coins"]);
        $session->setKills($data["kills"]);
        $session->setWins($data["wins"]);
        $session->setSpectatorSettings(SpectatorSettings::fromData($session, $data));
        $session->finishLoad();
    }

    public function updateCoins(Session $session): void {
        $this->updateProperty($session, "coins");
    }

    public function updateKills(Session $session): void {
        $this->updateProperty($session, "kills");
    }

    public function updateWins(Session $session): void {
        $this->updateProperty($session, "wins");
    }

    private function insertIfNotExists(string $xuid): void {
        $stmt = $this->sqlite->prepare("INSERT OR IGNORE INTO bedwars_users (xuid, coins, kills, wins, flying_speed, auto_teleport, night_vision) VALUES (:xuid, 0, 0, 0, 0, true, true)");
        $stmt->bindParam(":xuid", $xuid);
        $stmt->execute();
    }

    private function fetchUserDetails(string $xuid): array {
        $stmt = $this->sqlite->prepare("SELECT * FROM bedwars_users WHERE xuid = :xuid");
        $stmt->bindParam(":xuid", $xuid);
        $result = $stmt->execute();

        return $result->fetchArray(SQLITE3_ASSOC);
    }

    private function updateProperty(Session $session, string $property): void {
        $stmt = $this->sqlite->prepare("UPDATE bedwars_users SET $property = :value WHERE xuid = :xuid");
        $stmt->bindValue(":value", $session->{'get' . ucfirst($property)}());
        $stmt->bindValue(":xuid", $session->getPlayer()->getXuid());
        $stmt->execute();
    }

    public function saveSession(Session $session): void {
        $settings = $session->getSpectatorSettings();

        $stmt = $this->sqlite->prepare("UPDATE bedwars_users SET flying_speed = :flying_speed, auto_teleport = :auto_teleport, night_vision = :night_vision WHERE xuid = :xuid");
        $stmt->bindValue(":flying_speed", $settings->getFlyingSpeed());
        $stmt->bindValue(":auto_teleport", $settings->getAutoTeleport());
        $stmt->bindValue(":night_vision", $settings->getNightVision());
        $stmt->bindValue(":xuid", $session->getPlayer()->getXuid());
        $stmt->execute();
    }

}