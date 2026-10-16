<?php

declare(strict_types=1);


namespace betterlite\bedwars\session\setup\builder;


use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\World;
use pocketmine\world\WorldException;
use betterlite\bedwars\game\generator\Generator;
use betterlite\bedwars\game\map\Map;
use betterlite\bedwars\game\map\MapProperties;
use betterlite\bedwars\game\map\Mode;
use betterlite\bedwars\game\team\Team;
use function array_map;

class MapBuilder {
    use MapProperties;

    private string $playing_world;
    private string $waiting_world_folder;

    /** @var TeamBuilder[] */
    private array $teams;

    public function __construct(string $name, World $waiting_world, string $playing_world, Mode $players_per_team, int $max_capacity) {
        $this->name = $name;
        $this->waiting_world = $waiting_world;
        $this->waiting_world_folder = $waiting_world->getFolderName();
        $this->playing_world = $playing_world;
        $this->players_per_team = $players_per_team;
        $this->max_capacity = $max_capacity;

        $this->setDefaultTeams();
    }

    public function getPlayingWorld(): string {
        return $this->playing_world;
    }

    public function setSpectatorSpawnPosition(Vector3 $spectator_spawn_position): void {
        $this->spectator_spawn_position = $spectator_spawn_position;
    }

    public function addShopPosition(Vector3 $position): void {
        $this->shop_positions[] = $position;
    }

    public function addUpgradesPosition(Vector3 $position): void {
        $this->upgrades_positions[] = $position;
    }

    public function addGenerator(Generator $generator): void {
        $this->generators[] = $generator;
    }

    public function removeGenerator(Generator $generator): void {
        unset($this->generators[array_search($generator, $this->generators, true)]);
    }

    private function addTeam(string $name): void {
        $this->teams[] = new TeamBuilder($name);
    }

    private function setDefaultTeams(): void {
        $teams = ["Red", "Blue", "Yellow", "Green", "Cyan", "Gray", "Orange", "Magenta"];
        for($i = 0; $i < $this->getTeamsCount(); $i++) {
            $this->addTeam($teams[$i]);
        }
    }

    private function getTeamsCount(): int {
        return (int) ($this->max_capacity / $this->players_per_team->value);
    }

    /**
     * @return TeamBuilder[]
     */
    public function getTeams(): array {
        return $this->teams;
    }

    public function canBeBuilt(): bool {
        $can_build = true;

        foreach($this->teams ?? [] as $team) {
            if(!$team->canBeBuilt()) {
                $can_build = false;
                break;
            }
        }

        $is_set = isset(
            $this->spectator_spawn_position,
            $this->generators,
            $this->teams,
            $this->shop_positions,
            $this->upgrades_positions
        );

        return $is_set and $can_build;
    }

    public function build(): Map {
        $world_manager = Server::getInstance()->getWorldManager();
        $world = $world_manager->getWorldByName($this->waiting_world_folder);
        if($world === null) {
            $world_manager->loadWorld($this->waiting_world_folder);
            $world = $world_manager->getWorldByName($this->waiting_world_folder);
            if($world === null) {
                throw new WorldException("The waiting world \"" . $this->waiting_world_folder . "\" could not be loaded.");
            }
        }

        return new Map(
            $this->name,
            $this->spectator_spawn_position,
            $this->players_per_team,
            $this->max_capacity,
            $world,
            $this->generators,
            array_map(function(TeamBuilder $team_builder): Team {
                return $team_builder->build($this);
            }, $this->teams),
            $this->shop_positions,
            $this->upgrades_positions
        );
    }

    // The World object can't be serialized (it holds the Server, workers, file handles, ...),
    // so it's excluded when this builder is stored inside an AsyncTask.
    public function __serialize(): array {
        $data = get_object_vars($this);
        unset($data["waiting_world"]);
        return $data;
    }

    public function __unserialize(array $data): void {
        foreach($data as $key => $value) {
            $this->$key = $value;
        }
    }

}