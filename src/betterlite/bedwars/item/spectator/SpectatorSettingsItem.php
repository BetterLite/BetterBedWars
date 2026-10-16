<?php

declare(strict_types=1);


namespace betterlite\bedwars\item\spectator;


use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use betterlite\bedwars\form\spectator\SpectatorSettingsForm;
use betterlite\bedwars\session\Session;

class SpectatorSettingsItem extends SpectatorItem {

    public function __construct() {
        parent::__construct("{YELLOW}Spectator settings");
    }

    protected function onSpectatorInteract(Session $session): void {
        $session->getPlayer()->sendForm(new SpectatorSettingsForm($session));
    }

    protected function realItem(): Item {
        return VanillaBlocks::REDSTONE_COMPARATOR()->asItem();
    }

}