<?php

declare(strict_types=1);


namespace betterlite\bedwars\game\team;


use JsonSerializable;
use pocketmine\block\Air;
use pocketmine\block\Bed;
use pocketmine\item\VanillaItems;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\GameMode;
use pocketmine\utils\Utils;
use pocketmine\world\format\Chunk;
use pocketmine\world\Position;
use betterlite\bedwars\game\Game;
use betterlite\bedwars\game\generator\Generator;
use betterlite\bedwars\game\generator\GeneratorType;
use betterlite\bedwars\game\team\upgrade\trap\AlarmTrap;
use betterlite\bedwars\game\team\upgrade\trap\Trap;
use betterlite\bedwars\session\Session;
use betterlite\bedwars\utils\ColorUtils;
use function array_key_first;
use function array_search;
use function count;
use function in_array;
use function strtoupper;

class Team implements JsonSerializable {
    use TeamProperties;

    private int $capacity;

    private Upgrades $upgrades;

    private bool $bed_destroyed = false;

    /** @var Generator[] */
    private array $generators;

    /** @var Session[] */
    private array $members = [];

    /**
     * @param Generator[] $generators
     */
    public function __construct(string $name, int $capacity, Vector3 $spawn_point, Vector3 $bed_position, Area $zone, Area $claim, array $generators) {
        $this->name = $name;
        $this->color = ColorUtils::translate("{" . strtoupper($name) . "}");
        $this->capacity = $capacity;
        $this->spawn_point = $spawn_point;
        $this->bed_position = $bed_position;
        $this->zone = $zone;
        $this->claim = $claim;
        $this->generators = $generators;
        $this->upgrades = new Upgrades();
    }

    public function getColoredName(): string {
        return $this->color . $this->name;
    }

    public function getFirstLetter(): string {
        return $this->name[0];
    }

    public function getUpgrades(): Upgrades {
        return $this->upgrades;
    }

    public function isBedDestroyed(): bool {
        return $this->bed_destroyed;
    }

    /**
     * @return Generator[]
     */
    public function getGenerators(): array {
        return $this->generators;
    }

    /**
     * @return Session[]
     */
    public function getMembers(): array {
        return $this->members;
    }

    public function getMembersCount(): int {
        return count($this->members);
    }

    public function isFull(): bool {
        return $this->getMembersCount() >= $this->capacity;
    }

    public function isEmpty(): bool {
        return empty($this->members);
    }

    public function isAlive(): bool {
        return !$this->isEmpty();
    }

    public function hasMember(Session $session): bool {
        return in_array($session, $this->members, true);
    }

    public function destroyBed(Game $game, bool $break_block = true, bool $silent = false): void {
        $this->bed_destroyed = true;

        if($break_block) {
            $this->breakBedBlock($game);
        }

        if(!$silent) {
            foreach($game->getPlayersAndSpectators() as $session) {
                if(!$session->hasTeam() or $session->getTeam()->getName() !== $this->name) {
                    $session->playSound("mob.enderdragon.growl");
                }
            }

            foreach($this->members as $member) {
                $member->title("{RED}BED DESTROYED!", "{WHITE}You will no longer respawn!", 7, 30, 15);
                $member->playSound("mob.wither.death");
            }
        }
    }

    private function breakBedBlock(Game $game): void {
        $world = $game->getWorld();
        if($world === null) {
            return;
        }

        $position = Position::fromObject($this->bed_position, $world);

        $world->requestChunkPopulation($position->getX() >> Chunk::COORD_BIT_SIZE, $position->getZ() >> Chunk::COORD_BIT_SIZE, null)->onCompletion(
            function() use ($game, $world, $position) {
                // Check if game and world still exist
                if($game->getWorld() !== $world || $world === null) {
                    return;
                }

                // The stored position can be the bed itself or the block it was placed on:
                // break the bed wherever it actually is.
                $bed = $world->getBlock($position);
                if(!$bed instanceof Bed) {
                    $above = $world->getBlock($position->getSide(Facing::UP));
                    $below = $world->getBlock($position->getSide(Facing::DOWN));

                    if($above instanceof Bed) {
                        $bed = $above;
                    } elseif($below instanceof Bed) {
                        $bed = $below;
                    }
                }

                foreach($bed->getAffectedBlocks() as $block) {
                    if($block instanceof Air) {
                        continue;
                    }

                    $block->onBreak(VanillaItems::AIR());
                }
            },
            function() {}
        );
    }

    
    public function addGenerator(Generator $generator): void {
        $this->generators[] = $generator;
    }

