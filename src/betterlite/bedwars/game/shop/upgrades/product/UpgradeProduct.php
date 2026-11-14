<?php

declare(strict_types=1);


namespace betterlite\bedwars\game\shop\upgrades\product;


use pocketmine\utils\TextFormat;
use betterlite\bedwars\game\shop\upgrades\UpgradesProduct;
use betterlite\bedwars\game\team\Team;
use betterlite\bedwars\game\team\upgrade\Upgrade;
use betterlite\bedwars\session\Session;
use betterlite\bedwars\utils\GameUtils;

class UpgradeProduct extends UpgradesProduct {

    public function canBePurchased(Session $session): bool {
        return $this->getUpgrade($session->getTeam())->canLevelUp();
    }

    protected function purchase(Team $team): void {
        $this->getUpgrade($team)->levelUp($team);
    }

    public function getDisplayName(Session $session): string {
        $upgrade = $this->getUpgrade($session->getTeam());
        $level = $upgrade->getLevel();
        $levels = $upgrade->getLevels();

        $name = parent::getDisplayName($session);
        if($levels > 1 and $level !== $levels) {
            $name .= " " . GameUtils::intToRoman($level + 1);
        }
        return $name;
    }

    public function getDescription(Session $session): string {
        if(!$this->getUpgrade($session->getTeam())->canLevelUp()) {
            return TextFormat::RED . "You already have this upgrade!";
        }
        return parent::getDescription($session);
    }

    private function getUpgrade(Team $team): Upgrade {
        $upgrade = $team->getUpgrades()->getUpgrade($this->name);
        if($upgrade === null) {
            throw new \InvalidArgumentException("Unknown upgrade: " . $this->name);
        }
        return $upgrade;
    }

}