<?php

declare(strict_types=1);

/**
 * xPages — Genel yardımcı fonksiyonlar
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * Handler yükleme fonksiyonu.
 *
 * Thin wrapper around \XoopsModules\Xpages\Helper::getHandler(). The
 * parent Xmf\Module\Helper implementation loads class/{name}.php and
 * instantiates Xpages{Name}Handler for us, so this function just
 * normalises the "not found" case (false → null) for legacy callers.
 */
function xpages_get_handler(string $name)
{
    $handler = \XoopsModules\Xpages\Helper::getInstance()->getHandler($name);
    return $handler !== false ? $handler : null;
}

/**
 * Admin boot — load language files + enforce module-level admin rights.
 *
 * XOOPS's include/cp_header.php verifies system-admin-group membership, but
 * a site may grant module-level admin rights to a narrower group (via
 * system admin → groups → module admin). Without this guard, any user in
 * the system-admin group can reach xpages admin pages even if they were
 * only granted admin on a DIFFERENT module.
 */
function xpages_admin_boot() {
    xpages_load_language('admin');
    xpages_load_language('modinfo');
    xpages_require_module_admin();
}

/**
 * Register the module's admin stylesheet with the XOOPS admin theme.
 *
 * Must be called AFTER xoops_cp_header() because $GLOBALS['xoTheme'] is
 * populated by that call. All admin controllers invoke this via the
 * common bootstrap path: xpages_admin_boot() → xoops_cp_header() → this.
 */
function xpages_admin_register_css(): void {
    if (!isset($GLOBALS['xoTheme']) || !is_object($GLOBALS['xoTheme'])) {
        return;
    }
    $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/modules/xpages/assets/css/admin.css');
}

/**
 * Render an admin template file with a set of assignments.
 *
 * Encapsulates the $xoopsTpl->assign()/fetch() dance every admin
 * controller performs when it needs to emit a block of HTML. Templates
 * live in modules/xpages/templates/admin/ and are looked up by filename
 * relative to that directory (no leading slash).
 *
 * The helper also auto-injects two convenience values that every admin
 * template is likely to want:
 *   - xoops_token_html — the CSRF token HTML block, ready to drop inside
 *     a <form>. Saves every template from wiring up
 *     $GLOBALS['xoopsSecurity']->getTokenHTML() manually.
 *   - xpages_upload_url — the module's public upload URL (used by any
 *     template that renders previews of user-uploaded files).
 *
 * @param string              $template Filename under templates/admin/ (e.g. 'xpages_admin_pages.tpl')
 * @param array<string,mixed> $vars     Smarty variable assignments
 */
function xpages_admin_render(string $template, array $vars = []): void {
    global $xoopsTpl;
    if (!isset($xoopsTpl) || !($xoopsTpl instanceof \XoopsTpl)) {
        require_once $GLOBALS['xoops']->path('class/template.php');
        $xoopsTpl = new \XoopsTpl();
    }

    // Allow <{include file="xpages_*.tpl"}> inside admin templates to
    // resolve from templates/admin/ without a full path prefix.
    $adminTplDir = XOOPS_ROOT_PATH . '/modules/xpages/templates/admin/';
    if (method_exists($xoopsTpl, 'addTemplateDir')) {
        $xoopsTpl->addTemplateDir($adminTplDir);
    }

    if (isset($GLOBALS['xoopsSecurity']) && is_object($GLOBALS['xoopsSecurity'])) {
        $xoopsTpl->assign('xoops_token_html', $GLOBALS['xoopsSecurity']->getTokenHTML());
    }
    $xoopsTpl->assign('xpages_upload_url', XOOPS_UPLOAD_URL . '/xpages/');

    foreach ($vars as $key => $value) {
        $xoopsTpl->assign($key, $value);
    }

    echo $xoopsTpl->fetch($adminTplDir . $template);
}

/**
 * Register the module's public-facing stylesheet.
 *
 * Must be called AFTER XOOPS_ROOT_PATH/header.php has been included (the
 * main public header is what populates $xoTheme on the front-end). Used
 * by page.php and index.php so the public layout can use class names
 * instead of inline style attributes in the .tpl templates.
 */
function xpages_register_public_css(): void {
    if (!isset($GLOBALS['xoTheme']) || !is_object($GLOBALS['xoTheme'])) {
        return;
    }
    $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/modules/xpages/assets/css/style.css');
}

/**
 * Redirect any request that isn't from a user with admin rights
 * specifically for the xpages module. Call at the top of every admin
 * controller (done automatically via xpages_admin_boot()).
 */
