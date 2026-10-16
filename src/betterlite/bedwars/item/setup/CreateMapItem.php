<?php

declare(strict_types=1);


namespace betterlite\bedwars\item\setup;


use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use betterlite\bedwars\item\BedwarsItem;
use betterlite\bedwars\session\Session;

class CreateMapItem extends BedwarsItem {

    public function __construct() {
        parent::__construct("Create map");
    }

    public function onInteract(Session $session): void {
        $setup = $session->getMapSetup();
        if($setup === null) {
            return;
        }

        if($setup->getMapBuilder()->canBeBuilt()) {
            $session->teleportToHub();
            $session->message("{GREEN}Map created successfully.");

            $setup->createMap();
        } else {
            $session->message("{RED}You must set all the map properties before creating it.");
        }
    }

    protected function realItem(): Item {
        return VanillaItems::EMERALD();
    }

}