    private function removeEmeraldGenerator(): void {
        foreach($this->generators as $index => $generator) {
            if($generator->getType() === GeneratorType::TEAM_EMERALD) {
                unset($this->generators[$index]);
                break;
            }
        }
    }

    public function addMember(Session $session): void {
        $this->members[] = $session;

        $session->setTeam($this);
        $session->clearAllInventories();
        $session->getGameSettings()->apply();

        $player = $session->getPlayer();
        $player->setNameTag($this->color . $this->getFirstLetter() . " " . $player->getName());
        $player->setGamemode(GameMode::SURVIVAL());
        $player->teleport(Position::fromObject($this->spawn_point, $session->getGame()->getWorld()));
    }

    public function removeMember(Session $session): void {
        $index = array_search($session, $this->members, true);
        if($index !== false) {
            unset($this->members[$index]);
        }

        $game = $session->getGame();
        if($game !== null and $this->isEmpty() and !$this->bed_destroyed) {
            $this->destroyBed($game);
        }

        $session->setTeam(null);
    }

    public function notifyTrap(Trap $trap, Team $team): void {
        $name = $trap->getName();
        if($trap instanceof AlarmTrap) {
            $title = "{BOLD}{RED}ALARM!!!";
            $subtitle = "{WHITE}" . $name . " set off by " . $team->getColoredName() . "{WHITE} team!";
            $message = "{BOLD}{RED}" . $name . " set off by " . $team->getColoredName() . "{RED} team!";
        } else {
            $title = "{RED}TRAP TRIGGERED!";
            $subtitle = "{WHITE}Your $name has been set off!";
            $message = "{BOLD}{RED}" . $name . " was set off!";
        }

        $sound = $trap instanceof AlarmTrap ? "mob.enderdragon.growl" : "mob.wolf.growl";
        foreach($this->members as $member) {
            $member->title($title, $subtitle);
            $member->message($message);
            $member->playSound($sound);
        }
    }

    public function reset(): void {
        $this->bed_destroyed = false;
        $this->upgrades = new Upgrades();

        $this->removeEmeraldGenerator();

        foreach($this->generators as $generator) {
            $generator->reset();
        }
        foreach($this->members as $member) {
            $member->setTeam(null);
        }
        $this->members = [];
    }

    public function jsonSerialize(): array {
        // A team saved without generators must still produce a valid entry instead of
        // crashing on generators[0].
        $generator_position = $this->generators !== [] ? $this->generators[array_key_first($this->generators)]->getPosition() : $this->spawn_point;

        return [
            "name" => $this->name,
            "spawn_point" => [
                "x" => $this->spawn_point->getX(),
                "y" => $this->spawn_point->getY(),
                "z" => $this->spawn_point->getZ()
            ],
            "bed" => [
                "x" => $this->bed_position->getX(),
                "y" => $this->bed_position->getY(),
                "z" => $this->bed_position->getZ()
            ],
            "generator" => [
                "x" => $generator_position->getX(),
                "y" => $generator_position->getY(),
                "z" => $generator_position->getZ()
            ],
            "areas" => [
                "zone" => $this->zone->jsonSerialize(),
                "claim" => $this->claim->jsonSerialize()
            ]
        ];
    }

    public function __clone(): void {
        $this->upgrades = clone $this->upgrades;
        $this->generators = Utils::cloneObjectArray($this->generators);
    }

}