<?php

declare(strict_types=1);


namespace betterlite\bedwars\item\setup;


use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use betterlite\bedwars\game\shop\ShopId;
use betterlite\bedwars\session\Session;

class AddVillagerItem extends SetupItem {

    private ShopId $id;

    public function __construct(ShopId $id) {
        $this->id = $id;
        parent::__construct($id->value . " villager");
    }

    public function getId(): ShopId {
        return $this->id;
    }

    public function getName(): string {
        return $this->id->value;
    }

    public function onInteract(Session $session): void {}

    protected function realItem(): Item {
        return VanillaItems::VILLAGER_SPAWN_EGG();
    }

}