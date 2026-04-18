<?php

declare(strict_types=1);

/**
 * xPages — PHPUnit bootstrap.
 *
 * Sets up the minimum environment for unit tests: Composer autoload,
 * XOOPS constants, and class stubs for XoopsObject /
 * XoopsPersistableObjectHandler / Criteria / CriteriaCompo so handler
 * files can be included without a running XOOPS instance.
 *
 * @package xpages
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Minimum XOOPS surface for include/*.php files to
// `if (!defined('XOOPS_ROOT_PATH')) exit();` through.
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

// XOOPS core class stubs — load before any tests that include handler
// files extending XoopsPersistableObjectHandler.
require_once __DIR__ . '/stubs/xoops_stubs.php';
