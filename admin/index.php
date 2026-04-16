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
$recentPages = $pageHandler ? $pageHandler->getObjects($criteria) : array();

$criteria = new CriteriaCompo();
$criteria->setSort('hits');
$criteria->setOrder('DESC');
$criteria->setLimit(5);
$popularPages = $pageHandler ? $pageHandler->getObjects($criteria) : array();

$monthlyStats = array();
for ($i = 5; $i >= 0; $i--) {
    $month     = date('Y-m', strtotime("-$i months"));
    $startDate = strtotime($month . '-01');
    $endDate   = strtotime($month . '-' . date('t', $startDate) . ' 23:59:59');

    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('update_date', $startDate, '>='));
    $criteria->add(new Criteria('update_date', $endDate,   '<='));
    $count = $pageHandler ? $pageHandler->getCount($criteria) : 0;

    $monthlyStats[] = array(
        'month' => date('M Y', $startDate),
        'count' => $count
    );
}

$maxCount = !empty($monthlyStats) ? max(array_column($monthlyStats, 'count')) : 1;
if ($maxCount < 1) $maxCount = 1;

// Dashboard başlığı
echo '<div class="xpages-header" style="margin:16px 0 24px">';
echo '<h2 style="margin:0 0 4px;font-size:22px">📄 ' . _AM_XPAGES_DASHBOARD . '</h2>';
echo '<p style="margin:0;color:#6b7280">' . _AM_XPAGES_DASHBOARD_SUBTITLE . '</p>';
echo '</div>';

// İstatistik Kartları
echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:28px">';

echo '<div style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;padding:22px;border-radius:10px;text-align:center">';
echo '<div style="font-size:13px;opacity:.85">' . _AM_XPAGES_STAT_TOTAL_PAGES_LBL . '</div>';
echo '<div style="font-size:38px;font-weight:700">' . $totalPages . '</div>';
echo '</div>';

echo '<div style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;padding:22px;border-radius:10px;text-align:center">';
echo '<div style="font-size:13px;opacity:.85">' . _AM_XPAGES_STAT_ACTIVE_PAGES_LBL . '</div>';
echo '<div style="font-size:38px;font-weight:700">' . $activePages . '</div>';
echo '</div>';

echo '<div style="background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;padding:22px;border-radius:10px;text-align:center">';
echo '<div style="font-size:13px;opacity:.85">' . _AM_XPAGES_STAT_FIELDS_LBL . '</div>';
echo '<div style="font-size:38px;font-weight:700">' . $totalFields . '</div>';
echo '</div>';

echo '<div style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;padding:22px;border-radius:10px;text-align:center">';
echo '<div style="font-size:13px;opacity:.85">' . _AM_XPAGES_STAT_GALLERY_LBL . '</div>';
echo '<div style="font-size:38px;font-weight:700">' . $totalImages . '</div>';
echo '</div>';

echo '</div>';

// 2 Kolonlu Layout
echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(400px,1fr));gap:20px;margin-bottom:28px">';

// Sol Kolon - Son Eklenen Sayfalar
echo '<div style="background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.07);overflow:hidden">';
echo '<div style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;padding:12px 18px;font-weight:600">' . _AM_XPAGES_RECENT_PAGES_WIDGET . '</div>';
echo '<div style="padding:15px">';
if (empty($recentPages)) {
    echo '<p style="color:#6c757d;text-align:center">' . _AM_XPAGES_NO_PAGES_YET . '</p>';
} else {
    echo '<ul style="list-style:none;margin:0;padding:0">';
    foreach ($recentPages as $page) {
        echo '<li style="padding:10px 0;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center">';
        echo '<a href="page_edit.php?page_id=' . $page->getVar('page_id') . '" style="color:#333;text-decoration:none">' . htmlspecialchars((string)$page->getVar('title'), ENT_QUOTES) . '</a>';
        echo '<span style="background:#e9ecef;padding:2px 8px;border-radius:20px;font-size:11px">' . date('d.m.Y', $page->getVar('update_date')) . '</span>';
        echo '</li>';
    }
    echo '</ul>';
    echo '<div style="margin-top:10px;text-align:center"><a href="pages.php" style="color:#007bff;font-size:12px">' . _AM_XPAGES_SEE_ALL_PAGES . '</a></div>';
}
echo '</div></div>';

