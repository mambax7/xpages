<?php

declare(strict_types=1);

/**
 * xPages — PHPUnit bootstrap.
 *
 * Defines the minimum XOOPS constants needed for helper functions to
 * be includable without running a live XOOPS bootstrap. Also loads
 * Composer's autoloader so tests in tests/Unit/ can refer to
 * XoopsModules\Xpages\Tests\* classes.
 *
 * @package xpages
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Minimum XOOPS surface for include/*.php files to `if (!defined('XOOPS_ROOT_PATH')) exit();` through.
if (!defined('XOOPS_ROOT_PATH')) {
    define('XOOPS_ROOT_PATH', dirname(__DIR__, 3));
}
if (!defined('XOOPS_URL')) {
    define('XOOPS_URL', 'http://example.test');
}
if (!defined('XOOPS_UPLOAD_URL')) {
    define('XOOPS_UPLOAD_URL', XOOPS_URL . '/uploads');
}
if (!defined('XOOPS_UPLOAD_PATH')) {
    define('XOOPS_UPLOAD_PATH', sys_get_temp_dir() . '/xpages-tests-upload');
}
