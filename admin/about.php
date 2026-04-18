<?php
/**
 * xPages — Admin hakkında sayfası
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

include_once '../../../include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/modules/xpages/include/functions.php';
xpages_admin_boot();

xoops_cp_header();
xpages_admin_register_css();

if (class_exists('Xmf\\Module\\Admin')) {
    \Xmf\Module\Admin::getInstance()->displayNavigation('about.php');
}

// ── Data assembly ────────────────────────────────────────────────────────────
// Assemble raw data here, then hand it to the Smarty template. The template
// (templates/admin/xpages_admin_about.tpl) owns all the HTML; this file is
// the view controller.
$infoRows = [
    ['label' => _AM_XPAGES_ABOUT_LABEL_MODULE_NAME,  'value' => 'xPages'],
    ['label' => _AM_XPAGES_ABOUT_LABEL_VERSION,      'value' => '1.0.1'],
    ['label' => _AM_XPAGES_ABOUT_LABEL_AUTHOR,       'value' => 'Eren Yumak — <a href="https://aymak.net" target="_blank" rel="noopener" class="xpages-muted-link">Aymak</a>'],
    ['label' => _AM_XPAGES_ABOUT_LABEL_WEBSITE,      'value' => '<a href="https://aymak.net" target="_blank" rel="noopener" class="xpages-muted-link">https://aymak.net</a>'],
    ['label' => _AM_XPAGES_ABOUT_LABEL_LICENSE,      'value' => 'GNU General Public License v2'],
    ['label' => _AM_XPAGES_ABOUT_LABEL_COMPATIBILITY,'value' => 'XOOPS 2.7.0+, PHP 7.0+, MySQL/MariaDB 5.6+'],
    ['label' => _AM_XPAGES_ABOUT_LABEL_ENCODING,     'value' => 'UTF-8'],
];

$features = [
    _AM_XPAGES_ABOUT_FEATURE_1,
    _AM_XPAGES_ABOUT_FEATURE_2,
    _AM_XPAGES_ABOUT_FEATURE_3,
    _AM_XPAGES_ABOUT_FEATURE_4,
    _AM_XPAGES_ABOUT_FEATURE_5,
    _AM_XPAGES_ABOUT_FEATURE_6,
    _AM_XPAGES_ABOUT_FEATURE_7,
    _AM_XPAGES_ABOUT_FEATURE_8,
    _AM_XPAGES_ABOUT_FEATURE_9,
];

$supportLinks = [
    ['icon' => '🌐', 'label' => _AM_XPAGES_ABOUT_SUPPORT_WEB,    'href' => 'https://aymak.net',                 'text' => 'https://aymak.net',          'new_window' => true],
    ['icon' => '📧', 'label' => _AM_XPAGES_ABOUT_SUPPORT_EMAIL,  'href' => 'mailto:info@aymak.net',             'text' => 'info@aymak.net',             'new_window' => false],
    ['icon' => '🐛', 'label' => _AM_XPAGES_ABOUT_SUPPORT_GITHUB, 'href' => 'https://github.com/aymak/xpages',   'text' => 'github.com/aymak/xpages',    'new_window' => true],
];

xpages_admin_render('xpages_admin_about.tpl', [
    'about_title'        => _AM_XPAGES_ABOUT_TITLE,
    'about_desc'         => _MI_XPAGES_DESC,
    'module_info_title'  => _AM_XPAGES_ABOUT_MODULE_INFO_TITLE,
    'info_rows'          => $infoRows,
    'features_title'     => _AM_XPAGES_ABOUT_FEATURES_TITLE,
    'features'           => $features,
    'template_title'     => _AM_XPAGES_ABOUT_TEMPLATE_TITLE,
    'code_example'       => _AM_XPAGES_ABOUT_SMARTY_EXAMPLE,
    'support_title'      => _AM_XPAGES_ABOUT_SUPPORT_TITLE,
    'support_links'      => $supportLinks,
    'footer_text'        => _AM_XPAGES_ABOUT_FOOTER,
]);

xoops_cp_footer();
