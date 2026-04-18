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

$moduleInfoRows = [
    [_AM_XPAGES_ABOUT_LABEL_MODULE_NAME, 'xPages'],
    [_AM_XPAGES_ABOUT_LABEL_VERSION, '1.0.1'],
    [_AM_XPAGES_ABOUT_LABEL_AUTHOR, 'Eren Yumak — <a href="https://aymak.net" target="_blank" rel="noopener" class="xpages-muted-link">Aymak</a>'],
    [_AM_XPAGES_ABOUT_LABEL_WEBSITE, '<a href="https://aymak.net" target="_blank" rel="noopener" class="xpages-muted-link">https://aymak.net</a>'],
    [_AM_XPAGES_ABOUT_LABEL_LICENSE, 'GNU General Public License v2'],
    [_AM_XPAGES_ABOUT_LABEL_COMPATIBILITY, 'XOOPS 2.7.0+, PHP 7.0+, MySQL/MariaDB 5.6+'],
    [_AM_XPAGES_ABOUT_LABEL_ENCODING, 'UTF-8'],
];

$featureList = [
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
?>

<div class="xpages-page-header">
    <h2><?= _AM_XPAGES_ABOUT_TITLE ?></h2>
    <p><?= _MI_XPAGES_DESC ?></p>
</div>

<div class="xpages-info-grid">

    <div class="xpages-info-card">
        <h3><?= _AM_XPAGES_ABOUT_MODULE_INFO_TITLE ?></h3>
        <table class="xpages-info-table">
            <?php foreach ($moduleInfoRows as $row): ?>
                <tr>
                    <td><?= $row[0] ?></td>
                    <td><?= $row[1] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="xpages-info-card">
        <h3><?= _AM_XPAGES_ABOUT_FEATURES_TITLE ?></h3>
        <ul class="xpages-info-list">
            <?php foreach ($featureList as $feature): ?>
                <li><?= $feature ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<div class="xpages-info-card xpages-info-card--wide xpages-info-card--spaced">
    <h3><?= _AM_XPAGES_ABOUT_TEMPLATE_TITLE ?></h3>
    <pre class="xpages-code-example"><?= htmlspecialchars(_AM_XPAGES_ABOUT_SMARTY_EXAMPLE, ENT_QUOTES) ?></pre>
</div>

<div class="xpages-support-callout">
    <h3><?= _AM_XPAGES_ABOUT_SUPPORT_TITLE ?></h3>
    <ul class="xpages-info-list">
        <li>🌐 <?= _AM_XPAGES_ABOUT_SUPPORT_WEB ?>: <a href="https://aymak.net" target="_blank" rel="noopener">https://aymak.net</a></li>
        <li>📧 <?= _AM_XPAGES_ABOUT_SUPPORT_EMAIL ?>: <a href="mailto:info@aymak.net">info@aymak.net</a></li>
        <li>🐛 <?= _AM_XPAGES_ABOUT_SUPPORT_GITHUB ?>: <a href="https://github.com/aymak/xpages" target="_blank" rel="noopener">github.com/aymak/xpages</a></li>
    </ul>
</div>

<div class="xpages-page-footer">
    <?= _AM_XPAGES_ABOUT_FOOTER ?>
</div>

<?php
xoops_cp_footer();
