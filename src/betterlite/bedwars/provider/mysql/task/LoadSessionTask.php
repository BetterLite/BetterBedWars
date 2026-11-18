<?php

declare(strict_types=1);


namespace betterlite\bedwars\provider\mysql\task;


use mysqli;
use betterlite\bedwars\BedWars;
use betterlite\bedwars\provider\mysql\MysqlAsyncTask;
use betterlite\bedwars\session\Session;
use betterlite\bedwars\session\SessionFactory;
use betterlite\bedwars\session\settings\SpectatorSettings;

class LoadSessionTask extends MysqlAsyncTask {

    private string $xuid;
    private string $username;

    public function __construct(Session $session) {
        $this->xuid = $session->getPlayer()->getXuid();
        $this->username = $session->getUsername();
        parent::__construct();
    }

    protected function onConnection(mysqli $mysqli): void {
        $this->insertIfNotExists($mysqli);
        $this->fetchUserDetails($mysqli);
    }

    private function insertIfNotExists(mysqli $mysqli): void {
        $stmt = $mysqli->prepare("INSERT IGNORE INTO bedwars_users (xuid, coins, kills, wins, flying_speed, auto_teleport, night_vision) VALUES (?, 0, 0, 0, 0, true, true)");
        $stmt->bind_param("s", ...[$this->xuid]);
        $stmt->execute();
        $stmt->close();
    }

    private function fetchUserDetails(mysqli $mysqli): void {
        $stmt = $mysqli->prepare("SELECT * FROM bedwars_users WHERE xuid = ?");
        $stmt->bind_param("s", ...[$this->xuid]);
        $stmt->execute();
        $result = $stmt->get_result();

        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $this->setResult($rows);

        $stmt->free_result();
        $stmt->close();
    }

    public function onCompletion(): void {
        $session = SessionFactory::getSessionByName($this->username);
        if($session === null or !$session->isLoadingData()) {
            return;
        }

        $rows = $this->getResult();
        if(empty($rows)) {
            $session->finishLoad();
            return;
        }
        $data = $rows[0];

        // Stats may be earned while the async load is in flight. Add the stored values
        // to any in-memory delta instead of overwriting it.
        $coins = (int) $data["coins"] + $session->getCoins();
        $kills = (int) $data["kills"] + $session->getKills();
        $wins = (int) $data["wins"] + $session->getWins();

        $session->setCoins($coins);
        $session->setKills($kills);
        $session->setWins($wins);
        $session->setSpectatorSettings(SpectatorSettings::fromData($session, $data));
        $session->finishLoad();

        // Increments earned during the load were not persisted because loading_data was
        // true; persist the merged totals if they changed from the stored values.
        $provider = BedWars::getInstance()->getProvider();
        if($coins !== (int) $data["coins"]) {
            $provider->updateCoins($session);
        }
        if($kills !== (int) $data["kills"]) {
            $provider->updateKills($session);
        }
        if($wins !== (int) $data["wins"]) {
            $provider->updateWins($session);
        }
    }

}