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

if (class_exists('Xmf\\Module\\Admin')) {
    \Xmf\Module\Admin::getInstance()->displayNavigation('about.php');
}

$moduleInfoRows = [
    [_AM_XPAGES_ABOUT_LABEL_MODULE_NAME, 'xPages'],
    [_AM_XPAGES_ABOUT_LABEL_VERSION, '1.0.1'],
    [_AM_XPAGES_ABOUT_LABEL_AUTHOR, 'Eren Yumak — <a href="https://aymak.net" target="_blank" rel="noopener" style="color:#007bff;text-decoration:none">Aymak</a>'],
    [_AM_XPAGES_ABOUT_LABEL_WEBSITE, '<a href="https://aymak.net" target="_blank" rel="noopener" style="color:#007bff;text-decoration:none">https://aymak.net</a>'],
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

<div style="margin:16px 0 24px">
    <h2 style="margin:0 0 4px;font-size:22px"><?= _AM_XPAGES_ABOUT_TITLE ?></h2>
    <p style="margin:0;color:#6b7280"><?= _MI_XPAGES_DESC ?></p>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(340px,1fr));gap:20px;margin-bottom:24px">

    <div style="background:#fff;padding:20px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.07)">
        <h3 style="margin:0 0 14px;color:#4472c4;font-size:16px"><?= _AM_XPAGES_ABOUT_MODULE_INFO_TITLE ?></h3>
        <table style="width:100%;border-collapse:collapse;font-size:13px">
            <?php foreach ($moduleInfoRows as $row): ?>
            <tr style="border-bottom:1px solid #f0f0f0">
                <td style="padding:8px 0;width:38%;font-weight:600;color:#374151"><?= $row[0] ?></td>
                <td style="padding:8px 0"><?= $row[1] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div style="background:#fff;padding:20px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.07)">
        <h3 style="margin:0 0 14px;color:#4472c4;font-size:16px"><?= _AM_XPAGES_ABOUT_FEATURES_TITLE ?></h3>
        <ul style="margin:0;padding-left:18px;line-height:1.8;font-size:13px">
            <?php foreach ($featureList as $feature): ?>
            <li><?= $feature ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<div style="background:#fff;padding:22px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.07);margin-bottom:24px">
    <h3 style="margin:0 0 14px;color:#4472c4;font-size:16px"><?= _AM_XPAGES_ABOUT_TEMPLATE_TITLE ?></h3>
    <pre style="background:#1e1e1e;color:#d4d4d4;padding:16px;border-radius:8px;overflow-x:auto;font-size:12px;line-height:1.6;margin:0"><?= htmlspecialchars(_AM_XPAGES_ABOUT_SMARTY_EXAMPLE, ENT_QUOTES) ?></pre>
</div>

<div style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;padding:22px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.1);margin-bottom:24px">
    <h3 style="margin:0 0 12px;font-size:16px"><?= _AM_XPAGES_ABOUT_SUPPORT_TITLE ?></h3>
    <ul style="margin:0;padding-left:18px;line-height:1.8;font-size:13px">
        <li>🌐 <?= _AM_XPAGES_ABOUT_SUPPORT_WEB ?>: <a href="https://aymak.net" target="_blank" style="color:#ffd700;text-decoration:none">https://aymak.net</a></li>
        <li>📧 <?= _AM_XPAGES_ABOUT_SUPPORT_EMAIL ?>: <a href="mailto:info@aymak.net" style="color:#ffd700;text-decoration:none">info@aymak.net</a></li>
        <li>🐛 <?= _AM_XPAGES_ABOUT_SUPPORT_GITHUB ?>: <a href="https://github.com/aymak/xpages" target="_blank" style="color:#ffd700;text-decoration:none">github.com/aymak/xpages</a></li>
    </ul>
</div>

<div style="text-align:center;padding:16px;color:#9ca3af;font-size:12px;border-top:1px solid #e5e7eb">
    <?= _AM_XPAGES_ABOUT_FOOTER ?>
</div>

<?php
xoops_cp_footer();
