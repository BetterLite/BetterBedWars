<?php

declare(strict_types=1);


namespace betterlite\bedwars\listener;


use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\nbt\tag\StringTag;
use betterlite\bedwars\item\BedwarsItem;
use betterlite\bedwars\item\BedwarsItems;
use betterlite\bedwars\session\Session;
use betterlite\bedwars\session\SessionFactory;
use function strtoupper;

class SetupListener implements Listener {

    public function onBreak(BlockBreakEvent $event): void {
        $session = SessionFactory::getSession($event->getPlayer());
        $item = $this->getBedwarsItem($event->getItem());
        if($item !== null and $this->isCreatingMap($session, $event->getItem())) {
            $event->cancel();

            $session->getMapSetup()->getStep()->onBlockBreak($event->getBlock(), $item);
        }
    }

    public function onItemUse(PlayerItemUseEvent $event): void {
        $session = SessionFactory::getSession($event->getPlayer());
        $item = $this->getBedwarsItem($event->getItem());
        if($item !== null and $this->isCreatingMap($session, $event->getItem())) {
            $event->cancel();

            $session->getMapSetup()->getStep()->onInteract($item);
        }
    }

    public function onInteract(PlayerInteractEvent $event): void {
        $session = SessionFactory::getSession($event->getPlayer());
        $item = $this->getBedwarsItem($event->getItem());
        if($item !== null and $this->isCreatingMap($session, $event->getItem())) {
            $event->cancel();

            $session->getMapSetup()->getStep()->onBlockInteract($event->getBlock()->getPosition(), $event->getAction(), $event, $item);
        }
    }

    private function getBedwarsItem(Item $item): ?BedwarsItem {
        $tag = $item->getNamedTag()->getTag("bedwars_name");
        if(!$tag instanceof StringTag) {
            return null;
        }

        return BedwarsItems::getAll()[strtoupper($tag->getValue())] ?? null;
    }

    private function isCreatingMap(Session $session, Item $item): bool {
        return $session->isCreatingMap() and $item->getNamedTag()->getTag("setup") !== null;
    }

}