<?php

namespace App\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

class HtmlPurifierService
{
    private HTMLPurifier $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();

        // Cache directory for HTMLPurifier serialised definitions
        $cacheDir = storage_path('app/htmlpurifier');
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        $config->set('Cache.SerializerPath', $cacheDir);

        // Allowed elements
        $config->set('HTML.Allowed',
            'p,br,strong,em,u,s,' .
            'ul,ol,li,' .
            'blockquote,pre,code,' .
            'h2,h3,h4,' .
            'table,thead,tbody,tr,th,td,' .
            'a[href|target|rel],' .
            'img[src|alt|width|height]'
        );

        // Only allow http/https URIs (blocks javascript:, data:, etc.)
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true]);

        // Automatically add rel="noopener noreferrer" to external links
        $config->set('HTML.TargetBlank', true);
        $config->set('HTML.TargetNoreferrer', true);
        $config->set('HTML.TargetNoopener', true);

        $this->purifier = new HTMLPurifier($config);
    }

    /**
     * Strip disallowed tags and dangerous attributes from the given HTML string.
     */
    public function clean(string $html): string
    {
        return $this->purifier->purify($html);
    }
}
