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
xpages_admin_register_css();

if (class_exists('Xmf\\Module\\Admin')) {
    \Xmf\Module\Admin::getInstance()->displayNavigation('fields.php');
}

$fieldHandler = xpages_get_handler('field');
$pageHandler  = xpages_get_handler('page');
$valueHandler = xpages_get_handler('fieldvalue');

if (!$fieldHandler || !$pageHandler || !$valueHandler) {
    echo '<div class="xp-alert xp-alert--error">xPages handler unavailable.</div>';
    xoops_cp_footer();
    exit;
}

// Request::getInt / getCmd default to reading GET then POST when method='REQUEST'.
$pageId  = Request::getInt('page_id',  0,       'REQUEST');
$op      = Request::getCmd('op',       'list',  'REQUEST');
$fieldId = Request::getInt('field_id', 0,       'REQUEST');

$pageObj   = $pageId ? $pageHandler->get($pageId) : null;
$pageTitle = $pageObj ? htmlspecialchars((string)$pageObj->getVar('title'), ENT_QUOTES) : _AM_XPAGES_GLOBAL_FIELDS;

echo '<div class="xp-toolbar xp-toolbar--inline">';
echo '<h2>⚙️ ' . _AM_XPAGES_MENU_FIELDS . '</h2>';
echo '<span class="xp-text-muted">— ' . $pageTitle . '</span>';
echo '</div>';

if ($pageId && $pageObj) {
    echo '<p><a href="page_edit.php?page_id=' . $pageId . '" class="xp-action--edit">◀ ' . _AM_XPAGES_BACK_TO_PAGE . '</a></p>';
}

// ── Sil ───────────────────────────────────────────────────────────────────────
if ($op === 'delete' && $fieldId) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || 1 !== Request::getInt('confirm', 0, 'POST')) {
        $fobj = $fieldHandler->get($fieldId);
        if ($fobj) {
            echo '<div class="xp-alert xp-alert--warning">';
            echo '<p>⚠️ ' . sprintf(_AM_XPAGES_FIELD_DELETE_CONFIRM, htmlspecialchars((string)$fobj->getVar('field_label'), ENT_QUOTES)) . '</p>';
            echo '<form method="post" action="fields.php?op=delete&field_id=' . $fieldId . '&page_id=' . $pageId . '" class="xp-confirm-actions">';
            echo '<input type="hidden" name="op" value="delete">';
            echo '<input type="hidden" name="field_id" value="' . $fieldId . '">';
            echo '<input type="hidden" name="page_id" value="' . $pageId . '">';
            echo '<input type="hidden" name="confirm" value="1">';
            echo $GLOBALS['xoopsSecurity']->getTokenHTML();
            echo '<button type="submit" class="xp-btn xp-btn--danger">' . _AM_XPAGES_YES . '</button>';
            echo '<a href="fields.php?page_id=' . $pageId . '" class="xp-btn xp-btn--cancel">' . _AM_XPAGES_NO . '</a>';
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
    $cleanLines = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line !== '') {
            $cleanLines[] = $line;
        }
    }
    $fieldOptions = implode("\n", $cleanLines);

    if ($fieldHandler->fieldNameExists($fname, $pageId, $fieldId)) {
        echo '<div class="xp-alert xp-alert--error">' . _AM_XPAGES_FIELD_NAME_EXISTS . '</div>';
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
                echo '<div class="xp-alert xp-alert--error">' . _AM_XPAGES_INVALID_FILE_TYPE . '</div>';
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
        echo '<div class="xp-alert xp-alert--error">' . _AM_XPAGES_SAVE_ERROR . '</div>';
    }
}

