<?php

declare(strict_types=1);


namespace betterlite\bedwars\game\shop;


abstract class Shop {

    abstract public function getId(): ShopId;

    /**
     * @return Category[]
     */
    abstract public function getCategories(): array;

}
