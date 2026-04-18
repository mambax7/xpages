<?php

declare(strict_types=1);

/**
 * xPages — Admin Galeri Yönetimi
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

use Xmf\Request;

include_once '../../../include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/modules/xpages/include/functions.php';
xpages_admin_boot();

xoops_cp_header();
xpages_admin_register_css();

if (class_exists('Xmf\\Module\\Admin')) {
    \Xmf\Module\Admin::getInstance()->displayNavigation('gallery.php');
}

$galleryHandler = xpages_get_handler('gallery');
$pageHandler    = xpages_get_handler('page');

if (!$galleryHandler || !$pageHandler) {
    echo '<div class="xp-alert xp-alert--error">' . _AM_XPAGES_HANDLER_UNAVAILABLE . '</div>';
    xoops_cp_footer();
    exit;
}

$pageId    = Request::getInt('page_id',    0,      'REQUEST');
$op        = Request::getCmd('op',         'list', 'REQUEST');
$galleryId = Request::getInt('gallery_id', 0,      'REQUEST');

$pageObj          = $pageId ? $pageHandler->get($pageId) : null;
$pageTitleDisplay = $pageObj ? (string)$pageObj->getVar('title') : _AM_XPAGES_GALLERY_ALL_PAGES;

// Header (toolbar + back link) — always shown above the op branches.
xpages_admin_render('xpages_admin_gallery_header.tpl', [
    'gallery_title'       => _AM_XPAGES_GALLERY_TITLE,
    'page_title_display'  => $pageTitleDisplay,
    'page_id'             => ($pageObj ? $pageId : 0),
    'label_back_to_page'  => _AM_XPAGES_BACK_TO_PAGE,
]);

// ── Sil ───────────────────────────────────────────────────────────────────────
if ($op === 'delete' && $galleryId) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || 1 !== Request::getInt('confirm', 0, 'POST')) {
        $gobj = $galleryHandler->get($galleryId);
        if ($gobj) {
            xpages_admin_render('xpages_admin_gallery_delete_confirm.tpl', [
                'confirm_message' => sprintf(
                    _AM_XPAGES_GALLERY_DELETE_CONFIRM,
                    htmlspecialchars((string)$gobj->getVar('title'), ENT_QUOTES)
                ),
                'gallery_id' => $galleryId,
                'page_id'    => $pageId,
                'label_yes'  => _AM_XPAGES_YES,
                'label_no'   => _AM_XPAGES_NO,
            ]);
        }
        xoops_cp_footer();
        exit;
    }
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header('gallery.php?page_id=' . $pageId, 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        exit;
    }
    $gobj = $galleryHandler->get($galleryId);
    if ($gobj) {
        $safeFile = xpages_safe_filename($gobj->getVar('image_path', 'n'));
        $filePath = $safeFile !== '' ? XOOPS_UPLOAD_PATH . '/xpages/gallery/' . $safeFile : '';
        if ($filePath !== '' && file_exists($filePath)) {
            @unlink($filePath);
        }
        $galleryHandler->delete($gobj);
        redirect_header('gallery.php?page_id=' . $pageId, 2, _AM_XPAGES_GALLERY_DELETED);
        exit;
    }
    redirect_header('gallery.php?page_id=' . $pageId, 2, _AM_XPAGES_PAGE_NOT_FOUND);
    exit;
}

// ── Kaydet ────────────────────────────────────────────────────────────────────
if ($op === 'save') {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header('gallery.php?page_id=' . $pageId, 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        exit;
    }

    if (!is_object($GLOBALS['xoopsUser'])) {
        redirect_header('gallery.php?page_id=' . $pageId, 3, _AM_XPAGES_SAVE_ERROR);
        exit;
    }

    $gallery = $galleryId ? $galleryHandler->get($galleryId) : $galleryHandler->create();

    $gallery->setVar('page_id',       $pageId);
    $gallery->setVar('title',         Request::getString('title',       '', 'POST'));
    $gallery->setVar('description',   Request::getString('description', '', 'POST'));
    $gallery->setVar('image_order',   Request::getInt('image_order',    0,  'POST'));
    $gallery->setVar('image_status',  Request::getInt('image_status',   1,  'POST'));
    $gallery->setVar('uid',           (int)$GLOBALS['xoopsUser']->getVar('uid'));

    // Harici URL kontrolü
    $imageUrl = xpages_normalize_url(Request::getString('image_url', '', 'POST'));
    if (!empty($imageUrl)) {
        $gallery->setVar('image_url', $imageUrl);
        $gallery->setVar('image_path', '');
    }

    // Dosya yükleme
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = XOOPS_UPLOAD_PATH . '/xpages/gallery/';
        xpages_ensure_upload_dir($uploadDir);

        $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ext, $allowedExts, true) && xpages_upload_is_allowed($_FILES['image_file']['tmp_name'], $ext)) {
            if ($galleryId && !empty($gallery->getVar('image_path'))) {
                $safeOldFile = xpages_safe_filename($gallery->getVar('image_path', 'n'));
                $oldFile = $safeOldFile !== '' ? $uploadDir . $safeOldFile : '';
                if ($oldFile !== '' && file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }

            $newFileName = 'gallery_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $newFileName)) {
                $gallery->setVar('image_path', $newFileName);
                $gallery->setVar('image_url', '');
            }
        }
    }

    if (!$galleryId) {
        $gallery->setVar('create_date', time());
    }

    if ($galleryHandler->insert($gallery)) {
        redirect_header('gallery.php?page_id=' . $pageId, 2, _AM_XPAGES_GALLERY_SAVED);
        exit;
    }
    echo '<div class="xp-alert xp-alert--error">' . _AM_XPAGES_GALLERY_SAVE_ERROR . '</div>';
}

// ── Ekle / Düzenle Formu ──────────────────────────────────────────────────────
if (in_array($op, ['add', 'edit'], true)) {
    $gallery = ($op === 'edit' && $galleryId) ? $galleryHandler->get($galleryId) : $galleryHandler->create();

    // Pre-compute "current image" preview URL so the template stays flat.
    $currentImage = null;
    if ($op === 'edit' && !empty($gallery->getVar('image_path'))) {
        $safeGalleryFile = xpages_safe_filename($gallery->getVar('image_path', 'n'));
        if ($safeGalleryFile !== '') {
            $currentImage = [
                'url' => XOOPS_UPLOAD_URL . '/xpages/gallery/' . rawurlencode($safeGalleryFile),
            ];
        }
    }

    xpages_admin_render('xpages_admin_gallery_form.tpl', [
        'form_title'       => $op === 'edit' ? _AM_XPAGES_GALLERY_EDIT : _AM_XPAGES_GALLERY_NEW,
        'page_id'          => $pageId,
        'gallery_id'       => $galleryId,
        'gallery'          => [
            'title'        => (string)$gallery->getVar('title',        'n'),
            'description'  => (string)$gallery->getVar('description',  'n'),
            'image_url'    => (string)$gallery->getVar('image_url',    'n'),
            'image_order'  => (int)   $gallery->getVar('image_order'),
            'image_status' => (bool)  $gallery->getVar('image_status'),
        ],
        'current_image'    => $currentImage,
        'label_title'      => _AM_XPAGES_GALLERY_IMG_TITLE,
        'label_desc'       => _AM_XPAGES_GALLERY_IMG_DESC,
        'label_file'       => _AM_XPAGES_GALLERY_IMG_FILE,
        'help_file'        => _AM_XPAGES_GALLERY_IMG_FILE_HELP,
        'label_current_img'=> _AM_XPAGES_GALLERY_CURRENT_IMG,
        'label_url'        => _AM_XPAGES_GALLERY_IMG_URL,
        'help_url'         => _AM_XPAGES_GALLERY_IMG_URL_HELP,
        'label_order'      => _AM_XPAGES_GALLERY_IMG_ORDER,
        'label_status'     => _AM_XPAGES_GALLERY_IMG_STATUS,
        'label_active'     => _AM_XPAGES_ACTIVE,
        'label_inactive'   => _AM_XPAGES_INACTIVE,
        'label_save'       => _AM_XPAGES_SAVE,
        'label_cancel'     => _AM_XPAGES_CANCEL,
    ]);

    xoops_cp_footer();
    exit;
}

// ── Galeri Listesi ────────────────────────────────────────────────────────────
$galleryItems = $galleryHandler->getGalleryForPage($pageId, false);

// Flatten XoopsObject list → template-ready card descriptors.
$rows = [];
foreach ($galleryItems ?: [] as $item) {
    $rows[] = [
        'id'           => (int)$item->getVar('gallery_id'),
        'title'        => (string)$item->getVar('title'),
        'description'  => (string)$item->getVar('description'),
        'image_url'    => (string)$item->getImageUrl(),
        'status_label' => sprintf(
            _AM_XPAGES_GALLERY_ORDER_STATUS,
            (int)$item->getVar('image_order')
        ) . ($item->getVar('image_status') ? _AM_XPAGES_STATUS_ACTIVE : _AM_XPAGES_STATUS_INACTIVE),
    ];
}

xpages_admin_render('xpages_admin_gallery_list.tpl', [
    'page_id'          => $pageId,
    'items'            => $rows,
    'label_add'        => _AM_XPAGES_GALLERY_ADD,
    'label_empty_text' => _AM_XPAGES_GALLERY_EMPTY,
    'label_add_first'  => _AM_XPAGES_GALLERY_ADD_FIRST,
]);

xoops_cp_footer();
