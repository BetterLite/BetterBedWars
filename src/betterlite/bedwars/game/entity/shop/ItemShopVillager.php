<?php

declare(strict_types=1);


namespace betterlite\bedwars\game\entity\shop;



use dresnite\EasyUI\Form;
use betterlite\bedwars\form\shop\ShopForm;
use betterlite\bedwars\game\shop\ShopFactory;
use betterlite\bedwars\game\shop\ShopId;
use betterlite\bedwars\session\Session;

class ItemShopVillager extends Villager {

    protected function getName(): string {
        return "ITEM SHOP";
    }

    protected function getForm(Session $session): Form {
        return new ShopForm($session, "Quick buy", ShopFactory::getShop(ShopId::ITEM));
    }

}