<?php

declare(strict_types=1);


namespace betterlite\bedwars\game\shop\item;


use betterlite\bedwars\game\shop\Category;
use betterlite\bedwars\game\shop\item\category\ArmorCategory;
use betterlite\bedwars\game\shop\item\category\BlocksCategory;
use betterlite\bedwars\game\shop\item\category\MeleeCategory;
use betterlite\bedwars\game\shop\item\category\MiscCategory;
use betterlite\bedwars\game\shop\item\category\PotionsCategory;
use betterlite\bedwars\game\shop\item\category\RangedCategory;
use betterlite\bedwars\game\shop\item\category\ToolsCategory;
use betterlite\bedwars\game\shop\Shop;
use betterlite\bedwars\game\shop\ShopId;

class ItemShop extends Shop {

    public function getId(): ShopId {
        return ShopId::ITEM;
    }

    /**
     * @return Category[]
     */
    public function getCategories(): array {
        return [
            new BlocksCategory(),
            new MeleeCategory(),
            new ArmorCategory(),
            new ToolsCategory(),
            new RangedCategory(),
            new PotionsCategory(),
            new MiscCategory()
        ];
    }

}