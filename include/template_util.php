<?php

declare(strict_types=1);

/**
 * xPages — Template + field-descriptor helpers.
 *
 * Split from the old include/functions.php god-file. Responsible for:
 *   - Building XpagesFieldDescriptor value objects for the extra-field
 *     input partial (admin/page_edit.php + xpages_field_input.tpl).
 *   - Assigning the xpages_page / xpages_gallery Smarty variables for
 *     the public single-page template (page.php + xpages_page.tpl).
 *
 * Depends on:
 *   - xpages_get_handler()       (handler_helpers.php)
 *   - xpages_normalize_url()     (url_util.php)
 *   - xpages_safe_filename()     (url_util.php)
 *
 * @package xpages
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * Build a template-ready descriptor for a single extra-field input.
 *
 * Returns a readonly XpagesFieldDescriptor value object. Templates
 * access its public properties via Smarty dot syntax ($field.type,
 * $field.options, ...); the `type` property is exposed as the enum's
 * string value so the template stays delimiter-agnostic.
 *
 * Unknown field_type strings fall back to XpagesFieldType::Text.
 *
 * @param mixed      $field XoopsObject field definition (or null)
 * @param string|int $value current stored value
 */
function xpages_build_field_descriptor($field, $value = ''): ?XpagesFieldDescriptor
{
    if (!$field) {
        return null;
    }

    xpages_require_field_classes();

    $fid      = (int)$field->getVar('field_id');
    $rawType  = (string)$field->getVar('field_type');
    $typeEnum = XpagesFieldType::tryFrom($rawType) ?? XpagesFieldType::Text;

    $base = [
        'id'       => $fid,
        'type'     => $typeEnum->value,
        'name'     => 'extra_fields[' . $fid . ']',
        'input_id' => 'extra_field_' . $fid,
        'label'    => (string)$field->getVar('field_label'),
        'desc'     => (string)$field->getVar('field_desc'),
        'required' => (bool)$field->getVar('field_required'),
        'value'    => (string)$value,
    ];

    // Exhaustive match — PHP errors if a new FieldType case is added
    // without a branch here, surfacing the gap at runtime.
    $extras = match ($typeEnum) {
        XpagesFieldType::Checkbox => [
            'checked' => ((int)$value === 1),
        ],
        XpagesFieldType::Select => array_merge(
            xpages_build_option_list($field, (string)$value, $fid),
            ['placeholder' => _AM_XPAGES_SELECT_PLACEHOLDER],
        ),
        XpagesFieldType::Radio => xpages_build_option_list($field, (string)$value, $fid),
        XpagesFieldType::File  => xpages_build_file_extras($fid, (string)$value),
        XpagesFieldType::Text, XpagesFieldType::Textarea, XpagesFieldType::Email,
        XpagesFieldType::Url,  XpagesFieldType::Tel,      XpagesFieldType::Number => [],
    };

    return new XpagesFieldDescriptor(...array_merge($base, $extras));
}

/**
 * Parse the stored field_options string into a list of descriptor rows.
 *
 * @return array{options: array<int,array{value:string,label:string,selected:bool,radio_id:string}>}
 */
function xpages_build_option_list($field, string $value, int $fid): array
{
    $raw = (string)$field->getVar('field_options');
    $raw = html_entity_decode($raw, ENT_QUOTES, 'UTF-8');
    $raw = preg_replace('/<br\s*\/?>/i', "\n", $raw);
    $raw = str_replace(["\r\n", "\r"], "\n", $raw);

    $options = [];
    $i = 0;
    foreach (explode("\n", trim($raw)) as $opt) {
        $opt = trim($opt);
        if ($opt === '') {
            continue;
        }
        $options[] = [
            'value'    => $opt,
            'label'    => $opt,
            'selected' => ($opt === $value),
            'radio_id' => 'extra_field_' . $fid . '_' . $i,
        ];
        $i++;
    }

    return ['options' => $options];
}

/**
 * Build the file-type specific descriptor extras.
 *
 * Only populates current_file_* when $value points at a safe filename
 * that survives xpages_safe_filename() — empty strings or traversal
 * attempts fall back to the "no file" template branch.
 *
 * @return array<string,mixed>
 */
function xpages_build_file_extras(int $fid, string $value): array
{
    $extras = [
        'file_input_name'  => 'extra_files[' . $fid . ']',
        'file_input_id'    => 'extra_file_'  . $fid,
        'labels'           => [
            'current_file' => _AM_XPAGES_FILE_CURRENT_LABEL,
            'replace_note' => _AM_XPAGES_FILE_REPLACE_NOTE,
            'file_none'    => _AM_XPAGES_FILE_NONE,
        ],
        'has_current_file' => false,
    ];

    if ($value === '') {
        return $extras;
    }

    $safeValue = xpages_safe_filename($value);
    if ($safeValue === '') {
        return $extras;
    }

    $ext = strtolower(pathinfo($safeValue, PATHINFO_EXTENSION));
    $extras['has_current_file']  = true;
    $extras['current_file_url']  = XOOPS_UPLOAD_URL . '/xpages/' . rawurlencode($safeValue);
    $extras['current_file_raw']  = $value;
    $extras['current_file_safe'] = $safeValue;
    $extras['is_image']          = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);

    return $extras;
}

/**
 * Load the FieldType enum + FieldDescriptor class on demand.
 *
 * The preloads/autoloader registers PSR-4 for XoopsModules\Xpages\* but
 * FieldType / FieldDescriptor are in the global namespace (their source
 * files ship at class/FieldType.php and class/FieldDescriptor.php).
 * This helper require_once's them explicitly for any code path that
 * runs before the normal XOOPS bootstrap.
 */
