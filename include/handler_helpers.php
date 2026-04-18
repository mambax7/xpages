<?php

declare(strict_types=1);

/**
 * xPages — Handler + bootstrap helpers.
 *
 * Covers module handler resolution, language-file loading, admin auth
 * enforcement, and CSS/JS asset registration. Previously lived in
 * include/functions.php (god-file). The file is included directly by
 * include/functions.php as part of the back-compat shim so existing
 * callers (`require_once .../include/functions.php`) keep working.
 *
 * @package xpages
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * Thin wrapper around \XoopsModules\Xpages\Helper::getHandler(). The
 * parent Xmf\Module\Helper implementation loads class/{name}.php and
 * instantiates Xpages{Name}Handler; this function only normalises the
 * "not found" return (false → null) for legacy callers that do
 * `if (!$handler)`.
 */
function xpages_get_handler(string $name)
{
    $handler = \XoopsModules\Xpages\Helper::getInstance()->getHandler($name);
    return $handler !== false ? $handler : null;
}

/**
 * Admin boot — load language files + enforce module-level admin rights.
 *
 * XOOPS's include/cp_header.php verifies system-admin-group membership,
 * but a site may grant module-level admin rights to a narrower group
 * (system admin → groups → module admin). Without this guard, any user
 * in the system-admin group can reach xpages admin pages even if they
 * were only granted admin on a DIFFERENT module.
 */
function xpages_admin_boot(): void
{
    xpages_load_language('admin');
    xpages_load_language('modinfo');
    xpages_require_module_admin();
}

/**
 * Register the module's admin stylesheet + JS with the XOOPS admin theme.
 *
 * Must be called AFTER xoops_cp_header() because $GLOBALS['xoTheme'] is
 * populated by that call. All admin controllers invoke this via the
 * common bootstrap path: xpages_admin_boot() → xoops_cp_header() → this.
 */
function xpages_admin_register_css(): void
{
    if (!isset($GLOBALS['xoTheme']) || !is_object($GLOBALS['xoTheme'])) {
        return;
    }
    $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/modules/xpages/assets/css/admin.css');
    $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/modules/xpages/assets/js/admin.js');
}

/**
 * Render an admin template file with a set of assignments.
 *
 * Encapsulates the $xoopsTpl->assign()/fetch() dance every admin
 * controller performs when it needs to emit a block of HTML. Templates
 * live in modules/xpages/templates/admin/ and are looked up by filename
 * relative to that directory (no leading slash).
 *
 * Auto-injects two convenience values:
 *   - xoops_token_html — CSRF token HTML, ready to drop inside a <form>.
 *   - xpages_upload_url — module's public upload URL (for preview URLs).
 *
 * @param string              $template Filename under templates/admin/
 * @param array<string,mixed> $vars     Smarty variable assignments
 */
function xpages_admin_render(string $template, array $vars = []): void
{
    global $xoopsTpl;
    if (!isset($xoopsTpl) || !($xoopsTpl instanceof \XoopsTpl)) {
        require_once $GLOBALS['xoops']->path('class/template.php');
        $xoopsTpl = new \XoopsTpl();
    }

    // Allow <{include file="xpages_*.tpl"}> inside admin templates to
    // resolve from templates/admin/ without a full path prefix.
    $adminTplDir = XOOPS_ROOT_PATH . '/modules/xpages/templates/admin/';
    if (method_exists($xoopsTpl, 'addTemplateDir')) {
        $xoopsTpl->addTemplateDir($adminTplDir);
    }

    if (isset($GLOBALS['xoopsSecurity']) && is_object($GLOBALS['xoopsSecurity'])) {
        $xoopsTpl->assign('xoops_token_html', $GLOBALS['xoopsSecurity']->getTokenHTML());
    }
    $xoopsTpl->assign('xpages_upload_url', XOOPS_UPLOAD_URL . '/xpages/');

    foreach ($vars as $key => $value) {
        $xoopsTpl->assign($key, $value);
    }

    echo $xoopsTpl->fetch($adminTplDir . $template);
}

/**
 * Register the module's public-facing stylesheet.
 *
 * Must be called AFTER XOOPS_ROOT_PATH/header.php is included (the
 * main public header populates $xoTheme on the front-end). Used by
 * page.php and index.php so public layout can use class names instead
 * of inline style attributes in the .tpl templates.
 */
function xpages_register_public_css(): void
{
    if (!isset($GLOBALS['xoTheme']) || !is_object($GLOBALS['xoTheme'])) {
        return;
    }
    $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/modules/xpages/assets/css/style.css');
}

/**
 * Redirect any request that isn't from a user with admin rights
 * specifically for the xpages module. Call at the top of every admin
 * controller (done automatically via xpages_admin_boot()).
 */
function xpages_require_module_admin(): void
{
    // cp_header.php already enforced the system admin-group check; this
    // is defense-in-depth for the per-module admin ACL.
    $helper = \XoopsModules\Xpages\Helper::getInstance();
    $user   = $helper->user();
    $module = $helper->module();

    if ($user === null || $module === null || !$user->isAdmin($module->getVar('mid'))) {
        redirect_header(
            XOOPS_URL . '/user.php',
            3,
            defined('_NOPERM') ? _NOPERM : 'You do not have permission to access this page.'
        );
        exit;
    }
}

/**
 * Dil dosyası yükleme. Delegates to the module Helper which uses
 * Xmf\Language::load() — handles the english-fallback lookup + log
 * integration for free.
 */
function xpages_load_language(string $type = 'main'): void
{
    \XoopsModules\Xpages\Helper::getInstance()->loadLanguage($type);
}
