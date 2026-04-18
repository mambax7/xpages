<?php

declare(strict_types=1);

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
    echo '<div class="xp-alert xp-alert--error">' . _AM_XPAGES_HANDLER_UNAVAILABLE . '</div>';
    xoops_cp_footer();
    exit;
}

$pageId  = Request::getInt('page_id',  0,       'REQUEST');
$op      = Request::getCmd('op',       'list',  'REQUEST');
$fieldId = Request::getInt('field_id', 0,       'REQUEST');

$pageObj           = $pageId ? $pageHandler->get($pageId) : null;
$pageTitleDisplay  = $pageObj ? (string)$pageObj->getVar('title') : _AM_XPAGES_GLOBAL_FIELDS;

// Header (toolbar + back link) — always shown above the op branches.
xpages_admin_render('xpages_admin_fields_header.tpl', [
    'menu_title'          => _AM_XPAGES_MENU_FIELDS,
    'page_title_display'  => $pageTitleDisplay,
    'page_id'             => ($pageObj ? $pageId : 0),
    'label_back_to_page'  => _AM_XPAGES_BACK_TO_PAGE,
]);

// ── Sil ───────────────────────────────────────────────────────────────────────
if ($op === 'delete' && $fieldId) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || 1 !== Request::getInt('confirm', 0, 'POST')) {
        $fobj = $fieldHandler->get($fieldId);
        if ($fobj) {
            xpages_admin_render('xpages_admin_fields_delete_confirm.tpl', [
                'confirm_message' => sprintf(
                    _AM_XPAGES_FIELD_DELETE_CONFIRM,
                    htmlspecialchars((string)$fobj->getVar('field_label'), ENT_QUOTES)
                ),
                'field_id'  => $fieldId,
                'page_id'   => $pageId,
                'label_yes' => _AM_XPAGES_YES,
                'label_no'  => _AM_XPAGES_NO,
            ]);
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
    $fieldOptions = html_entity_decode($fieldOptions, ENT_QUOTES, 'UTF-8');
    $fieldOptions = preg_replace('/<br\s*\/?>/i', "\n", $fieldOptions);
    $fieldOptions = str_replace("\r\n", "\n", $fieldOptions);
    $fieldOptions = str_replace("\r", "\n", $fieldOptions);
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
        $field->setVar('field_options',  $fieldOptions);
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
    $field      = ($op === 'edit' && $fieldId) ? $fieldHandler->get($fieldId) : $fieldHandler->create();
    $typeLabels = XpagesField::getTypeLabels();
    $typeLabels['file'] = _AM_XPAGES_FIELD_TYPE_FILE_IMG;
    $selectedType = (string)$field->getVar('field_type', 'n');

    // Flatten type-label map to an ordered list of selectable options.
    $typeOptions = [];
    foreach ($typeLabels as $value => $label) {
        $typeOptions[] = [
            'value'    => (string)$value,
            'label'    => (string)$label,
            'selected' => ($selectedType === (string)$value),
        ];
    }

    // Build the "current file" descriptor used by the file-upload widget.
    $currentFile   = null;
    $isFile        = ($selectedType === 'file');
    $safeFieldFile = $isFile ? xpages_safe_filename($field->getVar('field_default', 'n')) : '';
    if ($op === 'edit' && $isFile && $safeFieldFile !== ''
        && file_exists(XOOPS_UPLOAD_PATH . '/xpages/' . $safeFieldFile)
    ) {
        $ext = strtolower(pathinfo($safeFieldFile, PATHINFO_EXTENSION));
        $currentFile = [
            'url'      => XOOPS_UPLOAD_URL . '/xpages/' . rawurlencode($safeFieldFile),
            'filename' => $safeFieldFile,
            'is_image' => in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true),
        ];
    }

    $jsonFlags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP;

    xpages_admin_render('xpages_admin_fields_form.tpl', [
        'form_title'          => $op === 'edit' ? _AM_XPAGES_EDIT_FIELD : _AM_XPAGES_ADD_FIELD,
        'page_id'             => $pageId,
        'field_id'            => $fieldId,
        'field'               => [
            'name'         => (string)$field->getVar('field_name',    'n'),
            'label'        => (string)$field->getVar('field_label',   'n'),
            'type'         => $selectedType,
            'options'      => (string)$field->getVar('field_options', 'n'),
            'default'      => (string)$field->getVar('field_default', 'n'),
            'desc'         => (string)$field->getVar('field_desc',    'n'),
            'order'        => (int)   $field->getVar('field_order'),
            'status'       => (bool)  $field->getVar('field_status'),
            'required'     => (bool)  $field->getVar('field_required'),
            'show_in_tpl'  => (bool)  $field->getVar('show_in_tpl'),
            'is_file'      => $isFile,
        ],
        'type_options'        => $typeOptions,
        'current_file'        => $currentFile,
        'label_name'          => _AM_XPAGES_FIELD_NAME,
        'label_label'         => _AM_XPAGES_FIELD_LABEL,
        'label_type'          => _AM_XPAGES_FIELD_TYPE,
        'label_options'       => _AM_XPAGES_FIELD_OPTIONS,
        'label_default'       => _AM_XPAGES_FIELD_DEFAULT,
        'label_desc'          => _AM_XPAGES_FIELD_DESC,
        'label_order'         => _AM_XPAGES_FIELD_ORDER,
        'label_status'        => _AM_XPAGES_FIELD_STATUS,
        'label_required'      => _AM_XPAGES_FIELD_REQUIRED,
        'label_show_in_tpl'   => _AM_XPAGES_FIELD_SHOW_IN_TPL,
        'label_active'        => _AM_XPAGES_ACTIVE,
        'label_inactive'      => _AM_XPAGES_INACTIVE,
        'label_file_current'  => _AM_XPAGES_FILE_CURRENT,
        'label_save'          => _AM_XPAGES_SAVE,
        'label_cancel'        => _AM_XPAGES_CANCEL,
        'help_name'           => _AM_XPAGES_FIELD_NAME_HELP,
        'help_options'        => _AM_XPAGES_FIELD_OPTIONS_HELP,
        'help_default'        => _AM_XPAGES_FIELD_DEFAULT_HELP,
        'help_file_replace'   => _AM_XPAGES_FILE_REPLACE_HINT,
        'options_hint_title'  => _AM_XPAGES_OPTIONS_HINT_TITLE,
        'options_hint_body'   => _AM_XPAGES_OPTIONS_HINT_BODY,
        'options_hint_example'=> _AM_XPAGES_OPTIONS_HINT_EXAMPLE,
        'sample_placeholder'  => _AM_XPAGES_FIELD_OPTIONS_SAMPLE_PLACEHOLDER,
        'sample_code'         => _AM_XPAGES_FIELD_OPTIONS_SAMPLE_CODE,
        'file_help_js'        => json_encode((string)_AM_XPAGES_FILE_FIELD_HELP,    $jsonFlags),
        'default_help_js'     => json_encode((string)_AM_XPAGES_FIELD_DEFAULT_HELP, $jsonFlags),
    ]);

    xoops_cp_footer();
    exit;
}

