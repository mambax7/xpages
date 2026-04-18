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
    $adminObject = \Xmf\Module\Admin::getInstance();
    $adminObject->displayNavigation('index.php');
}

$pageHandler    = xpages_get_handler('page');
$fieldHandler   = xpages_get_handler('field');
$galleryHandler = xpages_get_handler('gallery');

$totalPages  = $pageHandler ? $pageHandler->getCount() : 0;
$totalFields = $fieldHandler ? $fieldHandler->getCount() : 0;
$totalImages = $galleryHandler ? $galleryHandler->getCount() : 0;
$activePages = $pageHandler ? $pageHandler->getCount(new Criteria('page_status', 1)) : 0;

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

$monthlyStats = [];
for ($i = 5; $i >= 0; $i--) {
    $month     = date('Y-m', strtotime("-$i months"));
    $startDate = strtotime($month . '-01');
    $endDate   = strtotime($month . '-' . date('t', $startDate) . ' 23:59:59');

    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('update_date', $startDate, '>='));
    $criteria->add(new Criteria('update_date', $endDate,   '<='));
    $count = $pageHandler ? $pageHandler->getCount($criteria) : 0;

    $monthlyStats[] = [
        'month' => date('M Y', $startDate),
        'count' => $count,
    ];
}

$maxCount = !empty($monthlyStats) ? max(array_column($monthlyStats, 'count')) : 1;
if ($maxCount < 1) $maxCount = 1;

// Dashboard başlığı
echo '<div class="xpages-page-header">';
echo '<h2>📄 ' . _AM_XPAGES_DASHBOARD . '</h2>';
echo '<p>' . _AM_XPAGES_DASHBOARD_SUBTITLE . '</p>';
echo '</div>';

// İstatistik Kartları
echo '<div class="xpages-stat-grid">';

echo '<div class="xpages-stat-card xpages-stat-card--padded xpages-stat-card--purple">';
echo '<div class="xpages-stat-label">' . _AM_XPAGES_STAT_TOTAL_PAGES_LBL . '</div>';
echo '<div class="xpages-stat-value">' . $totalPages . '</div>';
echo '</div>';

echo '<div class="xpages-stat-card xpages-stat-card--padded xpages-stat-card--green">';
echo '<div class="xpages-stat-label">' . _AM_XPAGES_STAT_ACTIVE_PAGES_LBL . '</div>';
echo '<div class="xpages-stat-value">' . $activePages . '</div>';
echo '</div>';

echo '<div class="xpages-stat-card xpages-stat-card--padded xpages-stat-card--blue">';
echo '<div class="xpages-stat-label">' . _AM_XPAGES_STAT_FIELDS_LBL . '</div>';
echo '<div class="xpages-stat-value">' . $totalFields . '</div>';
echo '</div>';

echo '<div class="xpages-stat-card xpages-stat-card--padded xpages-stat-card--orange">';
echo '<div class="xpages-stat-label">' . _AM_XPAGES_STAT_GALLERY_LBL . '</div>';
echo '<div class="xpages-stat-value">' . $totalImages . '</div>';
echo '</div>';

echo '</div>';

// 2 Kolonlu Layout
echo '<div class="xpages-widget-grid--400">';

// Sol Kolon - Son Eklenen Sayfalar
echo '<div class="xpages-widget">';
echo '<div class="xpages-widget-header xpages-widget-header--purple">' . _AM_XPAGES_RECENT_PAGES_WIDGET . '</div>';
echo '<div class="xpages-widget-content">';
if (empty($recentPages)) {
    echo '<p class="xpages-empty-muted">' . _AM_XPAGES_NO_PAGES_YET . '</p>';
} else {
    echo '<ul class="xpages-list">';
    foreach ($recentPages as $page) {
        echo '<li>';
        echo '<a href="page_edit.php?page_id=' . $page->getVar('page_id') . '">' . htmlspecialchars((string)$page->getVar('title'), ENT_QUOTES) . '</a>';
        echo '<span class="xpages-badge">' . date('d.m.Y', $page->getVar('update_date')) . '</span>';
        echo '</li>';
    }
    echo '</ul>';
    echo '<div class="xpages-see-all"><a href="pages.php">' . _AM_XPAGES_SEE_ALL_PAGES . '</a></div>';
}
echo '</div></div>';

