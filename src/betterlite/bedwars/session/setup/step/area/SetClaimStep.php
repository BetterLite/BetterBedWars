<?php

declare(strict_types=1);


namespace betterlite\bedwars\session\setup\step\area;


use betterlite\bedwars\game\team\Area;

class SetClaimStep extends SetAreaStep {

    protected function setArea(Area $area): void {
        $this->team->setClaim($area);
    }

}