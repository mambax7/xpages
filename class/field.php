<?php

declare(strict_types=1);

/**
 * xPages — Field Class
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
     */
    public static function getTypeLabels() {
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

class XpagesFieldHandler extends XoopsPersistableObjectHandler
{
    public function __construct($db)
    {
        parent::__construct($db, 'xpages_fields', 'XpagesField', 'field_id', 'field_name');
    }

    /**
     * Sayfaya ait alanları getir (DÜZELTİLDİ - CriteriaCompo kullanıldı)
     */
    public function getFieldsForPage($pageId, $onlyActive = true) {
        $scope = new CriteriaCompo();
        $scope->add(new Criteria('page_id', (int)$pageId));
        $scope->add(new Criteria('page_id', 0), 'OR'); // Global alanlar için

        $criteria = new CriteriaCompo();
        $criteria->add($scope);
        
        if ($onlyActive) {
            $criteria->add(new Criteria('field_status', 1));
        }
        $criteria->setSort('field_order');
        $criteria->setOrder('ASC');
        
        return $this->getObjects($criteria);
    }
    
    /**
     * Global alanları getir
     */
    public function getGlobalFields($onlyActive = true) {
        return $this->getFieldsForPage(0, $onlyActive);
    }
    
    /**
     * Alan adının var olup olmadığını kontrol et (DÜZELTİLDİ)
     */
    public function fieldNameExists($fieldName, $pageId, $excludeId = 0) {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('field_name', $fieldName));
        if ($excludeId > 0) {
            $criteria->add(new Criteria('field_id', $excludeId, '!='));
        }
        return $this->getCount($criteria) > 0;
    }
}