// Sağ Kolon - En Çok Okunanlar
echo '<div style="background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.07);overflow:hidden">';
echo '<div style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;padding:12px 18px;font-weight:600">' . _AM_XPAGES_POPULAR_PAGES_WIDGET . '</div>';
echo '<div style="padding:15px">';
if (empty($popularPages)) {
    echo '<p style="color:#6c757d;text-align:center">' . _AM_XPAGES_NO_STATS . '</p>';
} else {
    echo '<ul style="list-style:none;margin:0;padding:0">';
    foreach ($popularPages as $page) {
        echo '<li style="padding:10px 0;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center">';
        echo '<a href="page_edit.php?page_id=' . $page->getVar('page_id') . '" style="color:#333;text-decoration:none">' . htmlspecialchars((string)$page->getVar('title'), ENT_QUOTES) . '</a>';
        echo '<span style="background:#e9ecef;padding:2px 8px;border-radius:20px;font-size:11px">👁️ ' . number_format($page->getVar('hits')) . '</span>';
        echo '</li>';
    }
    echo '</ul>';
}
echo '</div></div>';

echo '</div>';

// Aylık İstatistik Grafiği
echo '<div style="background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.07);margin-bottom:28px;overflow:hidden">';
echo '<div style="background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;padding:12px 18px;font-weight:600">' . _AM_XPAGES_MONTHLY_STATS . '</div>';
echo '<div style="padding:20px">';
foreach ($monthlyStats as $stat) {
    $percent = ($stat['count'] / $maxCount) * 100;
    $percent = $percent < 5 ? 5 : $percent;
    echo '<div style="display:flex;align-items:center;margin-bottom:10px;font-size:13px">';
    echo '<div style="width:70px;color:#6c757d">' . $stat['month'] . '</div>';
    echo '<div style="flex:1;height:28px;background:#e9ecef;border-radius:5px;overflow:hidden;margin-left:10px">';
    echo '<div style="width:' . $percent . '%;height:100%;background:linear-gradient(90deg,#3b82f6,#2563eb);display:flex;align-items:center;justify-content:flex-end;padding-right:8px;color:#fff;font-size:11px">' . $stat['count'] . '</div>';
    echo '</div></div>';
}
echo '</div></div>';

// Hızlı İşlemler
echo '<div style="background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.07);margin-bottom:28px;overflow:hidden">';
echo '<div style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;padding:12px 18px;font-weight:600">' . _AM_XPAGES_QUICK_ACTIONS_WIDGET . '</div>';
echo '<div style="padding:20px;display:flex;gap:15px;flex-wrap:wrap">';
echo '<a href="page_edit.php" style="background:#10b981;color:#fff;padding:10px 20px;text-decoration:none;border-radius:6px">' . _AM_XPAGES_BTN_NEW_PAGE . '</a>';
echo '<a href="fields.php" style="background:#3b82f6;color:#fff;padding:10px 20px;text-decoration:none;border-radius:6px">' . _AM_XPAGES_BTN_NEW_FIELD . '</a>';
echo '<a href="gallery.php" style="background:#f59e0b;color:#fff;padding:10px 20px;text-decoration:none;border-radius:6px">' . _AM_XPAGES_BTN_GALLERY . '</a>';
echo '<a href="pages.php" style="background:#6c757d;color:#fff;padding:10px 20px;text-decoration:none;border-radius:6px">' . _AM_XPAGES_BTN_LIST_PAGES . '</a>';
echo '</div></div>';

// Sistem Bilgileri
echo '<div style="background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.07);overflow:hidden">';
echo '<div style="background:linear-gradient(135deg,#6c757d,#495057);color:#fff;padding:12px 18px;font-weight:600">' . _AM_XPAGES_SYS_INFO_WIDGET . '</div>';
echo '<div style="padding:20px">';
echo '<ul style="list-style:none;margin:0;padding:0">';
echo '<li style="padding:5px 0"><strong>' . _AM_XPAGES_SYSINFO_XOOPS . '</strong> ' . htmlspecialchars(XOOPS_VERSION, ENT_QUOTES) . '</li>';
echo '<li style="padding:5px 0"><strong>' . _AM_XPAGES_SYSINFO_MODULE . '</strong> 1.0</li>';
echo '<li style="padding:5px 0"><strong>' . _AM_XPAGES_SYSINFO_PHP . '</strong> ' . phpversion() . '</li>';
echo '<li style="padding:5px 0"><strong>' . _AM_XPAGES_SYSINFO_UPDATED . '</strong> ' . date('d.m.Y H:i:s') . '</li>';
echo '</ul>';
echo '</div></div>';

xoops_cp_footer();
?>
