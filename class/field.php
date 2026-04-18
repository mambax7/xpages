<?php

declare(strict_types=1);

/**
 * xPages — Field value object.
 *
 * The handler lives in fieldhandler.php, which this file loads at the
 * bottom. See class/page.php for the rationale behind the require_once
 * pattern (XOOPS helper lookup only loads `class/{name}.php`).
 *
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

class XpagesField extends XoopsObject
{
    public function __construct()
    {
        $this->initVar('field_id',       XOBJ_DTYPE_INT,    null, false);
        $this->initVar('page_id',        XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('field_name',     XOBJ_DTYPE_TXTBOX, '',   false, 100);
        $this->initVar('field_label',    XOBJ_DTYPE_TXTBOX, '',   false, 255);
        $this->initVar('field_type',     XOBJ_DTYPE_TXTBOX, 'text', false, 50);
        $this->initVar('field_options',  XOBJ_DTYPE_TXTAREA, '',  false);
        $this->initVar('field_required', XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('field_order',    XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('field_status',   XOBJ_DTYPE_INT,    1,    false);
        $this->initVar('field_desc',     XOBJ_DTYPE_TXTBOX, '',   false, 500);
        $this->initVar('field_default',  XOBJ_DTYPE_TXTAREA, '',  false);
        $this->initVar('show_in_tpl',    XOBJ_DTYPE_INT,    1,    false);
    }

    /**
     * Alan tipi etiketlerini döndür
     *
     * @return array<string,string>
     */
    public static function getTypeLabels(): array
    {
        return [
            'text'     => defined('_AM_XPAGES_FIELD_TYPE_TEXT') ? '📝 ' . _AM_XPAGES_FIELD_TYPE_TEXT : '📝 Text',
            'textarea' => defined('_AM_XPAGES_FIELD_TYPE_TEXTAREA') ? '📄 ' . _AM_XPAGES_FIELD_TYPE_TEXTAREA : '📄 Text Area',
            'email'    => defined('_AM_XPAGES_FIELD_TYPE_EMAIL') ? '📧 ' . _AM_XPAGES_FIELD_TYPE_EMAIL : '📧 E-mail',
            'url'      => defined('_AM_XPAGES_FIELD_TYPE_URL') ? '🔗 ' . _AM_XPAGES_FIELD_TYPE_URL : '🔗 URL',
            'tel'      => defined('_AM_XPAGES_FIELD_TYPE_TEL') ? '📞 ' . _AM_XPAGES_FIELD_TYPE_TEL : '📞 Phone',
            'number'   => defined('_AM_XPAGES_FIELD_TYPE_NUMBER') ? '🔢 ' . _AM_XPAGES_FIELD_TYPE_NUMBER : '🔢 Number',
            'checkbox' => defined('_AM_XPAGES_FIELD_TYPE_CHECKBOX') ? '☑️ ' . _AM_XPAGES_FIELD_TYPE_CHECKBOX : '☑️ Checkbox',
            'radio'    => defined('_AM_XPAGES_FIELD_TYPE_RADIO') ? '🔘 ' . _AM_XPAGES_FIELD_TYPE_RADIO : '🔘 Radio Button',
            'select'   => defined('_AM_XPAGES_FIELD_TYPE_SELECT') ? '📋 ' . _AM_XPAGES_FIELD_TYPE_SELECT : '📋 Select Box',
            'file'     => defined('_AM_XPAGES_FIELD_TYPE_FILE_IMG') ? '📎 ' . _AM_XPAGES_FIELD_TYPE_FILE_IMG : '📎 File/Image',
        ];
    }
}

require_once __DIR__ . '/fieldhandler.php';
