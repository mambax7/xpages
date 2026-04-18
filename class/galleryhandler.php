<?php

declare(strict_types=1);

/**
 * xPages — Gallery handler.
 *
 * Companion to class/gallery.php. See class/pagehandler.php for the
 * file-layout rationale.
 *
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

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
