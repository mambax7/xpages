<?php
/**
 * xPages — Genel yardımcı fonksiyonlar
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * Handler yükleme fonksiyonu
 */
function xpages_get_handler($name) {
    static $handlers = array();
    
    if (!isset($handlers[$name])) {
        $handlerClass = 'Xpages' . ucfirst($name) . 'Handler';
        $handlerFile = XOOPS_ROOT_PATH . '/modules/xpages/class/' . strtolower($name) . '.php';
        
        if (file_exists($handlerFile)) {
            require_once $handlerFile;
            
            if (class_exists($handlerClass)) {
                $handlers[$name] = new $handlerClass($GLOBALS['xoopsDB']);
            } else {
                trigger_error("xPages: Handler class {$handlerClass} not found in {$handlerFile}", E_USER_WARNING);
                return null;
            }
        } else {
            trigger_error("xPages: Handler file not found: {$handlerFile}", E_USER_WARNING);
            return null;
        }
    }
    
    return $handlers[$name];
}

/**
 * Admin boot - dil dosyalarını yükle
 */
function xpages_admin_boot() {
    xpages_load_language('admin');
    xpages_load_language('modinfo');
}

/**
 * Dil dosyası yükleme
 */
function xpages_load_language($type = 'main') {
    global $xoopsConfig;
    
    $lang = $xoopsConfig['language'];
    $file = XOOPS_ROOT_PATH . '/modules/xpages/language/' . $lang . '/' . $type . '.php';
    
    if (!file_exists($file)) {
        $file = XOOPS_ROOT_PATH . '/modules/xpages/language/english/' . $type . '.php';
    }
    
    if (file_exists($file)) {
        include_once $file;
    }
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

    if (strpos($url, '//') === 0) {
        return '';
    }

    $colonPos     = strpos($url, ':');
    $delimiterPos  = strcspn($url, '/?#');
    $hasScheme     = $colonPos !== false && ($delimiterPos === strlen($url) || $colonPos < $delimiterPos);

    if ($hasScheme) {
        $scheme = strtolower(substr($url, 0, $colonPos));
        $allowedSchemes = ['http', 'https', 'ftp', 'mailto'];
        if (!in_array($scheme, $allowedSchemes, true)) {
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
 * İlave alan input render fonksiyonu (RADIO/SELECT DÜZELTİLDİ)
 */
function xpages_render_field_input($field, $value = '') {
    if (!$field) return '';
    
    $fid = (int)$field->getVar('field_id');
    $name = 'extra_fields[' . $fid . ']';
    $label = htmlspecialchars((string)$field->getVar('field_label'), ENT_QUOTES);
    $type = $field->getVar('field_type');
    $required = (int)$field->getVar('field_required') ? ' required' : '';
    $desc = htmlspecialchars((string)$field->getVar('field_desc'), ENT_QUOTES);
    $options = $field->getVar('field_options');
    
    // RADIO ve SELECT için options değerini temizle (HTML etiketlerini newline'e çevir)
    if ($type === 'radio' || $type === 'select') {
        $options = html_entity_decode($options, ENT_QUOTES, 'UTF-8');
        $options = preg_replace('/<br\s*\/?>/i', "\n", $options);
        $options = str_replace("\r\n", "\n", $options);
        $options = str_replace("\r", "\n", $options);
    }
    
    $html = '<div class="xpages-field" id="field-' . $fid . '">';
    $html .= '<label for="extra_field_' . $fid . '">' . $label . ($required ? ' <span class="req">*</span>' : '') . '</label>';
    
    switch ($type) {
        case 'text':
        case 'email':
        case 'url':
        case 'tel':
            $html .= '<input type="' . $type . '" name="' . $name . '" id="extra_field_' . $fid . '" value="' . htmlspecialchars((string)$value, ENT_QUOTES) . '"' . $required . '>';
            break;
            
        case 'textarea':
            $html .= '<textarea name="' . $name . '" id="extra_field_' . $fid . '" rows="5"' . $required . '>' . htmlspecialchars((string)$value, ENT_QUOTES) . '</textarea>';
            break;
            
        case 'number':
            $html .= '<input type="number" name="' . $name . '" id="extra_field_' . $fid . '" value="' . htmlspecialchars((string)$value, ENT_QUOTES) . '"' . $required . '>';
            break;
            
        case 'checkbox':
            $html .= '<input type="hidden" name="' . $name . '" value="0">';
            $html .= '<input type="checkbox" name="' . $name . '" id="extra_field_' . $fid . '" value="1"' . ((int)$value === 1 ? ' checked' : '') . $required . '>';
            break;
            
        case 'select':
            $html .= '<select name="' . $name . '" id="extra_field_' . $fid . '"' . $required . '>';
            $html .= '<option value="">' . _AM_XPAGES_SELECT_PLACEHOLDER . '</option>';
            if (!empty($options)) {
                $optLines = explode("\n", trim($options));
                foreach ($optLines as $opt) {
                    $opt = trim($opt);
                    if ($opt === '') continue;
                    $selected = ($opt == $value) ? ' selected' : '';
                    $html .= '<option value="' . htmlspecialchars($opt, ENT_QUOTES) . '"' . $selected . '>' . htmlspecialchars($opt, ENT_QUOTES) . '</option>';
                }
            }
            $html .= '</select>';
            break;
            
        case 'radio':
            $html .= '<div class="xpages-radio-group">';
            if (!empty($options)) {
                $optLines = explode("\n", trim($options));
                $i = 0;
                foreach ($optLines as $opt) {
                    $opt = trim($opt);
                    if ($opt === '') continue;
                    $checked = ($opt == $value) ? ' checked' : '';
                    $radioId = 'extra_field_' . $fid . '_' . $i;
                    $html .= '<label for="' . $radioId . '" style="display:inline-block;margin-right:15px;font-weight:normal">';
                    $html .= '<input type="radio" name="' . $name . '" id="' . $radioId . '" value="' . htmlspecialchars($opt, ENT_QUOTES) . '"' . $checked . $required . '> ' . htmlspecialchars($opt, ENT_QUOTES);
                    $html .= '</label>';
                    $i++;
                }
            }
            $html .= '</div>';
            break;
            
        case 'file':
            $html .= '<div class="xpages-file-field">';
            $html .= '<input type="file" name="extra_files[' . $fid . ']" id="extra_file_' . $fid . '" accept="image/*,application/pdf,.doc,.docx,.zip">';
            
            if (!empty($value)) {
                $uploadUrl = XOOPS_UPLOAD_URL . '/xpages/';
                $safeValue = xpages_safe_filename((string)$value);
                $fileUrl = $safeValue !== '' ? $uploadUrl . rawurlencode($safeValue) : '';
                $ext = strtolower(pathinfo($safeValue, PATHINFO_EXTENSION));
                $html .= '<div class="xpages-current-file" style="margin-top:8px">';
                $html .= '<small><strong>' . _AM_XPAGES_FILE_CURRENT_LABEL . '</strong><br>';
                if ($fileUrl && in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                    $html .= '<img src="' . $fileUrl . '" style="max-width:150px;max-height:150px;margin-top:5px;border-radius:4px;border:1px solid #dee2e6">';
                } elseif ($fileUrl) {
                    $html .= '<a href="' . $fileUrl . '" target="_blank">📎 ' . htmlspecialchars($value) . '</a>';
                }
                $html .= '<br><span style="color:#6c757d;font-size:11px">' . _AM_XPAGES_FILE_REPLACE_NOTE . '</span>';
                $html .= '</small></div>';
                $html .= '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($safeValue, ENT_QUOTES) . '">';
            } else {
                $html .= '<small class="xpf-desc" style="display:block;margin-top:5px">' . _AM_XPAGES_FILE_NONE . '</small>';
            }
            $html .= '</div>';
            break;
            
        default:
            $html .= '<input type="text" name="' . $name . '" id="extra_field_' . $fid . '" value="' . htmlspecialchars((string)$value, ENT_QUOTES) . '"' . $required . '>';
    }
    
    if ($desc) {
        $html .= '<small class="xpf-desc">' . $desc . '</small>';
    }
    
    $html .= '</div>';
    
    return $html;
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
