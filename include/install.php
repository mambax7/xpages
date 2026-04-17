<?php
/**
 * xPages module install/update/uninstall hooks.
 *
 * @package xpages
 */

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

require_once __DIR__ . '/functions.php';

/**
 * Ensure the module upload folders exist and are protected.
 */
function xpages_prepare_upload_dirs(): bool
{
    $uploadRoot = XOOPS_UPLOAD_PATH . '/xpages';
    $galleryRoot = $uploadRoot . '/gallery';

    if (!xpages_ensure_upload_dir($uploadRoot)) {
        return false;
    }

    if (!xpages_ensure_upload_dir($galleryRoot)) {
        return false;
    }

    return true;
}

/**
 * Check whether a table exists.
 */
function xpages_table_exists(string $tableName): bool
{
    $db = $GLOBALS['xoopsDB'];
    $sqlTable = $db->prefix($tableName);
    $pattern = addcslashes($sqlTable, '\\_%');
    $result = $db->query("SHOW TABLES LIKE '" . $pattern . "'");

    return $result !== false && $db->fetchRow($result) !== false;
}

/**
 * Create the gallery table if it is missing.
 */
function xpages_create_gallery_table(): bool
{
    if (xpages_table_exists('xpages_gallery')) {
        return true;
    }

    $sql = <<<'SQL'
CREATE TABLE `xpages_gallery` (
    `gallery_id`    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `page_id`       INT(10) UNSIGNED NOT NULL DEFAULT 0,
    `title`         VARCHAR(255) NOT NULL DEFAULT '',
    `description`   TEXT NOT NULL,
    `image_path`    VARCHAR(255) NOT NULL DEFAULT '',
    `image_url`     VARCHAR(500) NOT NULL DEFAULT '',
    `image_order`   SMALLINT(5) NOT NULL DEFAULT 0,
    `image_status`  TINYINT(1) NOT NULL DEFAULT 1,
    `create_date`   INT(10) UNSIGNED NOT NULL DEFAULT 0,
    `uid`           INT(10) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`gallery_id`),
    KEY `page_id` (`page_id`),
    KEY `image_order` (`image_order`)
) ENGINE=InnoDB
SQL;

    return (bool)$GLOBALS['xoopsDB']->queryF($sql);
}

/**
 * Install hook.
 */
function xoops_module_install_xpages(?\XoopsModule $module = null): bool
{
    return xpages_prepare_upload_dirs();
}

/**
 * Update hook.
 */
function xoops_module_update_xpages(?\XoopsModule $module = null, ?string $previousVersion = null): bool
{
    xpages_prepare_upload_dirs();

    return xpages_create_gallery_table();
}

/**
 * Remove module-owned upload files during uninstall.
 */
function xpages_remove_tree(string $path): bool
{
    if (!file_exists($path)) {
        return true;
    }

    try {
        if (is_file($path) || is_link($path)) {
            return @unlink($path);
        }

        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isDir() && !$item->isLink()) {
                if (!@rmdir($item->getPathname())) {
                    return false;
                }
                continue;
            }

            if (!@unlink($item->getPathname())) {
                return false;
            }
        }

        return @rmdir($path);
    } catch (\Throwable $e) {
        return false;
    }
}

/**
 * Uninstall hook.
 */
function xoops_module_uninstall_xpages(?\XoopsModule $module = null): bool
{
    $uploadRoot = XOOPS_UPLOAD_PATH . '/xpages';

    if (is_dir($uploadRoot)) {
        xpages_remove_tree($uploadRoot);
    }

    return true;
}
