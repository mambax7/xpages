<?php
/**
 * xPages — Gallery Class
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

class XpagesGallery extends XoopsObject
{
    public function __construct()
    {
        $this->initVar('gallery_id',    XOBJ_DTYPE_INT,    null, false);
        $this->initVar('page_id',       XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('title',         XOBJ_DTYPE_TXTBOX, '',   false, 255);
        $this->initVar('description',   XOBJ_DTYPE_TXTAREA, '',  false);
        $this->initVar('image_path',    XOBJ_DTYPE_TXTBOX, '',   false, 255);
        $this->initVar('image_url',     XOBJ_DTYPE_TXTBOX, '',   false, 500);
        $this->initVar('image_order',   XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('image_status',  XOBJ_DTYPE_INT,    1,    false);
        $this->initVar('create_date',   XOBJ_DTYPE_INT,    time(), false);
        $this->initVar('uid',           XOBJ_DTYPE_INT,    0,    false);
    }
    
    public function getImageUrl() {
        if (!empty($this->getVar('image_url'))) {
            return $this->getVar('image_url');
        }
        if (!empty($this->getVar('image_path'))) {
            return XOOPS_UPLOAD_URL . '/xpages/gallery/' . $this->getVar('image_path');
        }
        return '';
    }
}

class XpagesGalleryHandler extends XoopsPersistableObjectHandler
{
    public function __construct($db)
    {
        parent::__construct($db, 'xpages_gallery', 'XpagesGallery', 'gallery_id', 'title');
    }
    
    public function getGalleryForPage($pageId, $onlyActive = true) {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('page_id', $pageId));
        if ($onlyActive) {
            $criteria->add(new Criteria('image_status', 1));
        }
        $criteria->setSort('image_order');
        $criteria->setOrder('ASC');
        
        return $this->getObjects($criteria);
    }
    
    public function getCountForPage($pageId) {
        $criteria = new Criteria('page_id', $pageId);
        return $this->getCount($criteria);
    }
    
    public function deleteGalleryForPage($pageId) {
        $gallery = $this->getGalleryForPage($pageId, false);
        foreach ($gallery as $item) {
            // Dosyayı sil
            $filePath = XOOPS_UPLOAD_PATH . '/xpages/gallery/' . $item->getVar('image_path');
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            $this->delete($item);
        }
        return true;
    }
}