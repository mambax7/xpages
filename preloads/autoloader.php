<?php

declare(strict_types=1);

/**
 * xPages — PSR-4 autoloader for XoopsModules\Xpages\* classes.
 *
 * Registered as a preload so it loads during XOOPS bootstrap. Also
 * require_once'd from xoops_version.php so fresh-install / module-
 * update flows (which read the manifest before preloads run) still
 * have the autoloader available.
 *
 * @see https://www.php-fig.org/psr/psr-4/examples/
 *
 * @package xpages
 */

spl_autoload_register(
    static function (string $class): void {
        // Namespace prefix derived from the module's directory name.
        $prefix = 'XoopsModules\\' . ucfirst(basename(dirname(__DIR__)));

        if (!str_starts_with($class, $prefix . '\\')) {
            return;
        }

        $relative = substr($class, strlen($prefix) + 1);
        $file     = dirname(__DIR__) . '/class/' . str_replace('\\', '/', $relative) . '.php';

        if (is_file($file)) {
            require_once $file;
        }
    }
);
