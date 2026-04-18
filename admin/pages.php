<?php
/**
 * xPages — Admin sayfa listesi
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

use Xmf\Request;

include_once '../../../include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/modules/xpages/include/functions.php';
xpages_admin_boot();

xoops_cp_header();

if (class_exists('Xmf\\Module\\Admin')) {
    \Xmf\Module\Admin::getInstance()->displayNavigation('pages.php');
}

$pageHandler = xpages_get_handler('page');

if (!$pageHandler) {
    echo '<div style="margin:18px 0;padding:14px 16px;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;border-radius:6px">xPages handler unavailable.</div>';
    xoops_cp_footer();
    exit;
}

// ── Silme işlemi ──────────────────────────────────────────────────────────────
if (Request::getCmd('op', '', 'GET') === 'delete' && Request::getInt('page_id', 0, 'GET') > 0) {
    $pageId = Request::getInt('page_id', 0, 'GET');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || 1 !== Request::getInt('confirm', 0, 'POST')) {
        $pageObj = $pageHandler->get($pageId);
        if ($pageObj) {
            echo '<div style="background:#fff3cd;border:1px solid #ffc107;padding:20px;margin:16px 0;border-radius:8px">';
            echo '<p style="font-size:15px;margin:0 0 14px">⚠️ ' . sprintf(_AM_XPAGES_DELETE_CONFIRM, htmlspecialchars((string)$pageObj->getVar('title'), ENT_QUOTES)) . '</p>';
            echo '<form method="post" action="pages.php?op=delete&page_id=' . $pageId . '" style="display:flex;gap:10px;align-items:center">';
            echo '<input type="hidden" name="op" value="delete">';
            echo '<input type="hidden" name="page_id" value="' . $pageId . '">';
            echo '<input type="hidden" name="confirm" value="1">';
            echo $GLOBALS['xoopsSecurity']->getTokenHTML();
            echo '<button type="submit" style="background:#dc3545;color:#fff;padding:7px 16px;border:none;border-radius:5px;cursor:pointer">' . _AM_XPAGES_YES . '</button>';
            echo '<a href="pages.php" style="background:#6c757d;color:#fff;padding:7px 16px;text-decoration:none;border-radius:5px">' . _AM_XPAGES_NO . '</a>';
            echo '</form></div>';
        }
        xoops_cp_footer();
        exit;
    }

    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header('pages.php', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        exit;
    }

    $pageObj = $pageHandler->get($pageId);
    if ($pageObj) {
        xpages_delete_page_data($pageId);
        $pageHandler->delete($pageObj);
        redirect_header('pages.php', 2, _AM_XPAGES_PAGE_DELETED);
        exit;
    }
    redirect_header('pages.php', 2, _AM_XPAGES_PAGE_NOT_FOUND);
    exit;
}

// ── Durum değiştir ────────────────────────────────────────────────────────────
if (Request::getCmd('op', '', 'POST') === 'toggle' && Request::getInt('page_id', 0, 'POST') > 0) {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header('pages.php', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        exit;
    }

    $pageObj = $pageHandler->get(Request::getInt('page_id', 0, 'POST'));
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
    echo '<th style="padding:11px 14px;text-align:left;font-size:13px">URL Alias</th>';
    echo '<th style="padding:11px 14px;text-align:center;font-size:13px">' . _AM_XPAGES_PAGE_STATUS . '</th>';
    echo '<th style="padding:11px 14px;text-align:center;font-size:13px">Sort Order</th>';
    echo '<th style="padding:11px 14px;text-align:center;font-size:13px">' . _AM_XPAGES_ACTIONS . '</th>';
    echo '</tr></thead><tbody>';

    foreach ($pages as $i => $p) {
        $pid    = (int)$p->getVar('page_id');
        $status = (int)$p->getVar('page_status');
        $bg     = $i % 2 ? '#f8f9fa' : '#fff';
        
        // Sayfa URL'ini oluştur
        $alias = $p->getVar('alias', 'n');
        if (!empty($alias)) {
            $pageUrl = XOOPS_URL . '/modules/xpages/page.php?alias=' . urlencode($alias);
        } else {
            $pageUrl = XOOPS_URL . '/modules/xpages/page.php?page_id=' . $pid;
        }

        echo '<tr style="border-bottom:1px solid #dee2e6;background:' . $bg . '">';
        echo '<td style="padding:11px 14px;font-size:13px">' . $pid . '</td>';
        echo '<td style="padding:11px 14px"><strong>' . htmlspecialchars((string)$p->getVar('title'), ENT_QUOTES) . '</strong></td>';
        echo '<td style="padding:11px 14px"><code style="background:#f1f3f5;padding:2px 6px;border-radius:3px;font-size:12px">' . htmlspecialchars($alias, ENT_QUOTES) . '</code></td>';
        echo '<td style="padding:11px 14px;text-align:center">';
        echo '<form method="post" action="pages.php" style="display:inline;margin:0">';
        echo '<input type="hidden" name="op" value="toggle">';
        echo '<input type="hidden" name="page_id" value="' . $pid . '">';
        echo $GLOBALS['xoopsSecurity']->getTokenHTML();
        echo '<button type="submit" title="' . _AM_XPAGES_TOGGLE_STATUS_TITLE . '" style="background:none;border:none;padding:0;text-decoration:none;cursor:pointer">';
        echo $status ? '✅ Aktif' : '❌ Pasif';
        echo '</button></form></td>';
        echo '<td style="padding:11px 14px;text-align:center">' . (int)$p->getVar('menu_order') . '</td>';
        echo '<td style="padding:11px 14px;text-align:center">';
        echo '<div style="display:flex;gap:10px;justify-content:center">';
        echo '<a href="page_edit.php?page_id=' . $pid . '" style="color:#007bff;text-decoration:none;font-size:13px" title="' . _AM_XPAGES_EDIT . '">✏️ ' . _AM_XPAGES_EDIT . '</a>';
        echo '<a href="' . $pageUrl . '" target="_blank" style="color:#17a2b8;text-decoration:none;font-size:13px" title="' . _AM_XPAGES_PAGETO . '">👁️ ' . _AM_XPAGES_PAGETO . '</a>';
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
?>
