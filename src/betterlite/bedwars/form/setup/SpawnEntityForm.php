<?php

declare(strict_types=1);


namespace betterlite\bedwars\form\setup;


use dresnite\EasyUI\utils\FormResponse;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use betterlite\bedwars\entity\PlayBedwarsEntity;
use betterlite\bedwars\form\CustomForm;
use betterlite\bedwars\game\map\Mode;

class SpawnEntityForm extends CustomForm {

    public function __construct() {
        parent::__construct("Spawn entity");
    }

    protected function onCreation(): void {
        $this->addSelectModeDropdown();
    }

    protected function onSubmit(Player $player, FormResponse $response): void {
        $players_per_team = Mode::fromInt((int) $response->getDropdownSubmittedOptionId("players_per_team"));

        $entity = new PlayBedwarsEntity($player->getLocation(), $player->getSkin(), CompoundTag::create()->setInt("players_per_team", $players_per_team->value));
        $entity->spawnToAll();

        $player->sendMessage(TextFormat::GREEN . "You have spawned the entity successfully!");
    }

}