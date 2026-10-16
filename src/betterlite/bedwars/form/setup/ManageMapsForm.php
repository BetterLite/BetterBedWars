<?php

declare(strict_types=1);


namespace betterlite\bedwars\form\setup;


use betterlite\bedwars\form\SimpleForm;
use betterlite\bedwars\game\map\MapFactory;

class ManageMapsForm extends SimpleForm {

    public function __construct() {
        parent::__construct("Manage maps");
    }

    protected function onCreation(): void {
        foreach(MapFactory::getMaps() as $map) {
            $this->addRedirectFormButton($map->getName(), new MapOptionsForm($map));
        }
    }

}