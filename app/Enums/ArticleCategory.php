<?php

namespace App\Enums;

enum ArticleCategory: string
{
    case Tech     = 'tech';
    case Security = 'security';
    case Guides   = 'guides';
    case News     = 'news';
    case Other    = 'other';

    public function label(): string
    {
        return match($this) {
            self::Tech     => 'Технологии',
            self::Security => 'Безопасность',
            self::Guides   => 'Гайды',
            self::News     => 'Новости',
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