// Sağ Kolon - En Çok Okunanlar
echo '<div class="xpages-widget">';
echo '<div class="xpages-widget-header xpages-widget-header--orange">' . _AM_XPAGES_POPULAR_PAGES_WIDGET . '</div>';
echo '<div class="xpages-widget-content">';
if (empty($popularPages)) {
    echo '<p class="xpages-empty-muted">' . _AM_XPAGES_NO_STATS . '</p>';
} else {
    echo '<ul class="xpages-list">';
    foreach ($popularPages as $page) {
        echo '<li>';
        echo '<a href="page_edit.php?page_id=' . $page->getVar('page_id') . '">' . htmlspecialchars((string)$page->getVar('title'), ENT_QUOTES) . '</a>';
        echo '<span class="xpages-badge">👁️ ' . number_format($page->getVar('hits')) . '</span>';
        echo '</li>';
    }
    echo '</ul>';
}
echo '</div></div>';

echo '</div>';

// Aylık İstatistik Grafiği
echo '<div class="xpages-widget xpages-info-card--spaced">';
echo '<div class="xpages-widget-header xpages-widget-header--blue">' . _AM_XPAGES_MONTHLY_STATS . '</div>';
echo '<div class="xpages-widget-content--wide">';
foreach ($monthlyStats as $stat) {
    $percent = ($stat['count'] / $maxCount) * 100;
    $percent = $percent < 5 ? 5 : $percent;
    echo '<div class="xpages-chart-bar">';
    echo '<div class="xpages-chart-bar-label xpages-chart-bar-label--wide">' . $stat['month'] . '</div>';
    echo '<div class="xpages-chart-bar-track">';
    echo '<div class="xpages-chart-bar-fill xpages-chart-bar-fill--blue" style="width:' . (float)$percent . '%">' . $stat['count'] . '</div>';
    echo '</div>';
    echo '</div>';
}
echo '</div></div>';

// Hızlı İşlemler
echo '<div class="xpages-widget xpages-info-card--spaced">';
echo '<div class="xpages-widget-header xpages-widget-header--green">' . _AM_XPAGES_QUICK_ACTIONS_WIDGET . '</div>';
echo '<div class="xpages-quick-actions">';
echo '<a href="page_edit.php" class="xp-btn xp-btn--wide xp-btn--green">' . _AM_XPAGES_BTN_NEW_PAGE . '</a>';
echo '<a href="fields.php" class="xp-btn xp-btn--wide xp-btn--blue">' . _AM_XPAGES_BTN_NEW_FIELD . '</a>';
echo '<a href="gallery.php" class="xp-btn xp-btn--wide xp-btn--orange">' . _AM_XPAGES_BTN_GALLERY . '</a>';
echo '<a href="pages.php" class="xp-btn xp-btn--wide xp-btn--gray">' . _AM_XPAGES_BTN_LIST_PAGES . '</a>';
echo '</div></div>';

// Sistem Bilgileri
echo '<div class="xpages-widget">';
echo '<div class="xpages-widget-header xpages-widget-header--gray">' . _AM_XPAGES_SYS_INFO_WIDGET . '</div>';
echo '<div class="xpages-widget-content--wide">';
echo '<ul class="xpages-sysinfo-list">';
echo '<li><strong>' . _AM_XPAGES_SYSINFO_XOOPS . '</strong> ' . htmlspecialchars(XOOPS_VERSION, ENT_QUOTES) . '</li>';
echo '<li><strong>' . _AM_XPAGES_SYSINFO_MODULE . '</strong> 1.0</li>';
echo '<li><strong>' . _AM_XPAGES_SYSINFO_PHP . '</strong> ' . phpversion() . '</li>';
echo '<li><strong>' . _AM_XPAGES_SYSINFO_UPDATED . '</strong> ' . date('d.m.Y H:i:s') . '</li>';
echo '</ul>';
echo '</div></div>';

xoops_cp_footer();
