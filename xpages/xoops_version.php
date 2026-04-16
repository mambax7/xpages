<?php
/**
 * xPages - Static Pages Module for XOOPS 2.7.0
 * @package    xpages
 * @author     Eren Yumak — Aymak (aymak.net)
 * @copyright  2024 Eren Yumak
 * @license    GPL 2.0
 */

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

$modversion = [];

$modversion['name']            = _MI_XPAGES_NAME;
$modversion['version']         = 1.0;
$modversion['description']     = _MI_XPAGES_DESC;
$modversion['author']          = 'Eren Yumak — Aymak';
$modversion['credits']         = 'Eren Yumak';
$modversion['help']            = '';
$modversion['license']         = 'GPL 2.0';
$modversion['official']        = 0;
$modversion['image']           = 'images/logo.png';
$modversion['dirname']         = 'xpages';
$modversion['modactivate']     = 1;
$modversion['moddeactivate']   = 1;
$modversion['hasAdmin']        = 1;
$modversion['adminindex']      = 'admin/index.php';
$modversion['adminmenu']       = 'admin/menu.php';
$modversion['system_menu']     = 1;
$modversion['hasmain']         = 1;
$modversion['mainfile']        = 'index.php';
$modversion['sub']             = [];

// ── Sayfalar tablosu ──────────────────────────────────────────────────────────
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';

$modversion['tables'][0] = 'xpages_pages';
$modversion['tables'][1] = 'xpages_fields';
$modversion['tables'][2] = 'xpages_field_values';

// ── Bloklar ───────────────────────────────────────────────────────────────────
$modversion['blocks'][1] = [
    'file'        => 'blocks/xpages_blocks.php',
    'name'        => _MI_XPAGES_BLOCK_RECENT,
    'description' => _MI_XPAGES_BLOCK_RECENT_DESC,
    'show_func'   => 'xpages_block_recent',
    'edit_func'   => 'xpages_block_recent_edit',
    'options'     => '5|1',
    'template'    => 'xpages_block_recent.tpl',
];

$modversion['blocks'][2] = [
    'file'        => 'blocks/xpages_blocks.php',
    'name'        => _MI_XPAGES_BLOCK_MENU,
    'description' => _MI_XPAGES_BLOCK_MENU_DESC,
    'show_func'   => 'xpages_block_menu',
    'edit_func'   => '',
    'options'     => '',
    'template'    => 'xpages_block_menu.tpl',
];

// ── Şablonlar ─────────────────────────────────────────────────────────────────
$modversion['templates'][1]  = ['file' => 'xpages_index.tpl',        'description' => ''];
$modversion['templates'][2]  = ['file' => 'xpages_page.tpl',         'description' => ''];
$modversion['templates'][3]  = ['file' => 'xpages_block_recent.tpl', 'description' => ''];
$modversion['templates'][4]  = ['file' => 'xpages_block_menu.tpl',   'description' => ''];

// ── Arama ─────────────────────────────────────────────────────────────────────
$modversion['hasSearch']     = 1;
$modversion['search']['file']   = 'include/search.php';
$modversion['search']['func']   = 'xpages_search';

// ── Yorumlar ─────────────────────────────────────────────────────────────────
$modversion['hasComments']   = 1;
$modversion['comments']['itemName'] = 'page_id';
$modversion['comments']['pageName'] = 'page.php';
$modversion['comments']['callbackFile'] = 'include/comment_functions.php';
$modversion['comments']['callbackFunc'] = 'xpages_comments_approve';

// ── Admin menü bağlantıları ───────────────────────────────────────────────────
$modversion['config'][1] = [
    'name'        => 'items_per_page',
    'title'       => '_MI_XPAGES_ITEMS_PER_PAGE',
    'description' => '_MI_XPAGES_ITEMS_PER_PAGE_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 10,
];

$modversion['config'][2] = [
    'name'        => 'allow_comments',
    'title'       => '_MI_XPAGES_ALLOW_COMMENTS',
    'description' => '_MI_XPAGES_ALLOW_COMMENTS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][3] = [
    'name'        => 'meta_keywords',
    'title'       => '_MI_XPAGES_META_KEYWORDS',
    'description' => '_MI_XPAGES_META_KEYWORDS_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '',
];

$modversion['config'][4] = [
    'name'        => 'meta_description',
    'title'       => '_MI_XPAGES_META_DESCRIPTION',
    'description' => '_MI_XPAGES_META_DESCRIPTION_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '',
];

$modversion['config'][5] = [
    'name'        => 'show_breadcrumb',
    'title'       => '_MI_XPAGES_SHOW_BREADCRUMB',
    'description' => '_MI_XPAGES_SHOW_BREADCRUMB_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][6] = [
    'name'        => 'show_lastmod',
    'title'       => '_MI_XPAGES_SHOW_LASTMOD',
    'description' => '_MI_XPAGES_SHOW_LASTMOD_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

// Admin menü bağlantılarına galeriyi ekleyin
$adminmenu[] = [
    'title' => '🖼️ Galeri',
    'link'  => 'admin/gallery.php',
    'icon'  => 'images/admin/gallery.png',
];