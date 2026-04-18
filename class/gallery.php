<?php

declare(strict_types=1);

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
    
    public function getImageUrl(): string
    {
        $imageUrl = (string)$this->getVar('image_url', 'n');
        if ($imageUrl !== '') {
            return xpages_normalize_url($imageUrl);
        }
        $imagePath = xpages_safe_filename($this->getVar('image_path', 'n'));
        if ($imagePath !== '') {
            return XOOPS_UPLOAD_URL . '/xpages/gallery/' . rawurlencode($imagePath);
        }
        return '';
    }
}

class XpagesGalleryHandler extends XoopsPersistableObjectHandler
{
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'xpages_gallery', 'XpagesGallery', 'gallery_id', 'title');
    }

    /**
     * @return XpagesGallery[]
     */
    public function getGalleryForPage(int $pageId, bool $onlyActive = true): array
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('page_id', $pageId));
        if ($onlyActive) {
            $criteria->add(new Criteria('image_status', 1));
        }
        $criteria->setSort('image_order');
        $criteria->setOrder('ASC');

        return $this->getObjects($criteria) ?: [];
    }

    public function getCountForPage(int $pageId): int
    {
        $criteria = new Criteria('page_id', $pageId);
        return (int)$this->getCount($criteria);
    }

    public function deleteGalleryForPage(int $pageId): bool
    {
        $gallery = $this->getGalleryForPage($pageId, false);
        foreach ($gallery as $item) {
            $safeFile = xpages_safe_filename((string)$item->getVar('image_path', 'n'));
            if ($safeFile !== '') {
                $filePath = XOOPS_UPLOAD_PATH . '/xpages/gallery/' . $safeFile;
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $this->delete($item);
        }
        return true;
    }
}
