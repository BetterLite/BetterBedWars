<?php

declare(strict_types=1);


namespace betterlite\bedwars\game\entity\shop;


use dresnite\EasyUI\Form;
use betterlite\bedwars\form\shop\CategoryForm;
use betterlite\bedwars\game\shop\ShopFactory;
use betterlite\bedwars\game\shop\ShopId;
use betterlite\bedwars\game\shop\upgrades\UpgradesShop;
use betterlite\bedwars\session\Session;
use betterlite\bedwars\utils\ColorUtils;

class UpgradesShopVillager extends Villager {

    protected function getName(): string {
        return "TEAM\n{AQUA}UPGRADES";
    }

    protected function getForm(Session $session): Form {
        /** @var UpgradesShop $shop */
        $shop = ShopFactory::getShop(ShopId::UPGRADES);

        $form = new CategoryForm($session, $shop->getCategories()[0]);
        $form->addRedirectFormButton(
            ColorUtils::translate("{GOLD}{BOLD}Traps{RESET}\n{YELLOW}Click to view!"),
            new CategoryForm($session, $shop->getCategories()[1])
        );
        return $form;
    }

}