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

    <?php // (inline <style> block extracted to assets/css/admin.css) ?>

    <div class="xpages-widget-grid">

        <!-- İstatistik Kartları -->
        <div class="xpages-widget">
            <div class="xpages-widget-header"><?= _AM_XPAGES_WIDGET_STATS ?></div>
            <div class="xpages-widget-content">
                <div class="xpages-stat-cards">
                    <div class="xpages-stat-card xpages-stat-card--purple">
                        <h3><?= $totalPages ?></h3>
                        <p><?= _AM_XPAGES_STAT_TOTAL_PAGES ?></p>
                    </div>
                    <div class="xpages-stat-card xpages-stat-card--green">
                        <h3><?= $activePages ?></h3>
                        <p><?= _AM_XPAGES_STAT_ACTIVE_PAGES ?></p>
                    </div>
                    <div class="xpages-stat-card xpages-stat-card--blue">
                        <h3><?= $totalFields ?></h3>
                        <p><?= _AM_XPAGES_STAT_FIELDS_COUNT ?></p>
                    </div>
                    <div class="xpages-stat-card xpages-stat-card--orange">
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
                    <p class="xpages-empty-muted"><?= _AM_XPAGES_NO_PAGES_YET ?></p>
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
                    <div class="xpages-see-all">
                        <a href="pages.php"><?= _AM_XPAGES_SEE_ALL_PAGES ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- En Çok Okunan Sayfalar -->
        <div class="xpages-widget">
            <div class="xpages-widget-header"><?= _AM_XPAGES_WIDGET_POPULAR ?></div>
            <div class="xpages-widget-content">
                <?php if (empty($popularPages)): ?>
                    <p class="xpages-empty-muted"><?= _AM_XPAGES_NO_STATS ?></p>
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
                        <div class="xpages-chart-bar-fill" style="width:<?= (float)$percent ?>%">
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
                <div class="xpages-quick-column">
                    <a href="page_edit.php" class="xp-btn xp-btn--block xp-btn--green"><?= _AM_XPAGES_BTN_NEW_PAGE ?></a>
                    <a href="fields.php" class="xp-btn xp-btn--block xp-btn--blue"><?= _AM_XPAGES_BTN_NEW_FIELD ?></a>
                    <a href="pages.php" class="xp-btn xp-btn--block xp-btn--cancel"><?= _AM_XPAGES_BTN_LIST_PAGES ?></a>
                </div>
            </div>
        </div>

        <!-- Kısa Bilgiler -->
        <div class="xpages-widget">
            <div class="xpages-widget-header"><?= _AM_XPAGES_WIDGET_SYSINFO ?></div>
            <div class="xpages-widget-content">
                <ul class="xpages-sysinfo-list">
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
