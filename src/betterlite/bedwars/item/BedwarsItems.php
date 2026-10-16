<?php

declare(strict_types=1);


namespace betterlite\bedwars\item;


use pocketmine\utils\CloningRegistryTrait;
use betterlite\bedwars\game\generator\GeneratorType;
use betterlite\bedwars\game\shop\ShopId;
use betterlite\bedwars\item\game\LeaveGameItem;
use betterlite\bedwars\item\game\TrackerShopItem;
use betterlite\bedwars\item\setup\AddGeneratorItem;
use betterlite\bedwars\item\setup\AddVillagerItem;
use betterlite\bedwars\item\setup\CancelItem;
use betterlite\bedwars\item\setup\ClaimingWandItem;
use betterlite\bedwars\item\setup\ConfigurationItem;
use betterlite\bedwars\item\setup\CreateMapItem;
use betterlite\bedwars\item\setup\ExitSetupItem;
use betterlite\bedwars\item\setup\SetBedPositionItem;
use betterlite\bedwars\item\setup\SetTeamGeneratorItem;
use betterlite\bedwars\item\spectator\PlayAgainItem;
use betterlite\bedwars\item\spectator\ReturnToLobbyItem;
use betterlite\bedwars\item\spectator\SpectatorSettingsItem;
use betterlite\bedwars\item\spectator\TeleporterItem;

/**
 * @method static LeaveGameItem LEAVE_GAME()
 * @method static PlayAgainItem PLAY_AGAIN()
 * @method static ReturnToLobbyItem RETURN_TO_LOBBY()
 * @method static SpectatorSettingsItem SPECTATOR_SETTINGS()
 * @method static TeleporterItem TELEPORTER()
 * @method static TrackerShopItem TRACKER_SHOP()
 * @method static AddGeneratorItem DIAMOND_GENERATOR()
 * @method static AddGeneratorItem EMERALD_GENERATOR()
 * @method static AddVillagerItem ITEM_VILLAGER()
 * @method static AddVillagerItem UPGRADES_VILLAGER()
 * @method static ConfigurationItem CONFIGURATION()
 * @method static CreateMapItem CREATE_MAP()
 * @method static ExitSetupItem EXIT_SETUP()
 * @method static SetTeamGeneratorItem TEAM_GENERATOR()
 * @method static SetBedPositionItem BED_POSITION()
 * @method static ClaimingWandItem CLAIMING_WAND()
 * @method static CancelItem CANCEL()
 */
class BedwarsItems {
    use CloningRegistryTrait;

    protected static function setup(): void {
        self::register("leave_game", new LeaveGameItem());
        self::register("play_again", new PlayAgainItem());
        self::register("return_to_lobby", new ReturnToLobbyItem());
        self::register("spectator_settings", new SpectatorSettingsItem());
        self::register("teleporter", new TeleporterItem());
        self::register("tracker_shop", new TrackerShopItem());

        self::register("diamond_generator", new AddGeneratorItem(GeneratorType::DIAMOND));
        self::register("emerald_generator", new AddGeneratorItem(GeneratorType::EMERALD));
        self::register("item_villager", new AddVillagerItem(ShopId::ITEM));
        self::register("upgrades_villager", new AddVillagerItem(ShopId::UPGRADES));
        self::register("configuration", new ConfigurationItem());
        self::register("create_map", new CreateMapItem());
        self::register("exit_setup", new ExitSetupItem());
        self::register("team_generator", new SetTeamGeneratorItem());
        self::register("bed_position", new SetBedPositionItem());
        self::register("claiming_wand", new ClaimingWandItem());
        self::register("cancel", new CancelItem());
    }

    /**
     * @return BedwarsItem[]
     */
    static public function getAll(): array {
        return self::_registryGetAll();
    }

    /**
     * @return BedwarsItem
     */
    static public function get(string $name): object {
        return self::_registryFromString($name);
    }

    static private function register(string $name, BedwarsItem $item): void {
        self::_registryRegister($name, $item);
    }

}