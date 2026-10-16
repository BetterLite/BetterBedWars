<?php

declare(strict_types=1);


namespace betterlite\bedwars\game\shop\upgrades;


use betterlite\bedwars\game\shop\Category;
use betterlite\bedwars\game\shop\Shop;
use betterlite\bedwars\game\shop\ShopId;
use betterlite\bedwars\game\shop\upgrades\category\TrapsCategory;
use betterlite\bedwars\game\shop\upgrades\category\UpgradesCategory;

class UpgradesShop extends Shop {

    public function getId(): ShopId {
        return ShopId::UPGRADES;
    }

    /**
     * @return Category[]
     */
    public function getCategories(): array {
        return [
            new UpgradesCategory(),
            new TrapsCategory()
        ];
    }

}