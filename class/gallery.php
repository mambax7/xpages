<?php

declare(strict_types=1);

/**
 * xPages — Gallery value object.
 *
 * The handler lives in galleryhandler.php, which this file loads at
 * the bottom. See class/page.php for the rationale.
 *
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

require_once __DIR__ . '/galleryhandler.php';
