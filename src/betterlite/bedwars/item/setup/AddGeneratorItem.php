<?php

declare(strict_types=1);


namespace betterlite\bedwars\item\setup;


use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use betterlite\bedwars\game\generator\GeneratorType;
use betterlite\bedwars\session\Session;
use betterlite\bedwars\utils\GameUtils;
use function strtolower;

class AddGeneratorItem extends SetupItem {

    private GeneratorType $type;

    public function __construct(GeneratorType $type) {
        $this->type = $type;
        parent::__construct(GameUtils::getGeneratorColor($name = $type->toString()) . $name . " generator");
    }

    protected function realItem(): Item {
        return StringToItemParser::getInstance()->parse(strtolower($this->type->toString()) . "_block");
    }

    public function onInteract(Session $session): void {}

}