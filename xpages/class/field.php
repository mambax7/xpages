<?php
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
            'text'     => '📝 Metin',
            'textarea' => '📄 Metin Alanı',
            'email'    => '📧 E-posta',
            'url'      => '🔗 URL',
            'tel'      => '📞 Telefon',
            'number'   => '🔢 Sayı',
            'checkbox' => '☑️ Onay Kutusu',
            'radio'    => '🔘 Radyo Butonu',
            'select'   => '📋 Seçim Kutusu',
            'file'     => '📎 Dosya/Resim',
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
     * Toplam kayıt sayısını döndür
     */
    public function getCount($criteria = null) {
        return parent::getCount($criteria);
    }
    
    /**
     * Sayfaya ait alanları getir (DÜZELTİLDİ - CriteriaCompo kullanıldı)
     */
    public function getFieldsForPage($pageId, $onlyActive = true) {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('page_id', $pageId));
        $criteria->add(new Criteria('page_id', 0), 'OR'); // Global alanlar için
        
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
        $criteria->add(new Criteria('page_id', $pageId));
        if ($excludeId > 0) {
            $criteria->add(new Criteria('field_id', $excludeId, '!='));
        }
        return $this->getCount($criteria) > 0;
    }
}