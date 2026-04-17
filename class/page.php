<?php
/**
 * xPages — Page Class
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
     * Sayfa URL'ini döndür
     */
    public function getPageUrl() {
        $alias = $this->getVar('alias');
        if (!empty($alias)) {
            return XOOPS_URL . '/modules/xpages/page.php?alias=' . urlencode($alias);
        }
        return XOOPS_URL . '/modules/xpages/page.php?page_id=' . $this->getVar('page_id');
    }
    
    /**
     * Robots meta etiketini döndür
     */
    public function getRobots() {
        $noindex  = $this->getVar('noindex');
        $nofollow = $this->getVar('nofollow');
        
        if ($noindex && $nofollow) return 'noindex, nofollow';
        if ($noindex) return 'noindex, follow';
        if ($nofollow) return 'index, nofollow';
        return 'index, follow';
    }
}

class XpagesPageHandler extends XoopsPersistableObjectHandler
{
    public function __construct($db)
    {
        parent::__construct($db, 'xpages_pages', 'XpagesPage', 'page_id', 'title');
    }
    
    /**
     * Toplam kayıt sayısını döndür
     */
    public function getCount($criteria = null) {
        return parent::getCount($criteria);
    }
    
    /**
     * Alias ile sayfa bul
     */
    public function getByAlias($alias) {
        $criteria = new Criteria('alias', $alias);
        $objects = $this->getObjects($criteria);
        return !empty($objects) ? $objects[0] : null;
    }

    /**
     * Menüde gösterilecek sayfaları getir
     */
    public function getMenuPages($parentId = 0, $onlyActive = true) {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('parent_id', (int)$parentId));
        $criteria->add(new Criteria('show_in_menu', 1));

        if ($onlyActive) {
            $criteria->add(new Criteria('page_status', 1));
        }

        $criteria->setSort('menu_order');
        $criteria->setOrder('ASC');

        return $this->getObjects($criteria);
    }
    
    /**
     * Benzersiz alias oluştur
     */
    public function generateAlias($title, $excludeId = 0) {
        $alias = $this->cleanAlias($title);
        $original = $alias;
        $counter = 1;
        
        while ($this->aliasExists($alias, $excludeId)) {
            $alias = $original . '-' . $counter;
            $counter++;
        }
        
        return $alias;
    }
    
    /**
     * Alias temizleme
     */
    private function cleanAlias($str) {
        $str = mb_strtolower($str, 'UTF-8');
        $str = preg_replace('/[^a-z0-9-]/', '-', $str);
        $str = preg_replace('/-+/', '-', $str);
        $str = trim($str, '-');
        return $str ?: 'page';
    }
    
    /**
     * Alias var mı kontrol et (DÜZELTİLDİ - CriteriaCompo kullanıldı)
     */
    private function aliasExists($alias, $excludeId = 0) {
        // CriteriaCompo kullan (add metodu olan)
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('alias', $alias));
        if ($excludeId > 0) {
            $criteria->add(new Criteria('page_id', $excludeId, '!='));
        }
        return $this->getCount($criteria) > 0;
    }
    
    /**
     * Hit sayısını artır
     */
    public function incrementHits($pageId) {
        $sql = "UPDATE {$this->table} SET hits = hits + 1 WHERE page_id = " . (int)$pageId;
        return $this->db->queryF($sql);
    }
}
