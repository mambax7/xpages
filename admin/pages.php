<?php
/**
 * xPages — Admin sayfa listesi
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

include_once '../../../include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/modules/xpages/include/functions.php';
xpages_admin_boot();

xoops_cp_header();

if (class_exists('Xmf\\Module\\Admin')) {
    \Xmf\Module\Admin::getInstance()->displayNavigation('pages.php');
}

$pageHandler = xpages_get_handler('page');

// ── Silme işlemi ──────────────────────────────────────────────────────────────
if (!empty($_GET['op']) && $_GET['op'] === 'delete' && !empty($_GET['page_id'])) {
    $pageId = (int)$_GET['page_id'];

    if (!isset($_GET['confirm'])) {
        $pageObj = $pageHandler->get($pageId);
        if ($pageObj) {
            echo '<div style="background:#fff3cd;border:1px solid #ffc107;padding:20px;margin:16px 0;border-radius:8px">';
            echo '<p style="font-size:15px;margin:0 0 14px">⚠️ ' . sprintf(_AM_XPAGES_DELETE_CONFIRM, htmlspecialchars((string)$pageObj->getVar('title'), ENT_QUOTES)) . '</p>';
            echo '<div style="display:flex;gap:10px">';
            echo '<a href="pages.php?op=delete&page_id=' . $pageId . '&confirm=1" style="background:#dc3545;color:#fff;padding:7px 16px;text-decoration:none;border-radius:5px">' . _AM_XPAGES_YES . '</a>';
            echo '<a href="pages.php" style="background:#6c757d;color:#fff;padding:7px 16px;text-decoration:none;border-radius:5px">' . _AM_XPAGES_NO . '</a>';
            echo '</div></div>';
        }
        xoops_cp_footer();
        exit;
    }

    $pageObj = $pageHandler->get($pageId);
    if ($pageObj) {
        xpages_delete_page_data($pageId);
        $pageHandler->delete($pageObj);
        redirect_header('pages.php', 2, _AM_XPAGES_PAGE_DELETED);
    }
    exit;
}

// ── Durum değiştir ────────────────────────────────────────────────────────────
if (!empty($_GET['op']) && $_GET['op'] === 'toggle' && !empty($_GET['page_id'])) {
    $pageObj = $pageHandler->get((int)$_GET['page_id']);
    if ($pageObj) {
        $pageObj->setVar('page_status', (int)!$pageObj->getVar('page_status'));
        $pageHandler->insert($pageObj);
    }
    redirect_header('pages.php', 0, '');
    exit;
}

// ── Liste ─────────────────────────────────────────────────────────────────────
$criteria = new CriteriaCompo();
$criteria->setSort('menu_order');
$criteria->setOrder('ASC');
$pages = $pageHandler->getObjects($criteria) ?: [];

echo '<div style="display:flex;align-items:center;justify-content:space-between;margin:16px 0 20px">';
echo '<h2 style="margin:0;font-size:20px">📄 ' . _AM_XPAGES_MENU_PAGES . '</h2>';
echo '<a href="page_edit.php" style="background:#28a745;color:#fff;padding:8px 16px;text-decoration:none;border-radius:6px;font-size:13px">➕ ' . _AM_XPAGES_MENU_ADD_PAGE . '</a>';
echo '</div>';

if (count($pages) > 0) {
    echo '<div style="overflow-x:auto">';
    echo '<table style="width:100%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08)">';
    echo '<thead>';
    echo '<tr style="background:#f8f9fa;border-bottom:2px solid #dee2e6">';
    echo '<th style="padding:11px 14px;text-align:left;font-size:13px">ID</th>';
    echo '<th style="padding:11px 14px;text-align:left;font-size:13px">' . _AM_XPAGES_PAGE_TITLE . '</th>';
    echo '<th style="padding:11px 14px;text-align:left;font-size:13px">' . _AM_XPAGES_PAGE_ALIAS . '</th>';
    echo '<th style="padding:11px 14px;text-align:center;font-size:13px">' . _AM_XPAGES_PAGE_STATUS . '</th>';
    echo '<th style="padding:11px 14px;text-align:center;font-size:13px">' . _AM_XPAGES_PAGE_ORDER . '</th>';
    echo '<th style="padding:11px 14px;text-align:center;font-size:13px">' . _AM_XPAGES_ACTIONS . '</th>';
    echo '</tr></thead><tbody>';

    foreach ($pages as $i => $p) {
        $pid    = (int)$p->getVar('page_id');
        $status = (int)$p->getVar('page_status');
        $bg     = $i % 2 ? '#f8f9fa' : '#fff';

        echo '<tr style="border-bottom:1px solid #dee2e6;background:' . $bg . '">';
        echo '<td style="padding:11px 14px;font-size:13px">' . $pid . '</td>';
        echo '<td style="padding:11px 14px"><strong>' . htmlspecialchars((string)$p->getVar('title'), ENT_QUOTES) . '</strong></td>';
        echo '<td style="padding:11px 14px"><code style="background:#f1f3f5;padding:2px 6px;border-radius:3px;font-size:12px">' . htmlspecialchars((string)$p->getVar('alias', 'n'), ENT_QUOTES) . '</code></td>';
        echo '<td style="padding:11px 14px;text-align:center">';
        echo '<a href="pages.php?op=toggle&page_id=' . $pid . '" title="<?= _AM_XPAGES_TOGGLE_STATUS_TITLE ?>" style="text-decoration:none">';
        echo $status ? _AM_XPAGES_STATUS_ACTIVE : _AM_XPAGES_STATUS_INACTIVE;
        echo '</a></td>';
        echo '<td style="padding:11px 14px;text-align:center">' . (int)$p->getVar('menu_order') . '</td>';
        echo '<td style="padding:11px 14px;text-align:center">';
        echo '<div style="display:flex;gap:10px;justify-content:center">';
        echo '<a href="page_edit.php?page_id=' . $pid . '" style="color:#007bff;text-decoration:none;font-size:13px" title="' . _AM_XPAGES_EDIT . '">✏️ ' . _AM_XPAGES_EDIT . '</a>';
        echo '<a href="pages.php?op=delete&page_id=' . $pid . '" style="color:#dc3545;text-decoration:none;font-size:13px" title="' . _AM_XPAGES_DELETE . '">🗑️ ' . _AM_XPAGES_DELETE . '</a>';
        echo '</div></td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';

    echo '<div style="margin-top:14px;padding:10px;background:#e9ecef;border-radius:6px;text-align:center;font-size:13px">';
    echo '📊 ' . sprintf(_AM_XPAGES_STAT_PAGES, count($pages));
    echo '</div>';

} else {
    echo '<div style="background:#fff;padding:40px;border-radius:12px;text-align:center;box-shadow:0 2px 8px rgba(0,0,0,.07)">';
    echo '<div style="font-size:46px;margin-bottom:10px">📭</div>';
    echo '<div style="font-size:17px;color:#6b7280">' . _AM_XPAGES_NO_PAGES . '</div>';
    echo '<a href="page_edit.php" style="display:inline-block;margin-top:16px;background:linear-gradient(135deg,#10b981,#059669);color:#fff;padding:10px 22px;border-radius:8px;text-decoration:none">' . _AM_XPAGES_CREATE_FIRST . '</a>';
    echo '</div>';
}

xoops_cp_footer();
