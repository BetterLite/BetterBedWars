<?php

declare(strict_types=1);


namespace betterlite\bedwars\item\game;


use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use betterlite\bedwars\form\shop\ShopForm;
use betterlite\bedwars\game\shop\ShopFactory;
use betterlite\bedwars\game\shop\ShopId;
use betterlite\bedwars\item\BedwarsItem;
use betterlite\bedwars\session\Session;

class TrackerShopItem extends BedwarsItem {

    public function __construct() {
        parent::__construct("Tracker Shop", false);
    }

    public function onInteract(Session $session): void {
        $session->getPlayer()->sendForm(new ShopForm($session, "Tracker & Communication", ShopFactory::getShop(ShopId::TRACKER)));
    }

    protected function realItem(): Item {
        return VanillaItems::COMPASS();
    }

}