<?php

declare(strict_types=1);


namespace betterlite\bedwars\game\shop\tracker;


use betterlite\bedwars\game\shop\Category;
use betterlite\bedwars\game\team\Team;
use betterlite\bedwars\session\Session;
use function array_map;

class TrackerCategory extends Category {

    public function __construct() {
        parent::__construct("Tracker Shop");
    }

    /**
     * @return TrackerProduct[]
     */
    public function getProducts(Session $session): array {
        return array_map(function(Team $team) {
            return new TrackerProduct($name = $team->getName(), "Track Team " . $name);
        }, $session->getGame()->getTeams());
    }

}