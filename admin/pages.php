<?php

declare(strict_types=1);

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
xpages_admin_register_css();

if (class_exists('Xmf\\Module\\Admin')) {
    \Xmf\Module\Admin::getInstance()->displayNavigation('pages.php');
}

$pageHandler = xpages_get_handler('page');

if (!$pageHandler) {
    echo '<div class="xp-alert xp-alert--error">' . _AM_XPAGES_HANDLER_UNAVAILABLE . '</div>';
    xoops_cp_footer();
    exit;
}

// ── Silme işlemi ──────────────────────────────────────────────────────────────
if (Request::getCmd('op', '', 'GET') === 'delete' && Request::getInt('page_id', 0, 'GET') > 0) {
    $pageId = Request::getInt('page_id', 0, 'GET');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || 1 !== Request::getInt('confirm', 0, 'POST')) {
        $pageObj = $pageHandler->get($pageId);
        if ($pageObj) {
            xpages_admin_render('xpages_admin_pages_delete_confirm.tpl', [
                'confirm_message' => sprintf(
                    _AM_XPAGES_DELETE_CONFIRM,
                    htmlspecialchars((string)$pageObj->getVar('title'), ENT_QUOTES)
                ),
                'page_id'   => $pageId,
                'label_yes' => _AM_XPAGES_YES,
                'label_no'  => _AM_XPAGES_NO,
            ]);
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
$pagesObjects = $pageHandler->getObjects($criteria) ?: [];

// Build row descriptors for the template — flat, pre-computed, so the
// template itself only iterates and echoes.
$rows = [];
foreach ($pagesObjects as $p) {
    $pid   = (int)$p->getVar('page_id');
    $alias = (string)$p->getVar('alias', 'n');
    $rows[] = [
        'id'         => $pid,
        'title'      => (string)$p->getVar('title'),
        'alias'      => $alias,
        'status'     => (int)$p->getVar('page_status'),
        'menu_order' => (int)$p->getVar('menu_order'),
        'page_url'   => $alias !== ''
            ? XOOPS_URL . '/modules/xpages/page.php?alias=' . urlencode($alias)
            : XOOPS_URL . '/modules/xpages/page.php?page_id=' . $pid,
    ];
}

xpages_admin_render('xpages_admin_pages.tpl', [
    'menu_title'         => _AM_XPAGES_MENU_PAGES,
    'add_label'          => _AM_XPAGES_MENU_ADD_PAGE,
    'col_title'          => _AM_XPAGES_PAGE_TITLE,
    'col_status'         => _AM_XPAGES_PAGE_STATUS,
    'col_actions'        => _AM_XPAGES_ACTIONS,
    'pages'              => $rows,
    'stat_text'          => sprintf(_AM_XPAGES_STAT_PAGES, count($rows)),
    'no_pages_text'      => _AM_XPAGES_NO_PAGES,
    'create_first_label' => _AM_XPAGES_CREATE_FIRST,
    'toggle_title'       => _AM_XPAGES_TOGGLE_STATUS_TITLE,
    'label_edit'         => _AM_XPAGES_EDIT,
    'label_view'         => _AM_XPAGES_PAGETO,
    'label_delete'       => _AM_XPAGES_DELETE,
]);

xoops_cp_footer();
