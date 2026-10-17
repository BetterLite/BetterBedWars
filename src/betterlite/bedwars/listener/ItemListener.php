<?php

declare(strict_types=1);


namespace betterlite\bedwars\listener;


use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\nbt\tag\StringTag;
use betterlite\bedwars\item\BedwarsItem;
use betterlite\bedwars\item\BedwarsItems;
use betterlite\bedwars\session\SessionFactory;
use function strtoupper;

class ItemListener implements Listener {

    public function onTransaction(InventoryTransactionEvent $event): void {
        foreach($event->getTransaction()->getActions() as $action) {
            if($action->getSourceItem()->getNamedTag()->getTag("bedwars_item") !== null) {
                $event->cancel();
            }
        }
    }

    public function onUse(PlayerItemUseEvent $event): void {
        $item = $this->getBedwarsItem($event->getItem());
        if($item !== null) {
            $item->onInteract(SessionFactory::getSession($event->getPlayer()));
        }
    }

    /**
     * PocketMine-MP only fires PlayerItemUseEvent when the player right-clicks
     * the air: right-clicking a block fires PlayerInteractEvent instead, so
     * BedWars items would not react at all while aiming at a block. This
     * mirrors the same item actions on block interactions.
     *
     * Item actions are not cancelled here on purpose, so that the normal block
     * interaction (for example opening a chest during a game) still works.
     *
     * @handleCancelled
     */
    public function onInteract(PlayerInteractEvent $event): void {
        if($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            return;
        }

        $item = $this->getBedwarsItem($event->getItem());
        if($item !== null) {
            $item->onInteract(SessionFactory::getSession($event->getPlayer()));
        }
    }

    private function getBedwarsItem(Item $item): ?BedwarsItem {
        $tag = $item->getNamedTag()->getTag("bedwars_name");
        if(!$tag instanceof StringTag) {
            return null;
        }

        return BedwarsItems::getAll()[strtoupper($tag->getValue())] ?? null;
    }

}
