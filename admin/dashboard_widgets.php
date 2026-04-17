<?php
/**
 * xPages — Dashboard Widget'ları
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

function xpages_dashboard_widgets() {
    $pageHandler  = xpages_get_handler('page');
    $fieldHandler = xpages_get_handler('field');
    $galleryHandler = xpages_get_handler('gallery');

    if (!$pageHandler || !$fieldHandler) {
        return;
    }

    // Son 5 sayfa
    $criteria = new CriteriaCompo();
    $criteria->setSort('update_date');
    $criteria->setOrder('DESC');
    $criteria->setLimit(5);
    $recentPages = $pageHandler->getObjects($criteria);

    // En çok okunan 5 sayfa
    $criteria = new CriteriaCompo();
    $criteria->setSort('hits');
    $criteria->setOrder('DESC');
    $criteria->setLimit(5);
    $popularPages = $pageHandler->getObjects($criteria);

    // Toplam istatistikler
    $totalPages  = $pageHandler->getCount();
    $totalFields = $fieldHandler->getCount();
    $totalImages = $galleryHandler ? $galleryHandler->getCount() : 0;
    $activePages = $pageHandler->getCount(new Criteria('page_status', 1));

    // Aylık ziyaret istatistikleri (son 12 ay)
    $monthlyStats = array();
    for ($i = 11; $i >= 0; $i--) {
        $month     = date('Y-m', strtotime("-$i months"));
        $startDate = strtotime($month . '-01');
        $endDate   = strtotime($month . '-' . date('t', $startDate) . ' 23:59:59');

        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('update_date', $startDate, '>='));
        $criteria->add(new Criteria('update_date', $endDate,   '<='));
        $count = $pageHandler->getCount($criteria);

        $monthlyStats[] = array(
            'month' => date('M Y', $startDate),
            'count' => $count
        );
    }
    ?>

    <style>
    .xpages-widget-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .xpages-widget {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,.08);
        overflow: hidden;
    }
    .xpages-widget-header {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff;
        padding: 12px 18px;
        font-weight: 600;
        font-size: 14px;
    }
    .xpages-widget-content {
        padding: 15px;
    }
    .xpages-stat-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    .xpages-stat-card {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
        padding: 18px;
        border-radius: 10px;
        text-align: center;
    }
    .xpages-stat-card h3 {
        margin: 0 0 5px;
        font-size: 28px;
    }
    .xpages-stat-card p {
        margin: 0;
        font-size: 12px;
        opacity: 0.9;
    }
    .xpages-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .xpages-list li {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .xpages-list li:last-child {
        border-bottom: none;
    }
    .xpages-list a {
        color: #333;
        text-decoration: none;
    }
    .xpages-list a:hover {
        color: #007bff;
    }
    .xpages-badge {
        background: #e9ecef;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 11px;
        color: #6c757d;
    }
    .xpages-chart {
        margin-top: 15px;
    }
    .xpages-chart-bar {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        font-size: 12px;
    }
    .xpages-chart-bar-label {
        width: 60px;
        color: #6c757d;
    }
    .xpages-chart-bar-fill {
        flex: 1;
        height: 25px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        border-radius: 4px;
        margin-left: 10px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 8px;
        color: #fff;
        font-size: 11px;
        min-width: 30px;
    }
    </style>

    <div class="xpages-widget-grid">

        <!-- İstatistik Kartları -->
        <div class="xpages-widget">
            <div class="xpages-widget-header"><?= _AM_XPAGES_WIDGET_STATS ?></div>
            <div class="xpages-widget-content">
                <div class="xpages-stat-cards">
                    <div class="xpages-stat-card" style="background:linear-gradient(135deg,#667eea,#764ba2)">
                        <h3><?= $totalPages ?></h3>
                        <p><?= _AM_XPAGES_STAT_TOTAL_PAGES ?></p>
                    </div>
                    <div class="xpages-stat-card" style="background:linear-gradient(135deg,#10b981,#059669)">
                        <h3><?= $activePages ?></h3>
                        <p><?= _AM_XPAGES_STAT_ACTIVE_PAGES ?></p>
                    </div>
                    <div class="xpages-stat-card" style="background:linear-gradient(135deg,#3b82f6,#2563eb)">
                        <h3><?= $totalFields ?></h3>
                        <p><?= _AM_XPAGES_STAT_FIELDS_COUNT ?></p>
                    </div>
                    <div class="xpages-stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                        <h3><?= $totalImages ?></h3>
                        <p><?= _AM_XPAGES_STAT_GALLERY_COUNT ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Son Eklenen Sayfalar -->
        <div class="xpages-widget">
            <div class="xpages-widget-header"><?= _AM_XPAGES_WIDGET_RECENT ?></div>
            <div class="xpages-widget-content">
                <?php if (empty($recentPages)): ?>
                    <p style="color:#6c757d;text-align:center"><?= _AM_XPAGES_NO_PAGES_YET ?></p>
                <?php else: ?>
                    <ul class="xpages-list">
                    <?php foreach ($recentPages as $page): ?>
                        <li>
                            <a href="page_edit.php?page_id=<?= $page->getVar('page_id') ?>">
                                <?= htmlspecialchars((string)$page->getVar('title'), ENT_QUOTES) ?>
                            </a>
                            <span class="xpages-badge">
                                <?= date('d.m.Y', $page->getVar('update_date')) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                    <div style="margin-top:10px;text-align:center">
                        <a href="pages.php" style="color:#007bff;font-size:12px"><?= _AM_XPAGES_SEE_ALL_PAGES ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- En Çok Okunan Sayfalar -->
        <div class="xpages-widget">
            <div class="xpages-widget-header"><?= _AM_XPAGES_WIDGET_POPULAR ?></div>
            <div class="xpages-widget-content">
                <?php if (empty($popularPages)): ?>
                    <p style="color:#6c757d;text-align:center"><?= _AM_XPAGES_NO_STATS ?></p>
                <?php else: ?>
                    <ul class="xpages-list">
                    <?php foreach ($popularPages as $page): ?>
                        <li>
                            <a href="page_edit.php?page_id=<?= $page->getVar('page_id') ?>">
                                <?= htmlspecialchars((string)$page->getVar('title'), ENT_QUOTES) ?>
                            </a>
                            <span class="xpages-badge">
                                👁️ <?= number_format($page->getVar('hits')) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Aylık Sayfa İstatistikleri Grafiği -->
        <div class="xpages-widget">
            <div class="xpages-widget-header"><?= _AM_XPAGES_WIDGET_MONTHLY ?></div>
            <div class="xpages-widget-content">
                <div class="xpages-chart">
                    <?php
                    $maxCount = max(array_column($monthlyStats, 'count'));
                    $maxCount = $maxCount > 0 ? $maxCount : 1;
                    foreach ($monthlyStats as $stat):
                        $percent = ($stat['count'] / $maxCount) * 100;
                    ?>
                    <div class="xpages-chart-bar">
                        <div class="xpages-chart-bar-label"><?= $stat['month'] ?></div>
                        <div class="xpages-chart-bar-fill" style="width: <?= $percent ?>%; min-width: 30px">
                            <?= $stat['count'] ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Hızlı İşlemler -->
        <div class="xpages-widget">
            <div class="xpages-widget-header"><?= _AM_XPAGES_WIDGET_QUICK ?></div>
            <div class="xpages-widget-content">
                <div style="display:flex;flex-direction:column;gap:10px">
                    <a href="page_edit.php" style="background:#10b981;color:#fff;padding:10px;text-align:center;text-decoration:none;border-radius:6px"><?= _AM_XPAGES_BTN_NEW_PAGE ?></a>
                    <a href="fields.php" style="background:#3b82f6;color:#fff;padding:10px;text-align:center;text-decoration:none;border-radius:6px"><?= _AM_XPAGES_BTN_NEW_FIELD ?></a>
                    <a href="pages.php" style="background:#6c757d;color:#fff;padding:10px;text-align:center;text-decoration:none;border-radius:6px"><?= _AM_XPAGES_BTN_LIST_PAGES ?></a>
                </div>
            </div>
        </div>

        <!-- Kısa Bilgiler -->
        <div class="xpages-widget">
            <div class="xpages-widget-header"><?= _AM_XPAGES_WIDGET_SYSINFO ?></div>
            <div class="xpages-widget-content">
                <ul class="xpages-list" style="margin:0">
                    <li><strong><?= _AM_XPAGES_SYSINFO_XOOPS ?></strong> <?= htmlspecialchars(XOOPS_VERSION, ENT_QUOTES) ?></li>
                    <li><strong><?= _AM_XPAGES_SYSINFO_MODULE ?></strong> 1.0</li>
                    <li><strong><?= _AM_XPAGES_SYSINFO_PHP ?></strong> <?= phpversion() ?></li>
                    <li><strong><?= _AM_XPAGES_SYSINFO_UPDATED ?></strong> <?= date('d.m.Y H:i') ?></li>
                </ul>
            </div>
        </div>

    </div>

    <?php
}
