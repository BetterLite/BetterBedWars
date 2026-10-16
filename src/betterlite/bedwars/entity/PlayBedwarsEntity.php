<?php

declare(strict_types=1);


namespace betterlite\bedwars\entity;


use pocketmine\block\BlockTypeIds;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use betterlite\bedwars\form\queue\PlayBedwarsForm;
use betterlite\bedwars\game\map\Mode;
use betterlite\bedwars\session\Session;
use betterlite\bedwars\session\SessionFactory;
use betterlite\bedwars\utils\ColorUtils;
use betterlite\bedwars\utils\ConfigGetter;
use betterlite\bedwars\utils\GameUtils;
use function array_filter;
use function count;

class PlayBedwarsEntity extends Human {

    private Mode $players_per_team;

    public function __construct(Location $location, Skin $skin, CompoundTag $nbt) {
        $this->players_per_team = Mode::fromInt($nbt->getInt("players_per_team"));
        parent::__construct($location, $skin);
    }

    protected function initEntity(CompoundTag $nbt): void {
        parent::initEntity($nbt);

        $this->updateNameTag();
        $this->setNameTagAlwaysVisible();
    }

    public function updateNameTag(): void {
        $amount = $this->getSessionsCount();
        $this->setNameTag(ColorUtils::translate(
            "{YELLOW}{BOLD}CLICK TO PLAY{RESET}\n" .
            "{AQUA}" . GameUtils::getMode($this->players_per_team) . " {GRAY}[v" . ConfigGetter::getVersion() . "]\n" .
            "{YELLOW}{BOLD}" . $amount . " " . ($amount === 1 ? "Player" : "Players")
        ));
    }

    public function attack(EntityDamageEvent $source): void {
        if($source instanceof EntityDamageByChildEntityEvent or !$source instanceof EntityDamageByEntityEvent) {
            return;
        }
        $damager = $source->getDamager();
        if(!$damager instanceof Player) {
            return;
        }

        if($damager->hasPermission(DefaultPermissions::ROOT_OPERATOR) and
            $damager->getInventory()->getItemInHand()->getTypeId() === BlockTypeIds::BEDROCK) {
            $this->kill();
            return;
        }

        $damager->sendForm(new PlayBedwarsForm($this->players_per_team));
    }

    public function onInteract(Player $player, Vector3 $clickPos): bool {
        $player->sendForm(new PlayBedwarsForm($this->players_per_team));
        return true;
    }

    public function saveNBT(): CompoundTag {
        return parent::saveNBT()->setInt("players_per_team", $this->players_per_team->value);
    }

    public function getPlayersPerTeam(): Mode {
        return $this->players_per_team;
    }

    private function getSessionsCount(): int {
        return count(array_filter(SessionFactory::getSessions(), function(Session $session) {
            return $session->isPlaying() and $session->getGame()->getMap()->getPlayersPerTeam() === $this->players_per_team;
        }));
    }

}