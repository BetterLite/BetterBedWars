<?php

declare(strict_types=1);


namespace betterlite\bedwars\game\map;


enum Mode: int {

    case SOLO = 1;
    case DUOS = 2;
    case SQUADS = 4;

    static public function fromInt(int $value): Mode {
        return match($value) {
            1 => self::SOLO,
            2 => self::DUOS,
            4 => self::SQUADS,
            default => throw new \InvalidArgumentException("Invalid players per team: $value")
        };
    }

    public function toString(): string {
        return match($this) {
            self::SOLO => "Solo",
            self::DUOS => "Duos",
            self::SQUADS => "Squads"
        };
    }

}