// ── Ekle / Düzenle Formu ──────────────────────────────────────────────────────
if (in_array($op, ['add', 'edit'], true)) {
    $field = ($op === 'edit' && $fieldId) ? $fieldHandler->get($fieldId) : $fieldHandler->create();
    $typeLabels = XpagesField::getTypeLabels();
    $typeLabels['file'] = _AM_XPAGES_FIELD_TYPE_FILE_IMG;

    // Mevcut options değerini olduğu gibi göster (nl2br kullanma!)
    $currentOptions = (string)$field->getVar('field_options', 'n');
    $isFile = $field->getVar('field_type', 'n') === 'file';
    ?>

<div class="xp-form-card">
<h3><?= $op === 'edit' ? _AM_XPAGES_EDIT_FIELD : _AM_XPAGES_ADD_FIELD ?></h3>
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
        <select name="field_type" id="xpfTypeSel">
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
        <input type="text" name="field_default" id="xpfDefaultInput" value="<?= htmlspecialchars((string)$field->getVar('field_default', 'n'), ENT_QUOTES) ?>"<?= $isFile ? ' class="xp-hidden"' : '' ?>>
        <div id="xpfFileArea"<?= $isFile ? '' : ' class="xp-hidden"' ?>>
            <input type="file" name="field_file" accept="image/*,application/pdf,.doc,.docx,.zip">
            <?php if ($op === 'edit' && $isFile && !empty($field->getVar('field_default'))): ?>
                <?php $safeFieldFile = xpages_safe_filename($field->getVar('field_default', 'n')); ?>
                <?php $filePath = XOOPS_UPLOAD_PATH . '/xpages/' . $safeFieldFile; ?>
                <?php if ($safeFieldFile !== '' && file_exists($filePath)): ?>
                    <div class="xp-margin-top-8">
                        <strong><?= _AM_XPAGES_FILE_CURRENT ?></strong>
                        <?php
                        $ext = strtolower(pathinfo($safeFieldFile, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)): ?>
                            <img src="<?= XOOPS_UPLOAD_URL . '/xpages/' . rawurlencode($safeFieldFile) ?>" class="xpf-preview" alt="Preview">
                        <?php else: ?>
                            <a href="<?= XOOPS_UPLOAD_URL . '/xpages/' . rawurlencode($safeFieldFile) ?>" target="_blank"><?= htmlspecialchars($safeFieldFile) ?></a>
                        <?php endif; ?>
                        <br><small class="xp-text-muted"><?= _AM_XPAGES_FILE_REPLACE_HINT ?></small>
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
        <td><input type="number" name="field_order" value="<?= (int)$field->getVar('field_order') ?>" min="0" class="xp-input-small"></td>
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
<button type="submit" class="xp-btn xp-btn--add"><?= _AM_XPAGES_SAVE ?></button>
<a href="fields.php?page_id=<?= $pageId ?>" class="xp-cancel-link"><?= _AM_XPAGES_CANCEL ?></a>
</form>
</div>

<script>
(function () {
    var typeSel      = document.getElementById('xpfTypeSel');
    var optsRow      = document.getElementById('xpfOptsRow');
    var defaultInput = document.getElementById('xpfDefaultInput');
    var fileArea     = document.getElementById('xpfFileArea');
    var defaultDesc  = document.getElementById('xpfDefaultDesc');
    var optionsInput = document.getElementById('xpfOptionsInput');

    // Language strings passed from PHP — addslashes() is safe here because
    // the values are language constants authored by the module maintainers.
    var fileHelp    = <?= json_encode((string)_AM_XPAGES_FILE_FIELD_HELP,    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    var defaultHelp = <?= json_encode((string)_AM_XPAGES_FIELD_DEFAULT_HELP, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    function toggle() {
        var t = typeSel.value;

        if (t === 'select' || t === 'radio') {
            optsRow.classList.remove('xp-hidden');
            if (optionsInput) optionsInput.required = true;
        } else {
            optsRow.classList.add('xp-hidden');
            if (optionsInput) optionsInput.required = false;
        }

        if (t === 'file') {
            if (defaultInput) defaultInput.classList.add('xp-hidden');
            if (fileArea)     fileArea.classList.remove('xp-hidden');
            if (defaultDesc)  defaultDesc.textContent = fileHelp;
        } else {
            if (defaultInput) defaultInput.classList.remove('xp-hidden');
            if (fileArea)     fileArea.classList.add('xp-hidden');
            if (defaultDesc)  defaultDesc.textContent = defaultHelp;
        }
    }

    if (typeSel) {
        typeSel.addEventListener('change', toggle);
        toggle();
    }
})();
</script>
    <?php
    xoops_cp_footer();
    exit;
}

// ── Alan Listesi ──────────────────────────────────────────────────────────────
$fields = $pageId ? $fieldHandler->getFieldsForPage($pageId, false) : $fieldHandler->getGlobalFields(false);

echo '<p><a href="fields.php?op=add&page_id=' . $pageId . '" class="xp-btn xp-btn--add">➕ ' . _AM_XPAGES_ADD_FIELD . '</a></p>';

if (empty($fields)) {
    echo '<div class="xp-empty">';
    echo '<div class="xp-empty-icon">⚙️</div>';
    echo '<div class="xp-empty-text">' . _AM_XPAGES_NO_FIELDS . '</div>';
    echo '<a href="fields.php?op=add&page_id=' . $pageId . '" class="xp-empty-cta">+ ' . _AM_XPAGES_ADD_FIELD . '</a>';
    echo '</div>';
    xoops_cp_footer();
    exit;
}

$typeLabels = XpagesField::getTypeLabels();
$typeLabels['file'] = _AM_XPAGES_FIELD_TYPE_FILE_IMG;

echo '<div class="xp-table-wrap">';
echo '<table class="xp-table">';
echo '<thead><tr>';
foreach (['ID', _AM_XPAGES_FIELD_NAME, _AM_XPAGES_FIELD_LABEL, _AM_XPAGES_FIELD_TYPE, _AM_XPAGES_FIELD_ORDER, _AM_XPAGES_FIELD_STATUS, _AM_XPAGES_ACTIONS] as $th) {
    echo '<th>' . $th . '</th>';
}
echo '</tr></thead><tbody>';

foreach ($fields as $f) {
    $fid   = (int)$f->getVar('field_id');
    $type  = (string)$f->getVar('field_type', 'n');
    $scope = (int)$f->getVar('page_id') === 0 ? ' <small class="xp-scope-label">(global)</small>' : '';

    echo '<tr>';
    echo '<td>' . $fid . '</td>';
    echo '<td><code class="xp-alias-code">' . htmlspecialchars((string)$f->getVar('field_name', 'n'), ENT_QUOTES) . '</code>' . $scope . '</td>';
    echo '<td><strong>' . htmlspecialchars((string)$f->getVar('field_label', 'n'), ENT_QUOTES) . '</strong>';
    if ($type === 'file' && !empty($f->getVar('field_default'))) {
        $safeFile = xpages_safe_filename($f->getVar('field_default', 'n'));
        if ($safeFile !== '') {
            $fileUrl = XOOPS_UPLOAD_URL . '/xpages/' . rawurlencode($safeFile);
            $ext = strtolower(pathinfo($safeFile, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                echo '<br><img src="' . $fileUrl . '" class="xp-thumb-sm" alt="">';
            } else {
                echo '<br><small><a href="' . $fileUrl . '" target="_blank" class="xp-text-small">' . _AM_XPAGES_FILE_VIEW . '</a></small>';
            }
        }
    }
    echo '</td>';
    echo '<td>' . htmlspecialchars($typeLabels[$type] ?? $type, ENT_QUOTES) . '</td>';
    echo '<td class="xp-cell-center">' . (int)$f->getVar('field_order') . '</td>';
    echo '<td class="xp-cell-center">' . ($f->getVar('field_status') ? '✅' : '❌') . '</td>';
    echo '<td><div class="xp-actions">';
    echo '<a href="fields.php?op=edit&field_id=' . $fid . '&page_id=' . $pageId . '" class="xp-action--edit">✏️ ' . _AM_XPAGES_EDIT . '</a>';
    echo '<a href="fields.php?op=delete&field_id=' . $fid . '&page_id=' . $pageId . '" class="xp-action--delete">🗑️ ' . _AM_XPAGES_DELETE . '</a>';
    echo '</div></td>';
    echo '</tr>';
}
echo '</tbody></table></div>';

echo '<div class="xp-alert xp-alert--info">';
echo '📊 ' . sprintf(_AM_XPAGES_STAT_FIELDS, count($fields));
echo '</div>';

xoops_cp_footer();
