<?php

declare(strict_types=1);

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
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'xpages_field_values', 'XpagesFieldvalue', 'value_id', 'field_value');
    }

    /**
     * Sayfaya ait tüm alan değerlerini getir
     *
     * @return array<int,string> field_id => field_value
     */
    public function getValuesForPage(int $pageId): array
    {
        $values   = [];
        $criteria = new Criteria('page_id', $pageId);
        $objects  = $this->getObjects($criteria);

        foreach ($objects ?: [] as $obj) {
            $values[(int)$obj->getVar('field_id')] = (string)$obj->getVar('field_value');
        }

        return $values;
    }

    /**
     * Sayfa için alan değerlerini kaydet
     */
    public function saveValuesForPage(int $pageId, array $values): bool
    {
        if ($pageId <= 0) {
            return false;
        }

        $fieldHandler = xpages_get_handler('field');
        $uploadDir = XOOPS_UPLOAD_PATH . '/xpages/';

        foreach ($values as $fieldId => $value) {
            $fieldId = (int)$fieldId;
            $field = $fieldHandler ? $fieldHandler->get($fieldId) : null;
            $fieldType = $field ? (string)$field->getVar('field_type', 'n') : '';
            $cleanValue = (string)$value;

            if ($fieldType === 'file') {
                $cleanValue = xpages_safe_filename($cleanValue);
            } elseif ($fieldType === 'url') {
                $cleanValue = xpages_normalize_url($cleanValue);
            } elseif ($fieldType === 'email') {
                $cleanValue = trim($cleanValue);
                $cleanValue = filter_var($cleanValue, FILTER_VALIDATE_EMAIL) ? $cleanValue : '';
            }

            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('page_id', $pageId));
            $criteria->add(new Criteria('field_id', $fieldId));

            $existing = $this->getObjects($criteria);
            $fieldValue = !empty($existing) ? $existing[0] : $this->create();
            $oldValue = !empty($existing) ? (string)$fieldValue->getVar('field_value', 'n') : '';

            if ($cleanValue !== '' || $cleanValue === '0') {
                if ($fieldType === 'file' && $oldValue !== '' && $oldValue !== $cleanValue) {
                    $oldFile = xpages_safe_filename($oldValue);
                    if ($oldFile !== '' && file_exists($uploadDir . $oldFile)) {
                        @unlink($uploadDir . $oldFile);
                    }
                }

                $fieldValue->setVar('page_id', $pageId);
                $fieldValue->setVar('field_id', $fieldId);
                $fieldValue->setVar('field_value', $cleanValue);
                $this->insert($fieldValue);
            } else {
                if ($fieldType === 'file' && $oldValue !== '') {
                    $oldFile = xpages_safe_filename($oldValue);
                    if ($oldFile !== '' && file_exists($uploadDir . $oldFile)) {
                        @unlink($uploadDir . $oldFile);
                    }
                }

                $this->deleteAll($criteria);
            }
        }

        return true;
    }
    
    /**
     * Sayfaya ait tüm alan değerlerini sil
     */
    public function deleteValuesForPage(int $pageId): bool
    {
        $criteria = new Criteria('page_id', $pageId);
        $values = $this->getObjects($criteria) ?: [];
        $fieldHandler = xpages_get_handler('field');
        $uploadDir = XOOPS_UPLOAD_PATH . '/xpages/';

        foreach ($values as $value) {
            $fieldId = (int)$value->getVar('field_id');
            $field = $fieldHandler ? $fieldHandler->get($fieldId) : null;
            if ($field && $field->getVar('field_type') === 'file' && !empty($value->getVar('field_value'))) {
                $oldFile = xpages_safe_filename($value->getVar('field_value', 'n'));
                if ($oldFile !== '' && file_exists($uploadDir . $oldFile)) {
                    @unlink($uploadDir . $oldFile);
                }
            }
        }

        return $this->deleteAll($criteria);
    }

    /**
     * Belirli bir alanın tüm değerlerini sil
     */
    public function deleteValuesForField(int $fieldId): bool
    {
        $criteria = new Criteria('field_id', (int)$fieldId);
        $values = $this->getObjects($criteria) ?: [];
        $fieldHandler = xpages_get_handler('field');
        $uploadDir = XOOPS_UPLOAD_PATH . '/xpages/';
        $field = $fieldHandler ? $fieldHandler->get((int)$fieldId) : null;

        foreach ($values as $value) {
            if ($field && $field->getVar('field_type') === 'file' && !empty($value->getVar('field_value'))) {
                $oldFile = xpages_safe_filename($value->getVar('field_value', 'n'));
                if ($oldFile !== '' && file_exists($uploadDir . $oldFile)) {
                    @unlink($uploadDir . $oldFile);
                }
            }
        }

        return $this->deleteAll($criteria);
    }
}
