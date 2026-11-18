<?php

declare(strict_types=1);


namespace betterlite\bedwars\provider\mysql;


use mysqli;
use pocketmine\scheduler\AsyncTask;
use betterlite\bedwars\BedWars;

abstract class MysqlAsyncTask extends AsyncTask {

    private MysqlCredentials $credentials;

    public function __construct(?MysqlCredentials $credentials = null) {
        $this->credentials = $credentials ?? BedWars::getInstance()->getProvider()->getCredentials();
    }

    public function onRun(): void {
        try {
            $mysqli = $this->credentials->getMysqli();
            if($mysqli->connect_errno) {
                BedWars::getInstance()->getLogger()->error("MySQL connection failed: " . $mysqli->connect_error);
                return;
            }
            $this->onConnection($mysqli);
            $mysqli->close();
        } catch(\Throwable $e) {
            BedWars::getInstance()->getLogger()->error("MySQL task failed: " . $e->getMessage());
        }
    }

    abstract protected function onConnection(mysqli $mysqli): void;

}