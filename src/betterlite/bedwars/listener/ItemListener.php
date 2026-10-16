<?php

declare(strict_types=1);


namespace betterlite\bedwars\listener;


use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use betterlite\bedwars\item\BedwarsItems;
use betterlite\bedwars\session\SessionFactory;
use function strtolower;

class ItemListener implements Listener {

    public function onTransaction(InventoryTransactionEvent $event): void {
        foreach($event->getTransaction()->getActions() as $action) {
            if($action->getSourceItem()->getNamedTag()->getTag("bedwars_item") !== null) {
                $event->cancel();
            }
        }
    }

    public function onUse(PlayerItemUseEvent $event): void {
        $tag = $event->getItem()->getNamedTag()->getTag("bedwars_name");
        if($tag === null) {
            return;
        }

        $item = BedwarsItems::getAll()[strtolower($tag->getValue())] ?? null;
        if($item !== null) {
            $item->onInteract(SessionFactory::getSession($event->getPlayer()));
        }
    }

}