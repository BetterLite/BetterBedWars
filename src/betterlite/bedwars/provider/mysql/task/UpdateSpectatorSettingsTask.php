<?php

declare(strict_types=1);


namespace betterlite\bedwars\provider\mysql\task;


use mysqli;
use betterlite\bedwars\provider\mysql\MysqlAsyncTask;
use betterlite\bedwars\session\Session;

class UpdateSpectatorSettingsTask extends MysqlAsyncTask {

    private string $xuid;
    private int $flying_speed;
    private bool $auto_teleport;
    private bool $night_vision;

    public function __construct(Session $session) {
        $settings = $session->getSpectatorSettings();

        $this->xuid = $session->getPlayer()->getXuid();
        $this->flying_speed = $settings->getFlyingSpeed();
        $this->auto_teleport = $settings->getAutoTeleport();
        $this->night_vision = $settings->getNightVision();
        parent::__construct();
    }

    protected function onConnection(mysqli $mysqli): void {
        $stmt = $mysqli->prepare("UPDATE bedwars_users SET flying_speed = ?, auto_teleport = ?, night_vision = ? WHERE xuid = ?");
        $stmt->bind_param("iiis", ...[$this->flying_speed, $this->auto_teleport, $this->night_vision, $this->xuid]);
        $stmt->execute();
        $stmt->close();
    }

}
