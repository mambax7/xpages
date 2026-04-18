<?php
/**
 * xPages — Dashboard Widget'ları
 *
 * Exposes xpages_dashboard_widgets() for legacy callers that expected a
 * standalone 6-widget grid. Data gathering is identical to admin/index.php
 * (12-month window instead of 6) but layout is a single xpages-widget-grid
 * rather than the stacked dashboard.
 *
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

function xpages_dashboard_widgets()
{
    $pageHandler    = xpages_get_handler('page');
    $fieldHandler   = xpages_get_handler('field');
    $galleryHandler = xpages_get_handler('gallery');

    if (!$pageHandler || !$fieldHandler) {
        return;
    }

    // Recent (5) + popular (5).
    $criteria = new CriteriaCompo();
    $criteria->setSort('update_date');
    $criteria->setOrder('DESC');
    $criteria->setLimit(5);
    $recentPages = $pageHandler->getObjects($criteria);

    $criteria = new CriteriaCompo();
    $criteria->setSort('hits');
    $criteria->setOrder('DESC');
    $criteria->setLimit(5);
    $popularPages = $pageHandler->getObjects($criteria);

    $totalPages  = $pageHandler->getCount();
    $totalFields = $fieldHandler->getCount();
    $totalImages = $galleryHandler ? $galleryHandler->getCount() : 0;
    $activePages = $pageHandler->getCount(new Criteria('page_status', 1));

    // 12-month activity chart.
    $monthlyStats = [];
    for ($i = 11; $i >= 0; $i--) {
        $month     = date('Y-m', strtotime("-$i months"));
        $startDate = strtotime($month . '-01');
        $endDate   = strtotime($month . '-' . date('t', $startDate) . ' 23:59:59');

        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('update_date', $startDate, '>='));
        $criteria->add(new Criteria('update_date', $endDate,   '<='));

        $monthlyStats[] = [
            'month' => date('M Y', $startDate),
            'count' => (int)$pageHandler->getCount($criteria),
        ];
    }
    $maxCount = max(array_column($monthlyStats, 'count'));
    if ($maxCount < 1) { $maxCount = 1; }

    // Flatten to template-ready descriptors.
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
        $bars[] = [
            'month'   => $stat['month'],
            'count'   => $stat['count'],
            'percent' => (float)(($stat['count'] / $maxCount) * 100),
        ];
    }

    xpages_admin_render('xpages_admin_dashboard_widgets.tpl', [
        'stats_widget' => [
            'title' => _AM_XPAGES_WIDGET_STATS,
            'cards' => [
                ['value' => $totalPages,  'label' => _AM_XPAGES_STAT_TOTAL_PAGES,   'modifier' => 'xpages-stat-card--purple'],
                ['value' => $activePages, 'label' => _AM_XPAGES_STAT_ACTIVE_PAGES,  'modifier' => 'xpages-stat-card--green'],
                ['value' => $totalFields, 'label' => _AM_XPAGES_STAT_FIELDS_COUNT,  'modifier' => 'xpages-stat-card--blue'],
                ['value' => $totalImages, 'label' => _AM_XPAGES_STAT_GALLERY_COUNT, 'modifier' => 'xpages-stat-card--orange'],
            ],
        ],
        'recent_widget' => [
            'title'         => _AM_XPAGES_WIDGET_RECENT,
            'items'         => $recentRows,
            'empty_text'    => _AM_XPAGES_NO_PAGES_YET,
            'see_all_label' => _AM_XPAGES_SEE_ALL_PAGES,
        ],
        'popular_widget' => [
            'title'      => _AM_XPAGES_WIDGET_POPULAR,
            'items'      => $popularRows,
            'empty_text' => _AM_XPAGES_NO_STATS,
        ],
        'monthly_widget' => [
            'title' => _AM_XPAGES_WIDGET_MONTHLY,
            'bars'  => $bars,
        ],
        'quick_widget' => [
            'title'   => _AM_XPAGES_WIDGET_QUICK,
            'actions' => [
                ['href' => 'page_edit.php', 'label' => _AM_XPAGES_BTN_NEW_PAGE,   'modifier' => 'xp-btn--green'],
                ['href' => 'fields.php',    'label' => _AM_XPAGES_BTN_NEW_FIELD,  'modifier' => 'xp-btn--blue'],
                ['href' => 'pages.php',     'label' => _AM_XPAGES_BTN_LIST_PAGES, 'modifier' => 'xp-btn--cancel'],
            ],
        ],
        'sysinfo_widget' => [
            'title' => _AM_XPAGES_WIDGET_SYSINFO,
            'rows'  => [
                ['label' => _AM_XPAGES_SYSINFO_XOOPS,   'value' => XOOPS_VERSION],
                ['label' => _AM_XPAGES_SYSINFO_MODULE,  'value' => '1.0'],
                ['label' => _AM_XPAGES_SYSINFO_PHP,     'value' => PHP_VERSION],
                ['label' => _AM_XPAGES_SYSINFO_UPDATED, 'value' => date('d.m.Y H:i')],
            ],
        ],
    ]);
}
