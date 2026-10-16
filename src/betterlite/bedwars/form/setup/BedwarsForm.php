<?php

declare(strict_types=1);


namespace betterlite\bedwars\form\setup;


use betterlite\bedwars\form\SimpleForm;

class BedwarsForm extends SimpleForm {

    public function __construct() {
        parent::__construct("BedWars");
    }

    protected function onCreation(): void {
        $this->addRedirectFormButton("Create a map", new CreateMapForm());
        $this->addRedirectFormButton("Manage maps", new ManageMapsForm());
        $this->addRedirectFormButton("Spawn join game entity", new SpawnEntityForm());
    }

}