// ── Alan Listesi ──────────────────────────────────────────────────────────────
$fields = $pageId ? $fieldHandler->getFieldsForPage($pageId, false) : $fieldHandler->getGlobalFields(false);

$typeLabels = XpagesField::getTypeLabels();
$typeLabels['file'] = _AM_XPAGES_FIELD_TYPE_FILE_IMG;

// Flatten XoopsObject list into template-ready row descriptors.
$rows = [];
foreach ($fields ?: [] as $f) {
    $fid       = (int)$f->getVar('field_id');
    $type      = (string)$f->getVar('field_type', 'n');
    $fileThumb = null;
    $fileUrl   = null;
    if ($type === 'file' && !empty($f->getVar('field_default'))) {
        $safeFile = xpages_safe_filename($f->getVar('field_default', 'n'));
        if ($safeFile !== '') {
            $url = XOOPS_UPLOAD_URL . '/xpages/' . rawurlencode($safeFile);
            $ext = strtolower(pathinfo($safeFile, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                $fileThumb = $url;
            } else {
                $fileUrl = $url;
            }
        }
    }
    $rows[] = [
        'id'         => $fid,
        'name'       => (string)$f->getVar('field_name',  'n'),
        'label'      => (string)$f->getVar('field_label', 'n'),
        'type_label' => $typeLabels[$type] ?? $type,
        'order'      => (int)$f->getVar('field_order'),
        'status'     => (bool)$f->getVar('field_status'),
        'is_global'  => ((int)$f->getVar('page_id') === 0),
        'file_thumb' => $fileThumb,
        'file_url'   => $fileUrl,
    ];
}

xpages_admin_render('xpages_admin_fields_list.tpl', [
    'page_id'        => $pageId,
    'fields'         => $rows,
    'stat_text'      => sprintf(_AM_XPAGES_STAT_FIELDS, count($rows)),
    'no_fields_text' => _AM_XPAGES_NO_FIELDS,
    'col_name'       => _AM_XPAGES_FIELD_NAME,
    'col_label'      => _AM_XPAGES_FIELD_LABEL,
    'col_type'       => _AM_XPAGES_FIELD_TYPE,
    'col_order'      => _AM_XPAGES_FIELD_ORDER,
    'col_status'     => _AM_XPAGES_FIELD_STATUS,
    'col_actions'    => _AM_XPAGES_ACTIONS,
    'label_add'      => _AM_XPAGES_ADD_FIELD,
    'label_edit'     => _AM_XPAGES_EDIT,
    'label_delete'   => _AM_XPAGES_DELETE,
    'label_file_view'=> _AM_XPAGES_FILE_VIEW,
]);

xoops_cp_footer();
