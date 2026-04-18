<?php
/**
 * xPages — Admin ilave alanlar yönetimi (Dosya/Resim desteği eklendi)
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

use Xmf\Request;

include_once '../../../include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/modules/xpages/include/functions.php';
xpages_admin_boot();

xoops_cp_header();

if (class_exists('Xmf\\Module\\Admin')) {
    \Xmf\Module\Admin::getInstance()->displayNavigation('fields.php');
}

$fieldHandler = xpages_get_handler('field');
$pageHandler  = xpages_get_handler('page');
$valueHandler = xpages_get_handler('fieldvalue');

if (!$fieldHandler || !$pageHandler || !$valueHandler) {
    echo '<div style="margin:18px 0;padding:14px 16px;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;border-radius:6px">xPages handler unavailable.</div>';
    xoops_cp_footer();
    exit;
}

// Request::getInt / getCmd default to reading GET then POST when method='REQUEST'.
$pageId  = Request::getInt('page_id',  0,       'REQUEST');
$op      = Request::getCmd('op',       'list',  'REQUEST');
$fieldId = Request::getInt('field_id', 0,       'REQUEST');

$pageObj   = $pageId ? $pageHandler->get($pageId) : null;
$pageTitle = $pageObj ? htmlspecialchars((string)$pageObj->getVar('title'), ENT_QUOTES) : _AM_XPAGES_GLOBAL_FIELDS;

echo '<div style="display:flex;align-items:center;gap:10px;margin:16px 0 20px">';
echo '<h2 style="margin:0;font-size:20px">⚙️ ' . _AM_XPAGES_MENU_FIELDS . '</h2>';
echo '<span style="color:#6b7280;font-size:16px">— ' . $pageTitle . '</span>';
echo '</div>';

if ($pageId && $pageObj) {
    echo '<p style="margin:0 0 14px"><a href="page_edit.php?page_id=' . $pageId . '" style="color:#007bff;text-decoration:none;font-size:13px">◀ ' . _AM_XPAGES_BACK_TO_PAGE . '</a></p>';
}

// ── Sil ───────────────────────────────────────────────────────────────────────
if ($op === 'delete' && $fieldId) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || 1 !== Request::getInt('confirm', 0, 'POST')) {
        $fobj = $fieldHandler->get($fieldId);
        if ($fobj) {
            echo '<div style="background:#fff3cd;border:1px solid #ffc107;padding:18px;margin-bottom:16px;border-radius:8px">';
            echo '<p style="margin:0 0 12px">⚠️ ' . sprintf(_AM_XPAGES_FIELD_DELETE_CONFIRM, htmlspecialchars((string)$fobj->getVar('field_label'), ENT_QUOTES)) . '</p>';
            echo '<form method="post" action="fields.php?op=delete&field_id=' . $fieldId . '&page_id=' . $pageId . '" style="display:flex;gap:10px;align-items:center">';
            echo '<input type="hidden" name="op" value="delete">';
            echo '<input type="hidden" name="field_id" value="' . $fieldId . '">';
            echo '<input type="hidden" name="page_id" value="' . $pageId . '">';
            echo '<input type="hidden" name="confirm" value="1">';
            echo $GLOBALS['xoopsSecurity']->getTokenHTML();
            echo '<button type="submit" style="background:#dc3545;color:#fff;padding:7px 16px;border:none;border-radius:5px;cursor:pointer">' . _AM_XPAGES_YES . '</button>';
            echo '<a href="fields.php?page_id=' . $pageId . '" style="background:#6c757d;color:#fff;padding:7px 16px;text-decoration:none;border-radius:5px">' . _AM_XPAGES_NO . '</a>';
            echo '</form></div>';
        }
        xoops_cp_footer();
        exit;
    }
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header('fields.php?page_id=' . $pageId, 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        exit;
    }
    $fobj = $fieldHandler->get($fieldId);
    if ($fobj) {
        if ($fobj->getVar('field_type') === 'file' && !empty($fobj->getVar('field_default'))) {
            $safeFile = xpages_safe_filename($fobj->getVar('field_default', 'n'));
            $filePath = $safeFile !== '' ? XOOPS_UPLOAD_PATH . '/xpages/' . $safeFile : '';
            if ($filePath !== '' && file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        if ($valueHandler && method_exists($valueHandler, 'deleteValuesForField')) {
            $valueHandler->deleteValuesForField($fieldId);
        }
        $fieldHandler->delete($fobj);
        redirect_header('fields.php?page_id=' . $pageId, 2, _AM_XPAGES_FIELD_DELETED);
        exit;
    }
    redirect_header('fields.php?page_id=' . $pageId, 2, _AM_XPAGES_PAGE_NOT_FOUND);
    exit;
}

// ── Kaydet ────────────────────────────────────────────────────────────────────
if ($op === 'save') {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header('fields.php?page_id=' . $pageId, 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        exit;
    }

    if (!is_object($GLOBALS['xoopsUser'])) {
        redirect_header('fields.php?page_id=' . $pageId, 3, _AM_XPAGES_SAVE_ERROR);
        exit;
    }

    $field = $fieldId ? $fieldHandler->get($fieldId) : $fieldHandler->create();
    $oldType = $fieldId ? $field->getVar('field_type') : '';

    $fname = preg_replace('/[^a-z0-9_]/', '', strtolower(trim(Request::getString('field_name', '', 'POST'))));
    if (empty($fname)) {
        $fname = 'field_' . time();
    }

// field_options değerini al ve temizle (RADIO/SELECT için)
$fieldOptions = Request::getString('field_options', '', 'POST');
// Önce HTML entity'leri dönüştür
$fieldOptions = html_entity_decode($fieldOptions, ENT_QUOTES, 'UTF-8');
// <br> etiketlerini newline'e çevir
$fieldOptions = preg_replace('/<br\s*\/?>/i', "\n", $fieldOptions);
// \r\n'leri \n'ye çevir
$fieldOptions = str_replace("\r\n", "\n", $fieldOptions);
$fieldOptions = str_replace("\r", "\n", $fieldOptions);
// Boş satırları temizle
$lines = explode("\n", $fieldOptions);
$cleanLines = array();
foreach ($lines as $line) {
    $line = trim($line);
    if ($line !== '') {
        $cleanLines[] = $line;
    }
}
$fieldOptions = implode("\n", $cleanLines);

    if ($fieldHandler->fieldNameExists($fname, $pageId, $fieldId)) {
        echo '<div style="background:#f8d7da;color:#721c24;padding:11px;margin-bottom:14px;border-radius:5px">' . _AM_XPAGES_FIELD_NAME_EXISTS . '</div>';
        $op = $fieldId ? 'edit' : 'add';
    } else {
        $field->setVar('page_id',        $pageId);
        $field->setVar('field_name',     $fname);
        $field->setVar('field_label',    Request::getString('field_label',    '',     'POST'));
        $field->setVar('field_type',     Request::getString('field_type',     'text', 'POST'));
        $field->setVar('field_options',  $fieldOptions);  // Orijinal haliyle kaydet
        $field->setVar('field_required', Request::getInt('field_required',    0,      'POST'));
        $field->setVar('field_order',    Request::getInt('field_order',       0,      'POST'));
        $field->setVar('field_status',   Request::getInt('field_status',      1,      'POST'));
        $field->setVar('field_desc',     Request::getString('field_desc',     '',     'POST'));

        if ($field->getVar('field_type') !== 'file') {
            $field->setVar('field_default',  Request::getString('field_default', '', 'POST'));
        } elseif ($oldType !== 'file' && empty($_FILES['field_file']['name'])) {
            $field->setVar('field_default', '');
        }

        $field->setVar('show_in_tpl',    Request::getInt('show_in_tpl', 1, 'POST'));

        if ($field->getVar('field_type') === 'file' && isset($_FILES['field_file']) && $_FILES['field_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = XOOPS_UPLOAD_PATH . '/xpages/';
            xpages_ensure_upload_dir($uploadDir);
            
            $ext = strtolower(pathinfo($_FILES['field_file']['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'zip'];
            
            if (!in_array($ext, $allowedExts, true) || !xpages_upload_is_allowed($_FILES['field_file']['tmp_name'], $ext)) {
                echo '<div style="background:#f8d7da;color:#721c24;padding:11px;margin-bottom:14px;border-radius:5px">' . _AM_XPAGES_INVALID_FILE_TYPE . '</div>';
                $op = $fieldId ? 'edit' : 'add';
            } else {
                if ($fieldId && !empty($field->getVar('field_default'))) {
                    $safeOldFile = xpages_safe_filename($field->getVar('field_default', 'n'));
                    $oldFile = $safeOldFile !== '' ? $uploadDir . $safeOldFile : '';
                    if ($oldFile !== '' && file_exists($oldFile)) {
                        @unlink($oldFile);
                    }
                }
                
                $newFileName = 'field_' . ($fieldId ?: time()) . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                if (move_uploaded_file($_FILES['field_file']['tmp_name'], $uploadDir . $newFileName)) {
                    $field->setVar('field_default', $newFileName);
                }
            }
        }

        if ($fieldHandler->insert($field)) {
            redirect_header('fields.php?page_id=' . $pageId, 2, _AM_XPAGES_FIELD_SAVED);
            exit;
        }
        echo '<div style="background:#f8d7da;color:#721c24;padding:11px;margin-bottom:14px;border-radius:5px">' . _AM_XPAGES_SAVE_ERROR . '</div>';
    }
}

// ── Ekle / Düzenle Formu ──────────────────────────────────────────────────────
if (in_array($op, ['add', 'edit'], true)) {
    $field = ($op === 'edit' && $fieldId) ? $fieldHandler->get($fieldId) : $fieldHandler->create();
    $typeLabels = XpagesField::getTypeLabels();
    $typeLabels['file'] = _AM_XPAGES_FIELD_TYPE_FILE_IMG;
    
    // Mevcut options değerini olduğu gibi göster (nl2br kullanma!)
    $currentOptions = (string)$field->getVar('field_options', 'n');
    ?>
<style>
.xpf-form-table{width:100%;border-collapse:collapse}
.xpf-form-table tr{border-bottom:1px solid #dee2e6}
.xpf-form-table tr:last-child{border-bottom:none}
.xpf-form-table td{padding:11px 12px;font-size:13px;vertical-align:top}
.xpf-form-table td:first-child{width:32%;font-weight:600;color:#374151}
.xpf-form-table input[type=text],.xpf-form-table input[type=number],
.xpf-form-table select,.xpf-form-table textarea{width:100%;box-sizing:border-box;padding:6px 8px;border:1px solid #ced4da;border-radius:4px;font-size:13px}
.xpf-desc{color:#6c757d;font-size:11px;display:block;margin-top:3px}
.xpf-preview{max-width:100px;max-height:100px;margin-top:8px;border-radius:6px;border:1px solid #dee2e6}
.xpf-options-help{background:#e8f4f8;padding:10px;border-radius:5px;margin-top:8px;font-size:12px}
</style>

<div style="background:#fff;padding:22px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.07);margin-top:16px">
<h3 style="margin:0 0 18px;font-size:16px"><?= $op === 'edit' ? _AM_XPAGES_EDIT_FIELD : _AM_XPAGES_ADD_FIELD ?></h3>
<form method="post" action="fields.php" enctype="multipart/form-data">
<input type="hidden" name="op" value="save">
<input type="hidden" name="page_id" value="<?= $pageId ?>">
<input type="hidden" name="field_id" value="<?= $fieldId ?>">
<?= $GLOBALS['xoopsSecurity']->getTokenHTML() ?>

<table class="xpf-form-table">
<tr>
    <td><label><?= _AM_XPAGES_FIELD_NAME ?> *</label><span class="xpf-desc"><?= _AM_XPAGES_FIELD_NAME_HELP ?></span></td>
    <td><input type="text" name="field_name" value="<?= htmlspecialchars((string)$field->getVar('field_name', 'n'), ENT_QUOTES) ?>" pattern="[a-z0-9_]+" required></td>
</tr>
<tr>
    <td><label><?= _AM_XPAGES_FIELD_LABEL ?> *</label></td>
    <td><input type="text" name="field_label" value="<?= htmlspecialchars((string)$field->getVar('field_label', 'n'), ENT_QUOTES) ?>" required></td>
</tr>
<tr>
    <td><label><?= _AM_XPAGES_FIELD_TYPE ?></label></td>
    <td>
        <select name="field_type" id="xpfTypeSel" onchange="xpfToggleOpts()">
        <?php foreach ($typeLabels as $k => $v): ?>
            <option value="<?= $k ?>" <?= $field->getVar('field_type', 'n') === $k ? 'selected' : '' ?>><?= htmlspecialchars($v, ENT_QUOTES) ?></option>
        <?php endforeach; ?>
        </select>
    </td>
</tr>
<tr id="xpfOptsRow">
    <td><label><?= _AM_XPAGES_FIELD_OPTIONS ?></label><span class="xpf-desc"><?= _AM_XPAGES_FIELD_OPTIONS_HELP ?></span></td>
    <td>
        <textarea name="field_options" id="xpfOptionsInput" rows="5" placeholder="<?= _AM_XPAGES_FIELD_OPTIONS_SAMPLE_PLACEHOLDER ?>"><?= htmlspecialchars($currentOptions, ENT_QUOTES) ?></textarea>
        <div class="xpf-options-help">
            <?= _AM_XPAGES_OPTIONS_HINT_TITLE ?><br>
            <?= _AM_XPAGES_OPTIONS_HINT_BODY ?><br>
            <?= _AM_XPAGES_OPTIONS_HINT_EXAMPLE ?><br>
            <code><?= _AM_XPAGES_FIELD_OPTIONS_SAMPLE_CODE ?></code>
        </div>
    </td>
</tr>
<tr id="xpfDefaultRow">
    <td><label><?= _AM_XPAGES_FIELD_DEFAULT ?></label><span class="xpf-desc" id="xpfDefaultDesc"><?= _AM_XPAGES_FIELD_DEFAULT_HELP ?></span></td>
    <td id="xpfDefaultCell">
        <input type="text" name="field_default" id="xpfDefaultInput" value="<?= htmlspecialchars((string)$field->getVar('field_default', 'n'), ENT_QUOTES) ?>" style="<?= $field->getVar('field_type', 'n') === 'file' ? 'display:none' : '' ?>">
        <div id="xpfFileArea" style="<?= $field->getVar('field_type', 'n') === 'file' ? '' : 'display:none' ?>">
            <input type="file" name="field_file" accept="image/*,application/pdf,.doc,.docx,.zip">
            <?php if ($op === 'edit' && $field->getVar('field_type', 'n') === 'file' && !empty($field->getVar('field_default'))): ?>
                <?php $safeFieldFile = xpages_safe_filename($field->getVar('field_default', 'n')); ?>
                <?php $filePath = XOOPS_UPLOAD_PATH . '/xpages/' . $safeFieldFile; ?>
                <?php if ($safeFieldFile !== '' && file_exists($filePath)): ?>
                    <div style="margin-top:8px">
                        <strong><?= _AM_XPAGES_FILE_CURRENT ?></strong>
                        <?php 
                        $ext = strtolower(pathinfo($safeFieldFile, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)): ?>
                            <img src="<?= XOOPS_UPLOAD_URL . '/xpages/' . rawurlencode($safeFieldFile) ?>" class="xpf-preview" alt="Preview">
                        <?php else: ?>
                            <a href="<?= XOOPS_UPLOAD_URL . '/xpages/' . rawurlencode($safeFieldFile) ?>" target="_blank"><?= htmlspecialchars($safeFieldFile) ?></a>
                        <?php endif; ?>
                        <br><small style="color:#6c757d"><?= _AM_XPAGES_FILE_REPLACE_HINT ?></small>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
      </td>
    </tr>
    <tr>
        <td><label><?= _AM_XPAGES_FIELD_DESC ?></label></td>
        <td><input type="text" name="field_desc" value="<?= htmlspecialchars((string)$field->getVar('field_desc', 'n'), ENT_QUOTES) ?>"></td>
    </tr>
    <tr>
        <td><label><?= _AM_XPAGES_FIELD_ORDER ?></label></td>
        <td><input type="number" name="field_order" value="<?= (int)$field->getVar('field_order') ?>" min="0" style="width:100px"></td>
    </tr>
    <tr>
        <td><label><?= _AM_XPAGES_FIELD_STATUS ?></label></td>
        <td>
            <select name="field_status">
                <option value="1" <?= $field->getVar('field_status') ? 'selected' : '' ?>><?= _AM_XPAGES_ACTIVE ?></option>
                <option value="0" <?= !$field->getVar('field_status') ? 'selected' : '' ?>><?= _AM_XPAGES_INACTIVE ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td><label><?= _AM_XPAGES_FIELD_REQUIRED ?></label></td>
        <td><input type="checkbox" name="field_required" value="1" <?= $field->getVar('field_required') ? 'checked' : '' ?>></td>
    </tr>
    <tr>
        <td><label><?= _AM_XPAGES_FIELD_SHOW_IN_TPL ?></label></td>
        <td><input type="checkbox" name="show_in_tpl" value="1" <?= $field->getVar('show_in_tpl') ? 'checked' : '' ?>></td>
    </tr>
</table>

<br>
<button type="submit" style="background:#28a745;color:#fff;border:none;padding:8px 22px;cursor:pointer;border-radius:5px;font-size:13px"><?= _AM_XPAGES_SAVE ?></button>
<a href="fields.php?page_id=<?= $pageId ?>" style="margin-left:12px;color:#6c757d;font-size:13px"><?= _AM_XPAGES_CANCEL ?></a>
</form>
</div>

<script>
function xpfToggleOpts() {
    var t = document.getElementById('xpfTypeSel').value;
    var optsRow = document.getElementById('xpfOptsRow');
    var defaultInput = document.getElementById('xpfDefaultInput');
    var fileArea = document.getElementById('xpfFileArea');
    var defaultDesc = document.getElementById('xpfDefaultDesc');
    var optionsInput = document.getElementById('xpfOptionsInput');
    
    if (t === 'select' || t === 'radio') {
        optsRow.style.display = '';
        if (optionsInput) optionsInput.required = true;
    } else {
        optsRow.style.display = 'none';
        if (optionsInput) optionsInput.required = false;
    }
    
    if (t === 'file') {
        if (defaultInput) defaultInput.style.display = 'none';
        if (fileArea) fileArea.style.display = '';
        if (defaultDesc) defaultDesc.innerHTML = '<?= addslashes(_AM_XPAGES_FILE_FIELD_HELP) ?>';
    } else {
        if (defaultInput) defaultInput.style.display = '';
        if (fileArea) fileArea.style.display = 'none';
        if (defaultDesc) defaultDesc.innerHTML = '<?= addslashes(_AM_XPAGES_FIELD_DEFAULT_HELP) ?>';
    }
}
xpfToggleOpts();
</script>
    <?php
    xoops_cp_footer();
    exit;
}

// ── Alan Listesi ──────────────────────────────────────────────────────────────
$fields = $pageId ? $fieldHandler->getFieldsForPage($pageId, false) : $fieldHandler->getGlobalFields(false);

echo '<p><a href="fields.php?op=add&page_id=' . $pageId . '" style="background:#28a745;color:#fff;padding:8px 16px;text-decoration:none;border-radius:6px;font-size:13px">➕ ' . _AM_XPAGES_ADD_FIELD . '</a></p>';

if (empty($fields)) {
    echo '<div style="background:#fff;padding:40px;border-radius:12px;text-align:center;box-shadow:0 2px 8px rgba(0,0,0,.07)">';
    echo '<div style="font-size:46px;margin-bottom:10px">⚙️</div>';
    echo '<div style="font-size:17px;color:#6b7280">' . _AM_XPAGES_NO_FIELDS . '</div>';
    echo '<a href="fields.php?op=add&page_id=' . $pageId . '" style="display:inline-block;margin-top:16px;background:#007bff;color:#fff;padding:10px 22px;border-radius:8px;text-decoration:none">+ ' . _AM_XPAGES_ADD_FIELD . '</a>';
    echo '</div>';
    xoops_cp_footer();
    exit;
}

$typeLabels = XpagesField::getTypeLabels();
$typeLabels['file'] = _AM_XPAGES_FIELD_TYPE_FILE_IMG;

echo '<div style="overflow-x:auto">';
echo '<table style="width:100%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.07)">';
echo '<thead><tr style="background:#f8f9fa;border-bottom:2px solid #dee2e6">';
foreach (['ID', _AM_XPAGES_FIELD_NAME, _AM_XPAGES_FIELD_LABEL, _AM_XPAGES_FIELD_TYPE, _AM_XPAGES_FIELD_ORDER, _AM_XPAGES_FIELD_STATUS, _AM_XPAGES_ACTIONS] as $th) {
    echo '<th style="padding:11px 14px;text-align:left;font-size:13px">' . $th . '</th>';
}
echo '</tr></thead><tbody>';

foreach ($fields as $i => $f) {
    $fid   = (int)$f->getVar('field_id');
    $type  = (string)$f->getVar('field_type', 'n');
    $scope = (int)$f->getVar('page_id') === 0 ? ' <small style="color:#6c757d">(global)</small>' : '';
    $bg    = $i % 2 ? '#f8f9fa' : '#fff';

    echo '<tr style="border-bottom:1px solid #dee2e6;background:' . $bg . '">';
    echo '<td style="padding:11px 14px;font-size:13px">' . $fid . '</td>';
    echo '<td style="padding:11px 14px"><code style="background:#f1f3f5;padding:2px 6px;border-radius:3px;font-size:12px">' . htmlspecialchars((string)$f->getVar('field_name', 'n'), ENT_QUOTES) . '</code>' . $scope . '</td>';
    echo '<td style="padding:11px 14px"><strong>' . htmlspecialchars((string)$f->getVar('field_label', 'n'), ENT_QUOTES) . '</strong>';
    if ($type === 'file' && !empty($f->getVar('field_default'))) {
        $safeFile = xpages_safe_filename($f->getVar('field_default', 'n'));
        if ($safeFile !== '') {
            $fileUrl = XOOPS_UPLOAD_URL . '/xpages/' . rawurlencode($safeFile);
            $ext = strtolower(pathinfo($safeFile, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                echo '<br><img src="' . $fileUrl . '" style="max-width:50px;max-height:50px;margin-top:5px;border-radius:4px">';
            } else {
                echo '<br><small><a href="' . $fileUrl . '" target="_blank" style="font-size:11px">' . _AM_XPAGES_FILE_VIEW . '</a></small>';
            }
        }
    }
    echo '</td>';
    echo '<td style="padding:11px 14px;font-size:13px">' . htmlspecialchars($typeLabels[$type] ?? $type, ENT_QUOTES) . '</td>';
    echo '<td style="padding:11px 14px;text-align:center">' . (int)$f->getVar('field_order') . '</td>';
    echo '<td style="padding:11px 14px;text-align:center">' . ($f->getVar('field_status') ? '✅' : '❌') . '</td>';
    echo '<td style="padding:11px 14px">';
    echo '<div style="display:flex;gap:10px">';
    echo '<a href="fields.php?op=edit&field_id=' . $fid . '&page_id=' . $pageId . '" style="color:#007bff;font-size:13px;text-decoration:none">✏️ ' . _AM_XPAGES_EDIT . '</a>';
    echo '<a href="fields.php?op=delete&field_id=' . $fid . '&page_id=' . $pageId . '" style="color:#dc3545;font-size:13px;text-decoration:none">🗑️ ' . _AM_XPAGES_DELETE . '</a>';
    echo '</div></td>';
}
echo '</tbody></table></div>';

echo '<div style="margin-top:14px;padding:10px;background:#e9ecef;border-radius:6px;text-align:center;font-size:13px">';
echo '📊 ' . sprintf(_AM_XPAGES_STAT_FIELDS, count($fields));
echo '</div>';

xoops_cp_footer();
?>