function xpages_require_field_classes(): void
{
    if (!class_exists('XpagesFieldDescriptor', false)) {
        require_once XOOPS_ROOT_PATH . '/modules/xpages/class/FieldType.php';
        require_once XOOPS_ROOT_PATH . '/modules/xpages/class/FieldDescriptor.php';
    }
}

/**
 * İlave alan input render fonksiyonu.
 *
 * Builds a descriptor via xpages_build_field_descriptor() and renders it
 * through the xpages_field_input.tpl partial. Returns the rendered HTML
 * so any external caller of this helper keeps working.
 */
function xpages_render_field_input($field, $value = '')
{
    $descriptor = xpages_build_field_descriptor($field, $value);
    if ($descriptor === null) {
        return '';
    }

    global $xoopsTpl;
    if (!isset($xoopsTpl) || !($xoopsTpl instanceof \XoopsTpl)) {
        require_once $GLOBALS['xoops']->path('class/template.php');
        $xoopsTpl = new \XoopsTpl();
    }

    $partial = XOOPS_ROOT_PATH . '/modules/xpages/templates/admin/xpages_field_input.tpl';

    // Local clone so the main-template $field assignment (if any) is preserved.
    $tpl = clone $xoopsTpl;
    $tpl->assign('field', $descriptor);
    return $tpl->fetch($partial);
}

/**
 * Sayfa değişkenlerini şablona ata (galeri dahil).
 *
 * Pre-computes display values for each extra field (image URLs, email
 * validation, URL normalisation) so the public template stays dumb.
 * Also assigns $xpages_gallery with the gallery rows for that page.
 */
function xpages_assign_page($page, $xoopsTpl): void
{
    if (!$page) {
        return;
    }

    $fieldHandler   = xpages_get_handler('field');
    $valueHandler   = xpages_get_handler('fieldvalue');
    $galleryHandler = xpages_get_handler('gallery');

    $pageId = (int)$page->getVar('page_id');
    $fields = [];
    $values = [];
    if ($fieldHandler && $valueHandler) {
        $fields = $fieldHandler->getFieldsForPage($pageId);
        $values = $valueHandler->getValuesForPage($pageId);
    }

    $extraFields     = [];
    $extraFieldsById = [];
    if ($fieldHandler && $valueHandler) {
        foreach ($fields as $field) {
            $fid          = (int)$field->getVar('field_id');
            $fname        = (string)$field->getVar('field_name', 'n');
            $fieldType    = (string)$field->getVar('field_type', 'n');
            $fieldValue   = $values[$fid] ?? $field->getVar('field_default', 'n');
            $displayValue = $fieldValue;
            $fileExt      = '';

            if ($fieldType === 'file' && !empty($fieldValue)) {
                $safeFile = xpages_safe_filename((string)$fieldValue);
                if ($safeFile !== '') {
                    $displayValue = XOOPS_UPLOAD_URL . '/xpages/' . rawurlencode($safeFile);
                    $fileExt      = strtolower(pathinfo($safeFile, PATHINFO_EXTENSION));
                } else {
                    $displayValue = '';
                }
            } elseif ($fieldType === 'url') {
                $displayValue = xpages_normalize_url((string)$fieldValue);
            } elseif ($fieldType === 'email') {
                $email        = trim((string)$fieldValue);
                $displayValue = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
            }

            $extraFields[$fname] = [
                'field_id'    => $fid,
                'field_name'  => $fname,
                'field_label' => $field->getVar('field_label'),
                'field_type'  => $fieldType,
                'value'       => $displayValue,
                'show_in_tpl' => (int)$field->getVar('show_in_tpl'),
            ];
            if ($fieldType === 'file') {
                $extraFields[$fname]['file_ext'] = $fileExt;
            }
            $extraFieldsById[$fid] = $extraFields[$fname];
        }
    }

    // Galeri verilerini ekle
    $galleryData = [];
    if ($galleryHandler) {
        $gallery = $galleryHandler->getGalleryForPage($pageId);
        foreach ($gallery as $g) {
            $imageUrl = xpages_normalize_url($g->getImageUrl());
            if ($imageUrl === '') {
                continue;
            }
            $galleryData[] = [
                'title'       => $g->getVar('title'),
                'description' => $g->getVar('description'),
                'image_url'   => $imageUrl,
            ];
        }
    }

    $xoopsTpl->assign('xpages_page', [
        'page_id'             => $page->getVar('page_id'),
        'title'               => $page->getVar('title'),
        'body'                => $page->getVar('body', 'n'),
        'short_desc'          => $page->getVar('short_desc'),
        'alias'               => $page->getVar('alias'),
        'update_date'         => $page->getVar('update_date'),
        'create_date'         => $page->getVar('create_date'),
        'hits'                => $page->getVar('hits'),
        'meta_title'          => $page->getVar('meta_title', 'n'),
        'meta_keywords'       => $page->getVar('meta_keywords', 'n'),
        'meta_desc'           => $page->getVar('meta_desc', 'n'),
        'robots'              => $page->getRobots(),
        'page_url'            => $page->getPageUrl(),
        'extra_fields'        => $extraFields,
        'extra_fields_by_id'  => $extraFieldsById,
    ]);

    $xoopsTpl->assign('xpages_gallery', $galleryData);

    // Absolute path to the xpages_field_value.tpl partial. Passed as a
    // template variable so xpages_page.tpl can <{include file=$var}>
    // without depending on the DB-resource registration (which only
    // activates after Module → Update installs the new template).
    $xoopsTpl->assign(
        'xpages_field_value_partial',
        XOOPS_ROOT_PATH . '/modules/xpages/templates/xpages_field_value.tpl'
    );
}
