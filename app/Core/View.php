<?php

namespace App\Core;

use App\Utilities\Error;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;

class View
{
    private static ?Environment $twigInstance = null;

    /**
     * Initialize or retrieve a singleton Twig environment instance with configuration.
     *
     * @return Environment
     */
    public static function twig(): Environment
    {
        if (self::$twigInstance === null) {
            $loader = new FilesystemLoader(ROOT . '/app/Views');

            $twigSettings = [
                // 'cache' => ROOT . '/storage/cache/twig',
            ];
            self::$twigInstance = new Environment($loader, $twigSettings);
        }
        return self::$twigInstance;
    }

    public static function render(string $page, array $content): void
    {
        try {
            $page = "$page.twig";
            echo self::twig()->render($page, $content);
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            Error::throw(500);
        }
    }
}