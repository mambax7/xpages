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
                error_log("xPages: Handler class {$handlerClass} not found in {$handlerFile}");
                return null;
            }
        } else {
            error_log("xPages: Handler file not found: {$handlerFile}");
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
                $fileUrl = $uploadUrl . $value;
                $ext = strtolower(pathinfo($value, PATHINFO_EXTENSION));
                $html .= '<div class="xpages-current-file" style="margin-top:8px">';
                $html .= '<small><strong>' . _AM_XPAGES_FILE_CURRENT_LABEL . '</strong><br>';
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $html .= '<img src="' . $fileUrl . '" style="max-width:150px;max-height:150px;margin-top:5px;border-radius:4px;border:1px solid #dee2e6">';
                } else {
                    $html .= '<a href="' . $fileUrl . '" target="_blank">📎 ' . htmlspecialchars($value) . '</a>';
                }
                $html .= '<br><span style="color:#6c757d;font-size:11px">' . _AM_XPAGES_FILE_REPLACE_NOTE . '</span>';
                $html .= '</small></div>';
                $html .= '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars((string)$value, ENT_QUOTES) . '">';
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
    $fields = $fieldHandler->getFieldsForPage($pageId);
    $values = $valueHandler->getValuesForPage($pageId);
    
    $extraFields = array();
    foreach ($fields as $field) {
        $fid = $field->getVar('field_id');
        $fname = $field->getVar('field_name');
        $fieldType = $field->getVar('field_type');
        $fieldValue = $values[$fid] ?? $field->getVar('field_default');
        
        if ($fieldType === 'file' && !empty($fieldValue)) {
            $fieldValue = XOOPS_UPLOAD_URL . '/xpages/' . $fieldValue;
        }
        
        $extraFields[$fname] = array(
            'field_label'   => $field->getVar('field_label'),
            'field_type'    => $fieldType,
            'value'         => $fieldValue,
            'show_in_tpl'   => (int)$field->getVar('show_in_tpl'),
        );
    }
    
    // Galeri verilerini ekle
    $galleryData = array();
    if ($galleryHandler) {
        $gallery = $galleryHandler->getGalleryForPage($pageId);
        foreach ($gallery as $g) {
            $galleryData[] = array(
                'title'       => $g->getVar('title'),
                'description' => $g->getVar('description'),
                'image_url'   => $g->getImageUrl(),
            );
        }
    }
    
    $xoopsTpl->assign('xpages_page', array(
        'page_id'      => $page->getVar('page_id'),
        'title'        => $page->getVar('title'),
        'body'         => $page->getVar('body'),
        'short_desc'   => $page->getVar('short_desc'),
        'alias'        => $page->getVar('alias'),
        'update_date'  => $page->getVar('update_date'),
        'create_date'  => $page->getVar('create_date'),
        'hits'         => $page->getVar('hits'),
        'page_url'     => $page->getPageUrl(),
        'extra_fields' => $extraFields,
    ));
    
    $xoopsTpl->assign('xpages_gallery', $galleryData);
}

/**
 * Sayfa verilerini sil
 */
function xpages_delete_page_data($pageId) {
    $pageHandler  = xpages_get_handler('page');
    $valueHandler = xpages_get_handler('fieldvalue');
    $fieldHandler = xpages_get_handler('field');
    $galleryHandler = xpages_get_handler('gallery');
    
    // Alt sayfaları bul
    $criteria = new Criteria('parent_id', $pageId);
    $subPages = $pageHandler->getObjects($criteria);
    foreach ($subPages as $subPage) {
        xpages_delete_page_data($subPage->getVar('page_id'));
        $pageHandler->delete($subPage);
    }
    
    // Alan değerlerini sil
    $valueHandler->deleteValuesForPage($pageId);
    
    // Sayfaya özel alan tanımlarını sil
    $criteria = new Criteria('page_id', $pageId);
    $fields = $fieldHandler->getObjects($criteria);
    foreach ($fields as $field) {
        if ($field->getVar('field_type') === 'file' && !empty($field->getVar('field_default'))) {
            $filePath = XOOPS_UPLOAD_PATH . '/xpages/' . $field->getVar('field_default');
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