<?php
/**
 * xPages — Ön yüz - Tekil sayfa görünümü
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

require_once '../../mainfile.php';

$xoopsOption['template_main'] = 'xpages_page.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once __DIR__ . '/include/functions.php';

$pageHandler = xpages_get_handler('page');

// Sayfayı bul: alias veya page_id
$page = null;
if (!empty($_GET['alias'])) {
    // Alias: yalnızca izin verilen karakterler, max 255
    $alias = preg_replace('/[^a-zA-Z0-9\-_]/', '', substr(rawurldecode($_GET['alias']), 0, 255));
    if ($alias !== '') {
        $page = $pageHandler->getByAlias($alias);
    }
} elseif (!empty($_GET['page_id'])) {
    $page = $pageHandler->get((int)$_GET['page_id']);
}

if (!$page || (int)$page->getVar('page_status') !== 1) {
    redirect_header(XOOPS_URL . '/modules/xpages/', 3, _MD_XPAGES_PAGE_NOT_FOUND);
    exit;
}

// 301 Yönlendirme
$redirect = $page->getVar('redirect_url', 'n');
if ($redirect) {
    header('Location: ' . $redirect, true, 301);
    exit;
}

// İsabeti artır
$pageHandler->incrementHits((int)$page->getVar('page_id'));

// Şablon değişkenleri
xpages_assign_page($page, $xoopsTpl);

// Meta tags
$metaTitle = $page->getVar('meta_title', 'n') ?: $page->getVar('title', 'n');
$metaDesc  = $page->getVar('meta_desc', 'n');
$metaKw    = $page->getVar('meta_keywords', 'n');

$xoopsTpl->assign('xoops_pagetitle', htmlspecialchars((string)$metaTitle, ENT_QUOTES));

// Meta etiketleri için doğru XOOPS yöntemi
if ($metaDesc) {
    $xoopsTpl->assign('xoops_meta_description', htmlspecialchars((string)$metaDesc, ENT_QUOTES));
}
if ($metaKw) {
    $xoopsTpl->assign('xoops_meta_keywords', htmlspecialchars((string)$metaKw, ENT_QUOTES));
}

// Robots
$robots = $page->getRobots();
if ($robots !== 'index, follow') {
    $xoopsTpl->assign('xpages_robots', $robots);
}

// Header/Footer kod enjeksiyonu
$headerCode = $page->getVar('header_code', 'n');
$footerCode = $page->getVar('footer_code', 'n');
if ($headerCode) {
    $xoopsTpl->assign('xpages_header_code', $headerCode);
}
if ($footerCode) {
    $xoopsTpl->assign('xpages_footer_code', $footerCode);
}

// Yorumlar — $xoopsModuleConfig global değişkenini kullan
if (!empty($xoopsModuleConfig['allow_comments'])) {
    $xoopsTpl->assign('xpages_show_comments', true);
    require_once XOOPS_ROOT_PATH . '/include/comment_view.php';
}

$xoopsTpl->assign('xpages_module_url',      XOOPS_URL . '/modules/xpages/');
$xoopsTpl->assign('xpages_show_lastmod',    (bool)($xoopsModuleConfig['show_lastmod']    ?? 1));
$xoopsTpl->assign('xpages_show_breadcrumb', (bool)($xoopsModuleConfig['show_breadcrumb'] ?? 1));

require_once XOOPS_ROOT_PATH . '/footer.php';
