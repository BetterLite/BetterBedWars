<?php

declare(strict_types=1);


namespace betterlite\bedwars\game\team\upgrade\presets;


use betterlite\bedwars\game\team\upgrade\Upgrade;

class HealPool extends Upgrade {

    public function getName(): string {
        return "Heal Pool";
    }

    public function getLevels(): int {
        return 1;
    }

}