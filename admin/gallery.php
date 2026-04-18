<?php
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

if (class_exists('Xmf\\Module\\Admin')) {
    \Xmf\Module\Admin::getInstance()->displayNavigation('gallery.php');
}

$galleryHandler = xpages_get_handler('gallery');
$pageHandler    = xpages_get_handler('page');

if (!$galleryHandler || !$pageHandler) {
    echo '<div style="margin:18px 0;padding:14px 16px;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;border-radius:6px">xPages handler unavailable.</div>';
    xoops_cp_footer();
    exit;
}

$pageId    = Request::getInt('page_id',    0,      'REQUEST');
$op        = Request::getCmd('op',         'list', 'REQUEST');
$galleryId = Request::getInt('gallery_id', 0,      'REQUEST');

$pageObj   = $pageId ? $pageHandler->get($pageId) : null;
$pageTitle = $pageObj ? htmlspecialchars((string)$pageObj->getVar('title'), ENT_QUOTES) : _AM_XPAGES_GALLERY_ALL_PAGES;

echo '<div style="display:flex;align-items:center;justify-content:space-between;margin:16px 0 20px">';
echo '<h2 style="margin:0;font-size:20px">' . _AM_XPAGES_GALLERY_TITLE . '</h2>';
echo '<span style="color:#6b7280;font-size:16px">— ' . $pageTitle . '</span>';
echo '</div>';

if ($pageId && $pageObj) {
    echo '<p style="margin:0 0 14px"><a href="page_edit.php?page_id=' . $pageId . '" style="color:#007bff;text-decoration:none;font-size:13px">◀ ' . _AM_XPAGES_BACK_TO_PAGE . '</a></p>';
}

