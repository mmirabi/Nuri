<?php

namespace Botble\Theme\Providers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Helper;
use Botble\Base\Supports\ServiceProvider;
use Botble\Theme\Facades\Theme;
use Composer\Autoload\ClassLoader;
use Illuminate\Support\Arr;

class ThemeManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (Theme::hasInheritTheme()) {
            $this->loadJsonTranslationsFromTheme(
                Theme::getInheritTheme()
            );
        }

        $this->loadJsonTranslationsFromTheme(
            Theme::getThemeName()
        );
    }

    public function boot(): void
    {
        if (Theme::hasInheritTheme()) {
            $this->registerAutoloadPathFromTheme(
                Theme::getInheritTheme()
            );
        }

        $this->registerAutoloadPathFromTheme(
            Theme::getThemeName()
        );
    }

    protected function loadJsonTranslationsFromTheme(string $theme): void
    {
        $this->loadJsonTranslationsFrom(theme_path($theme . '/lang'));
        $this->loadJsonTranslationsFrom(lang_path('vendor/themes/' . $theme));
    }

    protected function registerAutoloadPathFromTheme(string $theme): void
    {
        if (empty($theme)) {
            return;
        }

        $themePath = theme_path($theme);

        $configFilePath = $themePath . '/theme.json';

        if ($this->app['files']->exists($configFilePath)) {
            $content = BaseHelper::getFileData($configFilePath);

            if (! empty($content) && Arr::has($content, 'namespace')) {
                $loader = new ClassLoader();
                $loader->setPsr4($content['namespace'], theme_path($theme . '/src'));
                $loader->register();
            }
        }

        Helper::autoload($themePath . '/functions');
    }
}
