<?php
/**
 * xPages — Admin ana sayfa / dashboard
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

include_once '../../../include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/modules/xpages/include/functions.php';
xpages_admin_boot();

xoops_cp_header();
xpages_admin_register_css();

if (class_exists('Xmf\\Module\\Admin')) {
    \Xmf\Module\Admin::getInstance()->displayNavigation('index.php');
}

$pageHandler    = xpages_get_handler('page');
$fieldHandler   = xpages_get_handler('field');
$galleryHandler = xpages_get_handler('gallery');

// Aggregate counters (handlers may be null if module tables absent).
$totalPages  = $pageHandler    ? $pageHandler->getCount()                              : 0;
$totalFields = $fieldHandler   ? $fieldHandler->getCount()                             : 0;
$totalImages = $galleryHandler ? $galleryHandler->getCount()                           : 0;
$activePages = $pageHandler    ? $pageHandler->getCount(new Criteria('page_status', 1)) : 0;

// Recent (5 latest updates) + popular (5 most-hit).
$criteria = new CriteriaCompo();
$criteria->setSort('update_date');
$criteria->setOrder('DESC');
$criteria->setLimit(5);
$recentPages = $pageHandler ? $pageHandler->getObjects($criteria) : [];

$criteria = new CriteriaCompo();
$criteria->setSort('hits');
$criteria->setOrder('DESC');
$criteria->setLimit(5);
$popularPages = $pageHandler ? $pageHandler->getObjects($criteria) : [];

// 6-month activity chart (update_date per calendar month).
$monthlyStats = [];
for ($i = 5; $i >= 0; $i--) {
    $month     = date('Y-m', strtotime("-$i months"));
    $startDate = strtotime($month . '-01');
    $endDate   = strtotime($month . '-' . date('t', $startDate) . ' 23:59:59');

    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('update_date', $startDate, '>='));
    $criteria->add(new Criteria('update_date', $endDate,   '<='));

    $monthlyStats[] = [
        'month' => date('M Y', $startDate),
        'count' => $pageHandler ? $pageHandler->getCount($criteria) : 0,
    ];
}
$maxCount = !empty($monthlyStats) ? max(array_column($monthlyStats, 'count')) : 1;
if ($maxCount < 1) { $maxCount = 1; }

// Flatten XoopsObject lists → template-ready rows.
$recentRows = [];
foreach ($recentPages ?: [] as $page) {
    $recentRows[] = [
        'id'    => (int)$page->getVar('page_id'),
        'title' => (string)$page->getVar('title'),
        'date'  => date('d.m.Y', (int)$page->getVar('update_date')),
    ];
}

$popularRows = [];
foreach ($popularPages ?: [] as $page) {
    $popularRows[] = [
        'id'             => (int)$page->getVar('page_id'),
        'title'          => (string)$page->getVar('title'),
        'hits_formatted' => number_format((int)$page->getVar('hits')),
    ];
}

$bars = [];
foreach ($monthlyStats as $stat) {
    $percent = ($stat['count'] / $maxCount) * 100;
    if ($percent < 5) { $percent = 5; }
    $bars[] = [
        'month'   => $stat['month'],
        'count'   => (int)$stat['count'],
        'percent' => (float)$percent,
    ];
}

xpages_admin_render('xpages_admin_index.tpl', [
    'dashboard_title'    => _AM_XPAGES_DASHBOARD,
    'dashboard_subtitle' => _AM_XPAGES_DASHBOARD_SUBTITLE,
    'stat_cards'         => [
        ['value' => $totalPages,  'label' => _AM_XPAGES_STAT_TOTAL_PAGES_LBL,  'modifier' => 'xpages-stat-card--purple'],
        ['value' => $activePages, 'label' => _AM_XPAGES_STAT_ACTIVE_PAGES_LBL, 'modifier' => 'xpages-stat-card--green'],
        ['value' => $totalFields, 'label' => _AM_XPAGES_STAT_FIELDS_LBL,       'modifier' => 'xpages-stat-card--blue'],
        ['value' => $totalImages, 'label' => _AM_XPAGES_STAT_GALLERY_LBL,      'modifier' => 'xpages-stat-card--orange'],
    ],
    'recent_widget' => [
        'title'         => _AM_XPAGES_RECENT_PAGES_WIDGET,
        'items'         => $recentRows,
        'empty_text'    => _AM_XPAGES_NO_PAGES_YET,
        'see_all_label' => _AM_XPAGES_SEE_ALL_PAGES,
        'see_all_url'   => 'pages.php',
    ],
    'popular_widget' => [
        'title'      => _AM_XPAGES_POPULAR_PAGES_WIDGET,
        'items'      => $popularRows,
        'empty_text' => _AM_XPAGES_NO_STATS,
    ],
    'monthly_widget' => [
        'title' => _AM_XPAGES_MONTHLY_STATS,
        'bars'  => $bars,
    ],
    'quick_widget' => [
        'title'   => _AM_XPAGES_QUICK_ACTIONS_WIDGET,
        'actions' => [
            ['href' => 'page_edit.php', 'label' => _AM_XPAGES_BTN_NEW_PAGE,    'modifier' => 'xp-btn--green'],
            ['href' => 'fields.php',    'label' => _AM_XPAGES_BTN_NEW_FIELD,   'modifier' => 'xp-btn--blue'],
            ['href' => 'gallery.php',   'label' => _AM_XPAGES_BTN_GALLERY,     'modifier' => 'xp-btn--orange'],
            ['href' => 'pages.php',     'label' => _AM_XPAGES_BTN_LIST_PAGES,  'modifier' => 'xp-btn--gray'],
        ],
    ],
    'sysinfo_widget' => [
        'title' => _AM_XPAGES_SYS_INFO_WIDGET,
        'rows'  => [
            ['label' => _AM_XPAGES_SYSINFO_XOOPS,   'value' => XOOPS_VERSION],
            ['label' => _AM_XPAGES_SYSINFO_MODULE,  'value' => '1.0'],
            ['label' => _AM_XPAGES_SYSINFO_PHP,     'value' => PHP_VERSION],
            ['label' => _AM_XPAGES_SYSINFO_UPDATED, 'value' => date('d.m.Y H:i:s')],
        ],
    ],
]);

xoops_cp_footer();
