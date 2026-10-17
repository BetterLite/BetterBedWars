# BetterBedWars v1.2.0

A complete BedWars minigame for PocketMine-MP 5.0 — fork of [Sergittos/BedWars](https://github.com/Sergittos/BedWars) (BedwarsPM), maintained under the BetterLite name.

## 🛠️ Bug fixes (25+)

**Crashes**
- Fixed crash on every player quit (session removed before game cleanup)
- Fixed `Game::reset()` loop that spammed world deletion tasks
- Fixed `CreateMapTask` / `DeleteMapTask` storing non-serializable `World` objects in AsyncTasks
- Fixed MySQL `LoadSessionTask` crash on empty results
- Fixed exception when using setup items with uppercase names ("Claiming_wand", ...)
- Fixed "It's a trap" name mismatch (trap was purchasable unlimited times)
- Fixed boss bar `TypeError` crash on join (newer PMMP 5 `BossEventPacket::show()` signature)
- Fixed crash when interacting with non-BedWars items in setup mode (missing or wrong-typed NBT tags with newer `pocketmine/nbt`)

**Logic**
- Scoreboards now support newer PMMP 5 protocol packets while retaining compatibility with older PMMP 5 versions.
- Tracker Shop now works (own team no longer blocks the check)
- Claim protection now checks the actual placed blocks
- Player-placed TNT now ignites correctly
- Fixed `unset($arr[array_search()])` removing the wrong player
- Fixed MySQL `loading_data` race (redundant DB writes / stat loss)
- `ColorUtils::getDye` no longer crashes on unknown colors
- `Session::kill()` without a team no longer crashes on respawn
- Map deletion now cleans up active games and the waiting world
- Listener side effects (`onExhaust`, `onExplosionPrime`, `onMerge`) scoped to BedWars only
- Spectator settings now persist (SQLite & MySQL)
- Spectators get the proper game scoreboard
- Added `hasTeam()` guards on chest/bed interactions
- Map names with illegal characters are now rejected

**Robustness**
- Source-tree installations now load bStats and EasyUI without conflicting with the server's PocketMine-MP classes.
- Protocol packets (sound, mob equipment/armor, invisibility) are now built in a version-independent way, working on both older and newer PMMP 5 builds.
- Default values for uninitialized `Session` properties
- Safe registry lookups for bedwars items
- Defaults for missing JSON/SQLite data and corrupt `maps.json`
- `players_per_team` on join NPCs is read defensively (legacy/damaged entity data can no longer crash a chunk load)

## ✨ Completed TODOs
- Final kill detection (40 coins + "FINAL KILL!" broadcast)
- Trap trigger sounds
- `Mode` enum for players-per-team (Solo/Duos/Squads)
- `ShopId` enum for shops
- Deduplicated maps.json read/write into `MapFactory`

## 🔄 Changes
- Renamed plugin to **BetterBedWars** (author: BetterLite)
- Namespace moved from `sergittos\bedwars` to `betterlite\bedwars`
- New `README.md` and changelog (`changelogs/1.2.0.md`)
- Composer dependencies now track the BetterLite server packages (`betterlite/pocketmine-mp` and the `betterlite/*` libraries)

Full details: see `changelogs/1.2.0.md` — original project credit: Sergittos/BedWars (LGPL-3.0).
