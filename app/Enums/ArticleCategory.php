<?php

namespace App\Enums;

enum ArticleCategory: string
{
    case Proxy    = 'proxy';
    case Vpn      = 'vpn';
    case Security = 'security';
    case Tools    = 'tools';
    case Other    = 'other';

    public function label(): string
    {
        return match($this) {
            self::Proxy    => 'Прокси',
            self::Vpn      => 'VPN',
            self::Security => 'Безопасность',
            self::Tools    => 'Инструменты',
            self::Other    => 'Другое',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function forFrontend(): array
    {
        return array_map(fn($c) => ['value' => $c->value, 'label' => $c->label()], self::cases());
    }
}
