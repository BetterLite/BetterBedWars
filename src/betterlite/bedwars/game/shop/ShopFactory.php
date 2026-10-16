<?php

declare(strict_types=1);


namespace betterlite\bedwars\game\shop;


use betterlite\bedwars\game\shop\item\ItemShop;
use betterlite\bedwars\game\shop\tracker\TrackerShop;
use betterlite\bedwars\game\shop\upgrades\UpgradesShop;

class ShopFactory {

    /** @var Shop[] */
    static private array $shops = [];

    static public function init(): void {
        self::addShop(new ItemShop());
        self::addShop(new UpgradesShop());
        self::addShop(new TrackerShop());
    }

    static public function getShop(ShopId $id): ?Shop {
        return self::$shops[$id->value] ?? null;
    }

    static private function addShop(Shop $shop): void {
        self::$shops[$shop->getId()->value] = $shop;
    }

}