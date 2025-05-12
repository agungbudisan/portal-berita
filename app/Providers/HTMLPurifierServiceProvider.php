<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use HTMLPurifier;
use HTMLPurifier_Config;

class HTMLPurifierServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('htmlpurifier', function ($app) {
            // Buat direktori cache jika belum ada
            $cachePath = storage_path('app/purifier');
            if (!file_exists($cachePath)) {
                mkdir($cachePath, 0755, true);
            }

            $config = HTMLPurifier_Config::createDefault();
            $config->set('Core.Encoding', 'UTF-8');
            $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
            $config->set('Cache.SerializerPath', $cachePath);
            // Whitelist tag HTML yang diizinkan
            $config->set('HTML.Allowed', 'p,b,i,u,strong,em,a[href|title],ul,ol,li,br,span,div,h1,h2,h3,h4,h5,h6,img[src|alt|title|width|height],table[width|border],tr,td[width],th[width],thead,tbody,hr,blockquote,pre,code');
            $config->set('HTML.TargetBlank', true);
            $config->set('AutoFormat.RemoveEmpty', false);
            $config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,color,background-color,text-align,margin,padding');

            return new HTMLPurifier($config);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
