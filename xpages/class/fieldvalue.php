<?php
/**
 * xPages — Field Value Class
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

class XpagesFieldvalue extends XoopsObject
{
    public function __construct()
    {
        $this->initVar('value_id',    XOBJ_DTYPE_INT,    null, false);
        $this->initVar('page_id',     XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('field_id',    XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('field_value', XOBJ_DTYPE_TXTAREA, '',   false);
    }
}

class XpagesFieldvalueHandler extends XoopsPersistableObjectHandler
{
    public function __construct($db)
    {
        parent::__construct($db, 'xpages_field_values', 'XpagesFieldvalue', 'value_id', 'field_value');
    }
    
    /**
     * Toplam kayıt sayısını döndür
     */
    public function getCount($criteria = null) {
        return parent::getCount($criteria);
    }
    
    /**
     * Sayfaya ait tüm alan değerlerini getir
     */
    public function getValuesForPage($pageId) {
        $values = [];
        $criteria = new Criteria('page_id', $pageId);
        $objects = $this->getObjects($criteria);
        
        foreach ($objects as $obj) {
            $values[(int)$obj->getVar('field_id')] = $obj->getVar('field_value');
        }
        
        return $values;
    }
    

/**
 * Sayfa için alan değerlerini kaydet (TAMAMEN YENİLENDİ)
 */
public function saveValuesForPage($pageId, $values) {
    if (!$pageId || !is_array($values)) return false;
    
    foreach ($values as $fieldId => $value) {
        // Boş değilse veya 0 ise (checkbox için) kaydet
        if ($value !== '' || $value === '0') {
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('page_id', $pageId));
            $criteria->add(new Criteria('field_id', $fieldId));
            
            $existing = $this->getObjects($criteria);
            
            if (!empty($existing)) {
                $fieldValue = $existing[0];
            } else {
                $fieldValue = $this->create();
                $fieldValue->setVar('page_id', $pageId);
                $fieldValue->setVar('field_id', $fieldId);
            }
            
            $fieldValue->setVar('field_value', $value);
            $this->insert($fieldValue);
        } else {
            // Değer boşsa ve kayıt varsa sil
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('page_id', $pageId));
            $criteria->add(new Criteria('field_id', $fieldId));
            $this->deleteAll($criteria);
        }
    }
    
    return true;
}
    
    /**
     * Sayfaya ait tüm alan değerlerini sil
     */
    public function deleteValuesForPage($pageId) {
        $criteria = new Criteria('page_id', $pageId);
        return $this->deleteAll($criteria);
    }
}