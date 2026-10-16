<?php

declare(strict_types=1);


namespace betterlite\bedwars\game\shop\tracker;


use betterlite\bedwars\game\shop\Category;
use betterlite\bedwars\game\shop\Shop;
use betterlite\bedwars\game\shop\ShopId;

class TrackerShop extends Shop {

    public function getId(): ShopId {
        return ShopId::TRACKER;
    }

    /**
     * @return Category[]
     */
    public function getCategories(): array {
        return [
            new TrackerCategory()
        ];
    }

}