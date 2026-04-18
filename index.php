<?php
/**
 * xPages — Ön yüz - Sayfa listesi
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

use Xmf\Request;

require_once '../../mainfile.php';

$xoopsOption['template_main'] = 'xpages_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once __DIR__ . '/include/functions.php';

/** @var XoopsMySQLDatabase $xoopsDB */

// Module config is in global $xoopsModuleConfig

$pageHandler = xpages_get_handler('page');
if (!$pageHandler) {
    $xoopsTpl->assign('xpages_list', []);
    $xoopsTpl->assign('xpages_total', 0);
    $xoopsTpl->assign('xpages_start', 0);
    $xoopsTpl->assign('xpages_limit', 0);
    $xoopsTpl->assign('xpages_module_url', XOOPS_URL . '/modules/xpages/');
    $xoopsTpl->assign('xoops_pagetitle', $xoopsModule->getVar('name'));
    require_once XOOPS_ROOT_PATH . '/footer.php';
    return;
}

$criteria    = new CriteriaCompo();
$criteria->add(new Criteria('page_status', 1));
$criteria->add(new Criteria('parent_id',   0));
$criteria->add(new Criteria('show_in_nav',  1));
$criteria->setSort('menu_order');
$criteria->setOrder('ASC');

$itemsPerPage = isset($xoopsModuleConfig["items_per_page"]) ? (int)$xoopsModuleConfig['items_per_page'] : 10;
$totalCount   = $pageHandler->getCount($criteria);

// Sayfalama
$start = max(0, Request::getInt('start', 0, 'GET'));
$criteria->setStart($start);
$criteria->setLimit($itemsPerPage);

$pages = $pageHandler->getObjects($criteria);

$pageList = [];
foreach ($pages as $p) {
    $pageList[] = [
        'page_id'     => $p->getVar('page_id'),
        'title'       => $p->getVar('title'),
        'short_desc'  => $p->getVar('short_desc'),
        'update_date' => $p->getVar('update_date'),
        'hits'        => $p->getVar('hits'),
        'page_url'    => $p->getPageUrl(),
    ];
}

$xoopsTpl->assign('xpages_list',        $pageList);
$xoopsTpl->assign('xpages_total',       $totalCount);
$xoopsTpl->assign('xpages_start',       $start);
$xoopsTpl->assign('xpages_limit',       $itemsPerPage);
$xoopsTpl->assign('xpages_module_url',  XOOPS_URL . '/modules/xpages/');

// Breadcrumb
$xoopsTpl->assign('xoops_pagetitle', $xoopsModule->getVar('name'));

require_once XOOPS_ROOT_PATH . '/footer.php';
