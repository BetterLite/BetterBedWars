<?php

declare(strict_types=1);


namespace betterlite\bedwars\form\setup;


use betterlite\bedwars\form\SimpleForm;
use betterlite\bedwars\game\map\Map;

class MapOptionsForm extends SimpleForm {

    private Map $map;

    public function __construct(Map $map) {
        $this->map = $map;
        parent::__construct("Manage " . $map->getName() . " map", "What do you want to do?");
    }

    protected function onCreation(): void {
        $this->addRedirectFormButton("Delete", new RemoveMapForm($this->map));
    }

}