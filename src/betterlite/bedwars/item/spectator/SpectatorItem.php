<?php

declare(strict_types=1);


namespace betterlite\bedwars\item\spectator;


use betterlite\bedwars\item\BedwarsItem;
use betterlite\bedwars\session\Session;

abstract class SpectatorItem extends BedwarsItem {

    public function onInteract(Session $session): void {
        if($session->isSpectator()) {
            $this->onSpectatorInteract($session);
        }
    }

    abstract protected function onSpectatorInteract(Session $session): void;

}