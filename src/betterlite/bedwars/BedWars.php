<?php

declare(strict_types=1);


namespace betterlite\bedwars;


// Load only the plugin libraries when the plugin is run directly from the
// source tree. Do not load vendor/autoload.php here: it also registers the
// PocketMine-MP bundled with Composer, which can conflict with the server's
// own PocketMine-MP classes. Production .phar builds provide these libraries
// through Pharynx.
$vendor_dir = dirname(__DIR__, 3) . "/vendor";
if(is_dir($vendor_dir)) {
    $dependency_roots = [
        "bStats\\PocketmineMp\\" => $vendor_dir . "/bstats/pocketmine-mp/src/",
        "dresnite\\EasyUI\\" => $vendor_dir . "/dresnite/easyui/src/dresnite/EasyUI/",
    ];

    spl_autoload_register(static function(string $class) use ($dependency_roots): void {
        foreach($dependency_roots as $prefix => $root) {
            if(!str_starts_with($class, $prefix)) {
                continue;
            }

            $relative_class = substr($class, strlen($prefix));
            $file = $root . str_replace("\\", DIRECTORY_SEPARATOR, $relative_class) . ".php";
            if(is_file($file)) {
                require_once $file;
            }
            return;
        }
    });
}

use bStats\PocketmineMp\Metrics;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\event\Listener;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;
use betterlite\bedwars\command\BedWarsCommand;
use betterlite\bedwars\entity\PlayBedwarsEntity;
use betterlite\bedwars\game\entity\misc\Fireball;
use betterlite\bedwars\game\entity\shop\ItemShopVillager;
use betterlite\bedwars\game\entity\shop\UpgradesShopVillager;
use betterlite\bedwars\game\GameHeartbeat;
use betterlite\bedwars\game\GameManager;
use betterlite\bedwars\game\map\MapFactory;
use betterlite\bedwars\game\task\RemoveGameTask;
use betterlite\bedwars\listener\GameListener;
use betterlite\bedwars\listener\ItemListener;
use betterlite\bedwars\listener\SessionListener;
use betterlite\bedwars\listener\SetupListener;
use betterlite\bedwars\listener\SpawnProtectionListener;
use betterlite\bedwars\listener\WaitingListener;
use betterlite\bedwars\provider\json\JsonProvider;
use betterlite\bedwars\provider\mysql\MysqlProvider;
use betterlite\bedwars\provider\Provider;
use betterlite\bedwars\provider\sqlite\SqliteProvider;
use betterlite\bedwars\session\SessionFactory;
use betterlite\bedwars\utils\ConfigGetter;
use function basename;
use function strtolower;

class BedWars extends PluginBase {
    use SingletonTrait;

    private Provider $provider;
    private Metrics $metrics;
    private GameManager $game_manager;

    protected function onLoad(): void {
        self::setInstance($this);

        $worlds_dir = $this->getDataFolder() . "worlds/";
        if(!is_dir($worlds_dir)) {
            mkdir($worlds_dir);
        }

        $this->saveResource("maps.json");
    }

    protected function onEnable(): void {
        MapFactory::init();

        $this->provider = $this->obtainProvider();
        $this->metrics = new Metrics($this, 29605);
        $this->game_manager = new GameManager();

        $this->registerEntity(PlayBedwarsEntity::class);
        $this->registerEntity(ItemShopVillager::class);
        $this->registerEntity(UpgradesShopVillager::class);
        $this->registerFireball();

        $this->registerListener(new GameListener());
        $this->registerListener(new ItemListener());
        $this->registerListener(new SessionListener());
        $this->registerListener(new SetupListener());
        $this->registerListener(new WaitingListener());

        if(ConfigGetter::isSpawnProtectionEnabled()) {
            $this->registerListener(new SpawnProtectionListener());
        }

        $this->getServer()->getCommandMap()->register("bedwars", new BedWarsCommand());

        $this->getScheduler()->scheduleRepeatingTask(new GameHeartbeat(), 1);
    }

    protected function onDisable(): void {
        foreach(SessionFactory::getSessions() as $session) {
            $session->save();
        }

        foreach($this->game_manager->getGames() as $game) {
            $game->unloadWorld();
            $this->getServer()->getAsyncPool()->submitTask(new RemoveGameTask($game));
        }
    }

    private function registerListener(Listener $listener): void {
        $this->getServer()->getPluginManager()->registerEvents($listener, $this);
    }

    private function registerEntity(string $class): void {
        EntityFactory::getInstance()->register($class, function(World $world, CompoundTag $nbt) use ($class): Entity {
            return new $class(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ["bedwars:" . basename($class)]);
    }

    private function registerFireball(): void {
        EntityFactory::getInstance()->register(Fireball::class, function(World $world, CompoundTag $nbt): Fireball {
            return new Fireball(EntityDataHelper::parseLocation($nbt, $world), null);
        }, ["bedwars:fireball"]);
    }

    private function obtainProvider(): Provider {
        return match(strtolower(ConfigGetter::getProvider())) {
            "mysql" => new MysqlProvider(),
            "sqlite", "sqlite3" => new SqliteProvider(),
            "json" => new JsonProvider(),
            default => throw new \Error("Invalid provider, check your config and try again.")
        };
    }

    public function getProvider(): Provider {
        return $this->provider;
    }

    public function getMetrics(): Metrics {
        return $this->metrics;
    }

    public function getGameManager(): GameManager {
        return $this->game_manager;
    }

}
