<?php

declare(strict_types=1);

/**
 * xPages — Page value object.
 *
 * The handler lives in pagehandler.php, which this file loads at the
 * bottom. XOOPS's module-helper lookup (Xmf\Module\Helper::initHandler)
 * only loads `class/{name}.php`, so the require_once here is what keeps
 * the handler class available after the split.
 *
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

class XpagesPage extends XoopsObject
{
    public function __construct()
    {
        $this->initVar('page_id',       XOBJ_DTYPE_INT,    null, false);
        $this->initVar('title',         XOBJ_DTYPE_TXTBOX, '',   false, 255);
        $this->initVar('alias',         XOBJ_DTYPE_TXTBOX, '',   false, 255);
        $this->initVar('body',          XOBJ_DTYPE_TXTAREA, '',  false);
        $this->initVar('short_desc',    XOBJ_DTYPE_TXTAREA, '',  false);
        $this->initVar('page_status',   XOBJ_DTYPE_INT,    1,    false);
        $this->initVar('menu_order',    XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('show_in_menu',  XOBJ_DTYPE_INT,    1,    false);
        $this->initVar('show_in_nav',   XOBJ_DTYPE_INT,    1,    false);
        $this->initVar('parent_id',     XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('uid',           XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('create_date',   XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('update_date',   XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('meta_title',    XOBJ_DTYPE_TXTBOX, '',   false, 255);
        $this->initVar('meta_keywords', XOBJ_DTYPE_TXTAREA, '',  false);
        $this->initVar('meta_desc',     XOBJ_DTYPE_TXTAREA, '',  false);
        $this->initVar('noindex',       XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('nofollow',      XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('redirect_url',  XOBJ_DTYPE_TXTBOX, '',   false, 500);
        $this->initVar('header_code',   XOBJ_DTYPE_TXTAREA, '',  false);
        $this->initVar('footer_code',   XOBJ_DTYPE_TXTAREA, '',  false);
        $this->initVar('hits',          XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('comments',      XOBJ_DTYPE_INT,    0,    false);
    }

    /**
     * Sayfa URL'ini döndür.
     *
     * Routes through the module Helper so the dirname isn't hardcoded —
     * a site admin who renames the module directory gets correct URLs
     * without any source changes.
     */
    public function getPageUrl(): string
    {
        $helper = \XoopsModules\Xpages\Helper::getInstance();
        $alias  = (string)$this->getVar('alias');

        if ($alias !== '') {
            return $helper->url('page.php?alias=' . urlencode($alias));
        }
        return $helper->url('page.php?page_id=' . (int)$this->getVar('page_id'));
    }

    /**
     * Robots meta etiketini döndür
     */
    public function getRobots(): string
    {
        $noindex  = (bool)$this->getVar('noindex');
        $nofollow = (bool)$this->getVar('nofollow');

        return match (true) {
            $noindex && $nofollow => 'noindex, nofollow',
            $noindex              => 'noindex, follow',
            $nofollow             => 'index, nofollow',
            default               => 'index, follow',
        };
    }
}

require_once __DIR__ . '/pagehandler.php';