function xpages_require_module_admin(): void {
    global $xoopsUser, $xoopsModule;

    // cp_header.php already enforced the system admin-group check; this is
    // defense-in-depth for the per-module admin ACL.
    if (!is_object($xoopsUser)
        || !is_object($xoopsModule)
        || !$xoopsUser->isAdmin($xoopsModule->getVar('mid'))
    ) {
        redirect_header(
            XOOPS_URL . '/user.php',
            3,
            defined('_NOPERM') ? _NOPERM : 'You do not have permission to access this page.'
        );
        exit;
    }
}

/**
 * Dil dosyası yükleme.
 *
 * Delegates to the module Helper, which uses Xmf\Language::load() and
 * handles the english-fallback lookup + log integration for free.
 */
function xpages_load_language(string $type = 'main'): void
{
    \XoopsModules\Xpages\Helper::getInstance()->loadLanguage($type);
}

/**
 * URL değerini doğrula ve normalize et.
 * İzin verilenler: http, https, ftp, mailto ve göreli URL'ler.
 */
function xpages_normalize_url($url, $allowRelative = true) {
    $url = html_entity_decode(trim((string)$url), ENT_QUOTES, 'UTF-8');
    $url = preg_replace('/[\x00-\x1F\x7F]+/u', '', $url);

    if ($url === '') {
        return '';
    }

    if (preg_match('/[\s<>"\']/', $url)) {
        return '';
    }

    if (str_starts_with($url, '//')) {
        return '';
    }

    $colonPos     = strpos($url, ':');
    $delimiterPos  = strcspn($url, '/?#');
    $hasScheme     = $colonPos !== false && ($delimiterPos === strlen($url) || $colonPos < $delimiterPos);

    if ($hasScheme) {
        $scheme         = strtolower(substr($url, 0, $colonPos));
        $allowedSchemes = ['http', 'https', 'ftp', 'mailto'];
        if (!in_array($scheme, $allowedSchemes, true)) {
            return '';
        }
        // filter_var is a useful final structural check for http/https/ftp
        // (catches things like unclosed IPv6 brackets). Skip it for mailto
        // because FILTER_VALIDATE_URL rejects mailto: across PHP versions,
        // and skip for relative URLs (they fail the same check by design).
        if ($scheme !== 'mailto' && filter_var($url, FILTER_VALIDATE_URL) === false) {
            return '';
        }
    } elseif (!$allowRelative) {
        return '';
    }

    return $url;
}

/**
 * Dosya adını güvenli hale getir.
 */
function xpages_safe_filename($value) {
    $value = html_entity_decode(trim((string)$value), ENT_QUOTES, 'UTF-8');
    if ($value === '') {
        return '';
    }

    $path = parse_url($value, PHP_URL_PATH);
    if ($path !== null && $path !== false && $path !== '') {
        $value = $path;
    }

    $value = basename(str_replace('\\', '/', $value));
    $value = preg_replace('/[^A-Za-z0-9._-]/', '', $value);

    if ($value === '' || $value === '.' || $value === '..') {
        return '';
    }

    return $value;
}

/**
 * Upload dizinini hazırla ve yürütülebilir dosyaları kapat.
 */
function xpages_ensure_upload_dir($dir) {
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
            return false;
        }
    }

    $guardFile = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . '.htaccess';
    if (!file_exists($guardFile)) {
        $guardContent = "<FilesMatch \"\\.(php|phtml|phar|phps|pl|py|cgi|sh)$\">\n"
            . "    Require all denied\n"
            . "    Deny from all\n"
            . "</FilesMatch>\n";
        @file_put_contents($guardFile, $guardContent, LOCK_EX);
    }

    return true;
}

/**
 * Dosya yükleme türünü MIME ile doğrula.
 */