// ── Sil ───────────────────────────────────────────────────────────────────────
if ($op === 'delete' && $galleryId) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || 1 !== Request::getInt('confirm', 0, 'POST')) {
        $gobj = $galleryHandler->get($galleryId);
        if ($gobj) {
            echo '<div style="background:#fff3cd;border:1px solid #ffc107;padding:18px;margin-bottom:16px;border-radius:8px">';
            echo '<p style="margin:0 0 12px">⚠️ ' . sprintf(_AM_XPAGES_GALLERY_DELETE_CONFIRM, htmlspecialchars((string)$gobj->getVar('title'), ENT_QUOTES)) . '</p>';
            echo '<form method="post" action="gallery.php?op=delete&gallery_id=' . $galleryId . '&page_id=' . $pageId . '" style="display:flex;gap:10px;align-items:center">';
            echo '<input type="hidden" name="op" value="delete">';
            echo '<input type="hidden" name="gallery_id" value="' . $galleryId . '">';
            echo '<input type="hidden" name="page_id" value="' . $pageId . '">';
            echo '<input type="hidden" name="confirm" value="1">';
            echo $GLOBALS['xoopsSecurity']->getTokenHTML();
            echo '<button type="submit" style="background:#dc3545;color:#fff;padding:7px 16px;border:none;border-radius:5px;cursor:pointer">' . _AM_XPAGES_YES . '</button>';
            echo '<a href="gallery.php?page_id=' . $pageId . '" style="background:#6c757d;color:#fff;padding:7px 16px;text-decoration:none;border-radius:5px">' . _AM_XPAGES_NO . '</a>';
            echo '</form></div>';
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
    echo '<div style="background:#f8d7da;color:#721c24;padding:11px;margin-bottom:14px;border-radius:5px">' . _AM_XPAGES_GALLERY_SAVE_ERROR . '</div>';
}

// ── Ekle / Düzenle Formu ──────────────────────────────────────────────────────
if (in_array($op, ['add', 'edit'], true)) {
    $gallery = ($op === 'edit' && $galleryId) ? $galleryHandler->get($galleryId) : $galleryHandler->create();
    ?>
<style>
.xpf-form-table{width:100%;border-collapse:collapse}
.xpf-form-table tr{border-bottom:1px solid #dee2e6}
.xpf-form-table td{padding:11px 12px;font-size:13px;vertical-align:top}
.xpf-form-table td:first-child{width:30%;font-weight:600}
.xpf-form-table input[type=text],.xpf-form-table input[type=number],
.xpf-form-table select,.xpf-form-table textarea{width:100%;padding:6px 8px;border:1px solid #ced4da;border-radius:4px}
.image-preview{max-width:200px;max-height:200px;margin-top:8px;border-radius:8px;box-shadow:0 2px 5px rgba(0,0,0,0.1)}
</style>

<div style="background:#fff;padding:22px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.07)">
<h3 style="margin:0 0 18px"><?= $op === 'edit' ? _AM_XPAGES_GALLERY_EDIT : _AM_XPAGES_GALLERY_NEW ?></h3>
<form method="post" action="gallery.php" enctype="multipart/form-data">
<input type="hidden" name="op" value="save">
<input type="hidden" name="page_id" value="<?= $pageId ?>">
<input type="hidden" name="gallery_id" value="<?= $galleryId ?>">
<?= $GLOBALS['xoopsSecurity']->getTokenHTML() ?>

<table class="xpf-form-table">
    <tr>
        <td><label><?= _AM_XPAGES_GALLERY_IMG_TITLE ?> *</label></td>
        <td><input type="text" name="title" value="<?= htmlspecialchars((string)$gallery->getVar('title', 'n'), ENT_QUOTES) ?>" required></td>
    </tr>
    <tr>
        <td><label><?= _AM_XPAGES_GALLERY_IMG_DESC ?></label></td>
        <td><textarea name="description" rows="3"><?= htmlspecialchars((string)$gallery->getVar('description', 'n'), ENT_QUOTES) ?></textarea></td>
    </tr>
    <tr>
        <td><label><?= _AM_XPAGES_GALLERY_IMG_FILE ?></label></td>
        <td>
            <input type="file" name="image_file" accept="image/*">
            <small class="xpf-desc"><?= _AM_XPAGES_GALLERY_IMG_FILE_HELP ?></small>
            <?php if ($op === 'edit' && !empty($gallery->getVar('image_path'))): ?>
                <?php $safeGalleryFile = xpages_safe_filename($gallery->getVar('image_path', 'n')); ?>
                <?php if ($safeGalleryFile !== ''): ?>
                    <br><img src="<?= XOOPS_UPLOAD_URL . '/xpages/gallery/' . rawurlencode($safeGalleryFile) ?>" class="image-preview">
                    <br><small><?= _AM_XPAGES_GALLERY_CURRENT_IMG ?></small>
                <?php endif; ?>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td><label><?= _AM_XPAGES_GALLERY_IMG_URL ?></label></td>
        <td>
            <input type="url" name="image_url" value="<?= htmlspecialchars((string)$gallery->getVar('image_url', 'n'), ENT_QUOTES) ?>" placeholder="https://...">
            <small class="xpf-desc"><?= _AM_XPAGES_GALLERY_IMG_URL_HELP ?></small>
        </td>
    </tr>
    <tr>
        <td><label><?= _AM_XPAGES_GALLERY_IMG_ORDER ?></label></td>
        <td><input type="number" name="image_order" value="<?= (int)$gallery->getVar('image_order') ?>" min="0" style="width:100px"></td>
    </tr>
    <tr>
        <td><label><?= _AM_XPAGES_GALLERY_IMG_STATUS ?></label></td>
        <td>
            <select name="image_status">
                <option value="1" <?= $gallery->getVar('image_status') ? 'selected' : '' ?>><?= _AM_XPAGES_ACTIVE ?></option>
                <option value="0" <?= !$gallery->getVar('image_status') ? 'selected' : '' ?>><?= _AM_XPAGES_INACTIVE ?></option>
            </select>
        </td>
    </tr>
</table>
<br>
<button type="submit" style="background:#28a745;color:#fff;border:none;padding:8px 22px;border-radius:5px"><?= _AM_XPAGES_SAVE ?></button>
<a href="gallery.php?page_id=<?= $pageId ?>" style="margin-left:12px;color:#6c757d"><?= _AM_XPAGES_CANCEL ?></a>
</form>
</div>
    <?php
    xoops_cp_footer();
    exit;
}

// ── Galeri Listesi ────────────────────────────────────────────────────────────
$gallery = $galleryHandler->getGalleryForPage($pageId, false);

echo '<p><a href="gallery.php?op=add&page_id=' . $pageId . '" style="background:#28a745;color:#fff;padding:8px 16px;text-decoration:none;border-radius:6px">' . _AM_XPAGES_GALLERY_ADD . '</a></p>';

if (empty($gallery)) {
    echo '<div style="background:#fff;padding:40px;border-radius:12px;text-align:center">';
    echo '<div style="font-size:46px;margin-bottom:10px">🖼️</div>';
    echo '<div style="font-size:17px;color:#6b7280">' . _AM_XPAGES_GALLERY_EMPTY . '</div>';
    echo '<a href="gallery.php?op=add&page_id=' . $pageId . '" style="display:inline-block;margin-top:16px;background:#007bff;color:#fff;padding:10px 22px;border-radius:8px;text-decoration:none">' . _AM_XPAGES_GALLERY_ADD_FIRST . '</a>';
    echo '</div>';
    xoops_cp_footer();
    exit;
}

echo '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;margin-top:20px">';

foreach ($gallery as $item) {
    $imageUrl = $item->getImageUrl();
    echo '<div style="background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.1)">';
    echo '<div style="height:200px;overflow:hidden;background:#f0f0f0;display:flex;align-items:center;justify-content:center">';
    if ($imageUrl) {
        echo '<img src="' . $imageUrl . '" style="width:100%;height:100%;object-fit:cover">';
    } else {
        echo '<div style="font-size:48px">🖼️</div>';
    }
    echo '</div>';
    echo '<div style="padding:12px">';
    echo '<h4 style="margin:0 0 5px">' . htmlspecialchars((string)$item->getVar('title'), ENT_QUOTES) . '</h4>';
    echo '<p style="margin:0 0 10px;font-size:12px;color:#6c757d">' . htmlspecialchars((string)$item->getVar('description'), ENT_QUOTES) . '</p>';
    echo '<div style="display:flex;gap:10px;justify-content:space-between;align-items:center">';
    echo '<small>' . sprintf(_AM_XPAGES_GALLERY_ORDER_STATUS, (int)$item->getVar('image_order')) . ($item->getVar('image_status') ? _AM_XPAGES_STATUS_ACTIVE : _AM_XPAGES_STATUS_INACTIVE) . '</small>';
    echo '<div>';
    echo '<a href="gallery.php?op=edit&gallery_id=' . $item->getVar('gallery_id') . '&page_id=' . $pageId . '" style="color:#007bff;margin-right:10px">✏️</a>';
    echo '<a href="gallery.php?op=delete&gallery_id=' . $item->getVar('gallery_id') . '&page_id=' . $pageId . '" style="color:#dc3545">🗑️</a>';
    echo '</div></div></div></div>';
}

echo '</div>';

xoops_cp_footer();
?>
