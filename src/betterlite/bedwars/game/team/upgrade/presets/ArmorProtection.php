<?php

declare(strict_types=1);


namespace betterlite\bedwars\game\team\upgrade\presets;


use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use betterlite\bedwars\game\team\upgrade\Upgrade;
use betterlite\bedwars\session\Session;

class ArmorProtection extends Upgrade {

    public function getName(): string {
        return "Armor Protection";
    }

    public function getLevels(): int {
        return 4;
    }

    public function internalApplySession(Session $session): void {
        $inventory = $session->getPlayer()->getArmorInventory();
        foreach($inventory->getContents() as $index => $item) {
            $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), $this->level));
            $inventory->setItem($index, $item);
        }
    }

}