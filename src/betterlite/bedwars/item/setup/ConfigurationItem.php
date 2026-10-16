<?php

declare(strict_types=1);


namespace betterlite\bedwars\item\setup;


use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use betterlite\bedwars\form\setup\SetupMapForm;
use betterlite\bedwars\item\BedwarsItem;
use betterlite\bedwars\session\Session;

class ConfigurationItem extends BedwarsItem {

    public function __construct() {
        parent::__construct("Configuration");
    }

    public function onInteract(Session $session): void {
        if($session->isCreatingMap()) {
            $session->getPlayer()->sendForm(new SetupMapForm($session));
        }
    }

    protected function realItem(): Item {
        return VanillaItems::PAPER();
    }

}