function xpages_upload_is_allowed($tmpFile, $ext) {
    $ext = strtolower((string)$ext);
    $allowedMimeMap = [
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png'],
        'gif'  => ['image/gif'],
        'webp' => ['image/webp'],
        'pdf'  => ['application/pdf'],
        'doc'  => ['application/msword', 'application/vnd.ms-office', 'application/octet-stream'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/octet-stream'],
        'zip'  => ['application/zip', 'application/x-zip-compressed', 'application/octet-stream'],
    ];

    if (!isset($allowedMimeMap[$ext]) || !is_file($tmpFile) || !is_readable($tmpFile)) {
        return false;
    }

    if (!function_exists('finfo_open')) {
        return true;
    }

    $finfo = @finfo_open(FILEINFO_MIME_TYPE);
    if (!$finfo) {
        return true;
    }

    $mime = @finfo_file($finfo, $tmpFile);
    @finfo_close($finfo);

    if ($mime === false || $mime === '') {
        return false;
    }

    return in_array($mime, $allowedMimeMap[$ext], true);
}

/**
 * Sayfanın tüm alt sayfa ID'lerini topla.
 */
function xpages_collect_descendant_ids($pageHandler, $pageId, array &$descendantIds, array &$visited = []) {
    $pageId = (int)$pageId;
    if ($pageId <= 0 || isset($visited[$pageId]) || !$pageHandler) {
        return;
    }

    $visited[$pageId] = true;

    $criteria = new Criteria('parent_id', $pageId);
    $children = $pageHandler->getObjects($criteria) ?: [];

    foreach ($children as $child) {
        $childId = (int)$child->getVar('page_id');
        if ($childId <= 0) {
            continue;
        }
        if (!in_array($childId, $descendantIds, true)) {
            $descendantIds[] = $childId;
        }
        xpages_collect_descendant_ids($pageHandler, $childId, $descendantIds, $visited);
    }
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
 * PSR-4 autoloading arrives with 7f (preloads/autoloader.php); until
 * then the descriptor builder require_once's them explicitly.
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
 * so existing callers (admin/page_edit.php) continue to work.
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

    // Use a local clone so the main-template $field assignment (if any) is preserved.
    $tpl = clone $xoopsTpl;
    $tpl->assign('field', $descriptor);
    return $tpl->fetch($partial);
}

/**
 * Sayfa değişkenlerini şablona ata (Galeri desteği eklendi)
 */
function xpages_assign_page($page, $xoopsTpl) {
    if (!$page) return;
    
    $fieldHandler = xpages_get_handler('field');
    $valueHandler = xpages_get_handler('fieldvalue');
    $galleryHandler = xpages_get_handler('gallery');
    
    $pageId = $page->getVar('page_id');
    $fields = array();
    $values = array();
    if ($fieldHandler && $valueHandler) {
        $fields = $fieldHandler->getFieldsForPage($pageId);
        $values = $valueHandler->getValuesForPage($pageId);
    }
    
    $extraFields = array();
    $extraFieldsById = array();
    if ($fieldHandler && $valueHandler) {
        foreach ($fields as $field) {
            $fid = (int)$field->getVar('field_id');
            $fname = (string)$field->getVar('field_name', 'n');
            $fieldType = (string)$field->getVar('field_type', 'n');
            $fieldValue = $values[$fid] ?? $field->getVar('field_default', 'n');
            $displayValue = $fieldValue;
            $fileExt = '';
            
            if ($fieldType === 'file' && !empty($fieldValue)) {
                $safeFile = xpages_safe_filename((string)$fieldValue);
                if ($safeFile !== '') {
                    $displayValue = XOOPS_UPLOAD_URL . '/xpages/' . rawurlencode($safeFile);
                    $fileExt = strtolower(pathinfo($safeFile, PATHINFO_EXTENSION));
                } else {
                    $displayValue = '';
                }
            } elseif ($fieldType === 'url') {
                $displayValue = xpages_normalize_url((string)$fieldValue);
            } elseif ($fieldType === 'email') {
                $email = trim((string)$fieldValue);
                $displayValue = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
            }
            
            $extraFields[$fname] = array(
                'field_id'      => $fid,
                'field_name'    => $fname,
                'field_label'   => $field->getVar('field_label'),
                'field_type'    => $fieldType,
                'value'         => $displayValue,
                'show_in_tpl'   => (int)$field->getVar('show_in_tpl'),
            );
            if ($fieldType === 'file') {
                $extraFields[$fname]['file_ext'] = $fileExt;
            }
            $extraFieldsById[$fid] = $extraFields[$fname];
        }
    }
    
    // Galeri verilerini ekle
    $galleryData = array();
    if ($galleryHandler) {
        $gallery = $galleryHandler->getGalleryForPage($pageId);
        foreach ($gallery as $g) {
            $imageUrl = xpages_normalize_url($g->getImageUrl());
            if ($imageUrl === '') {
                continue;
            }
            $galleryData[] = array(
                'title'       => $g->getVar('title'),
                'description' => $g->getVar('description'),
                'image_url'   => $imageUrl,
            );
        }
    }
    
    $xoopsTpl->assign('xpages_page', array(
        'page_id'      => $page->getVar('page_id'),
        'title'        => $page->getVar('title'),
        'body'         => $page->getVar('body', 'n'),
        'short_desc'   => $page->getVar('short_desc'),
        'alias'        => $page->getVar('alias'),
        'update_date'  => $page->getVar('update_date'),
        'create_date'  => $page->getVar('create_date'),
        'hits'         => $page->getVar('hits'),
        'meta_title'   => $page->getVar('meta_title', 'n'),
        'meta_keywords'=> $page->getVar('meta_keywords', 'n'),
        'meta_desc'    => $page->getVar('meta_desc', 'n'),
        'robots'       => $page->getRobots(),
        'page_url'     => $page->getPageUrl(),
        'extra_fields' => $extraFields,
        'extra_fields_by_id' => $extraFieldsById,
    ));
    
    $xoopsTpl->assign('xpages_gallery', $galleryData);
}

/**
 * Sayfa verilerini sil
 */
function xpages_delete_page_data($pageId, array &$visited = array()) {
    $pageHandler  = xpages_get_handler('page');
    $valueHandler = xpages_get_handler('fieldvalue');
    $fieldHandler = xpages_get_handler('field');
    $galleryHandler = xpages_get_handler('gallery');

    $pageId = (int)$pageId;
    if ($pageId <= 0 || isset($visited[$pageId]) || !$pageHandler || !$valueHandler || !$fieldHandler) {
        return;
    }
    $visited[$pageId] = true;
    
    // Alt sayfaları bul
    $criteria = new Criteria('parent_id', $pageId);
    $subPages = $pageHandler->getObjects($criteria) ?: array();
    foreach ($subPages as $subPage) {
        xpages_delete_page_data($subPage->getVar('page_id'), $visited);
        $pageHandler->delete($subPage);
    }
    
    // Alan değerlerini sil
    $valueHandler->deleteValuesForPage($pageId);
    
    // Sayfaya özel alan tanımlarını sil
    $criteria = new Criteria('page_id', $pageId);
    $fields = $fieldHandler->getObjects($criteria) ?: array();
    foreach ($fields as $field) {
        if ($field->getVar('field_type') === 'file' && !empty($field->getVar('field_default'))) {
            $safeFile = xpages_safe_filename($field->getVar('field_default', 'n'));
            $filePath = $safeFile !== '' ? XOOPS_UPLOAD_PATH . '/xpages/' . $safeFile : '';
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        $fieldHandler->delete($field);
    }
    
    // Galeri verilerini sil
    if ($galleryHandler) {
        $galleryHandler->deleteGalleryForPage($pageId);
    }
}


/**
 * XOOPS editörlerini kontrol et ve listele
 */
function xpages_get_available_editors() {
    $editors = [];
    $editorPath = XOOPS_ROOT_PATH . '/class/xoopseditor';
    
    if (is_dir($editorPath)) {
        $dirs = scandir($editorPath);
        foreach ($dirs as $dir) {
            if ($dir !== '.' && $dir !== '..' && is_dir($editorPath . '/' . $dir)) {
                $editors[] = $dir;
            }
        }
    }
    
    return $editors;
}

/**
 * Editör render et (alternatif yöntem)
 */
function xpages_render_editor($name, $value, $rows = 25, $cols = '100%') {
    $editorHtml = '';
    
    // XOOPS editör sistemini dene
    if (file_exists(XOOPS_ROOT_PATH . '/class/xoopseditor/xoopseditor.php')) {
        require_once XOOPS_ROOT_PATH . '/class/xoopseditor/xoopseditor.php';
        
        if (class_exists('XoopsEditorHandler')) {
            $editorHandler = XoopsEditorHandler::getInstance();
            $editors = $editorHandler->getList();
            
            // Önce TinyMCE dene
            if (isset($editors['tinymce']) || isset($editors['tinymce7'])) {
                $editorType = isset($editors['tinymce7']) ? 'tinymce7' : 'tinymce';
                $editor = $editorHandler->get($editorType, [
                    'name' => $name,
                    'value' => $value,
                    'rows' => $rows,
                    'cols' => $cols,
                    'width' => '100%',
                    'height' => '400px'
                ]);
                if ($editor && method_exists($editor, 'render')) {
                    $editorHtml = $editor->render();
                }
            }
            // Sonra CKEditor dene
            elseif (isset($editors['ckeditor'])) {
                $editor = $editorHandler->get('ckeditor', [
                    'name' => $name,
                    'value' => $value,
                    'rows' => $rows,
                    'cols' => $cols
                ]);
                if ($editor && method_exists($editor, 'render')) {
                    $editorHtml = $editor->render();
                }
            }
            // En son dhtml dene
            elseif (isset($editors['dhtml'])) {
                $editor = $editorHandler->get('dhtml', [
                    'name' => $name,
                    'value' => $value,
                    'rows' => $rows,
                    'cols' => $cols
                ]);
                if ($editor && method_exists($editor, 'render')) {
                    $editorHtml = $editor->render();
                }
            }
        }
    }
    
    // Hiçbir editör yoksa textarea döndür
    if (empty($editorHtml)) {
        $editorHtml = '<textarea name="' . htmlspecialchars($name, ENT_QUOTES) . '" id="' . htmlspecialchars($name, ENT_QUOTES) . '" rows="' . (int)$rows . '" class="xp-code-textarea">' . htmlspecialchars($value, ENT_QUOTES) . '</textarea>';
    }
    
    return $editorHtml;
}
