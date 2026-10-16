<?php

declare(strict_types=1);


namespace betterlite\bedwars\provider\mysql;


use pocketmine\Server;
use betterlite\bedwars\provider\mysql\task\CreateTablesTask;
use betterlite\bedwars\provider\mysql\task\LoadSessionTask;
use betterlite\bedwars\provider\mysql\task\UpdateCoinsTask;
use betterlite\bedwars\provider\mysql\task\UpdateKillsTask;
use betterlite\bedwars\provider\mysql\task\UpdateSpectatorSettingsTask;
use betterlite\bedwars\provider\mysql\task\UpdateWinsTask;
use betterlite\bedwars\provider\Provider;
use betterlite\bedwars\session\Session;
use betterlite\bedwars\utils\ConfigGetter;

class MysqlProvider extends Provider {

    private MysqlCredentials $credentials;

    public function __construct() {
        $this->credentials = MysqlCredentials::fromData(ConfigGetter::getMysqlCredentials());

        $this->scheduleAsyncTask(new CreateTablesTask($this->credentials));
    }

    public function getCredentials(): MysqlCredentials {
        return $this->credentials;
    }

    public function loadSession(Session $session): void {
        $this->scheduleAsyncTask(new LoadSessionTask($session));
    }

    public function updateCoins(Session $session): void {
        $this->scheduleAsyncTask(new UpdateCoinsTask($session));
    }

    public function updateKills(Session $session): void {
        $this->scheduleAsyncTask(new UpdateKillsTask($session));
    }

    public function updateWins(Session $session): void {
        $this->scheduleAsyncTask(new UpdateWinsTask($session));
    }

    public function saveSession(Session $session): void {
        $this->scheduleAsyncTask(new UpdateSpectatorSettingsTask($session));
    }

    private function scheduleAsyncTask(MysqlAsyncTask $task): void {
        Server::getInstance()->getAsyncPool()->submitTask($task);
    }

}