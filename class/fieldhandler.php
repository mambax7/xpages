<?php

declare(strict_types=1);

/**
 * xPages — Field handler.
 *
 * Companion to class/field.php. See class/pagehandler.php for the file-
 * layout rationale.
 *
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

class XpagesFieldHandler extends XoopsPersistableObjectHandler
{
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'xpages_fields', 'XpagesField', 'field_id', 'field_name');
    }

    /**
     * Sayfaya ait alanları getir
     *
     * @return XpagesField[]
     */
    public function getFieldsForPage(int $pageId, bool $onlyActive = true): array
    {
        $scope = new CriteriaCompo();
        $scope->add(new Criteria('page_id', $pageId));
        $scope->add(new Criteria('page_id', 0), 'OR'); // Global alanlar için

        $criteria = new CriteriaCompo();
        $criteria->add($scope);

        if ($onlyActive) {
            $criteria->add(new Criteria('field_status', 1));
        }
        $criteria->setSort('field_order');
        $criteria->setOrder('ASC');

        return $this->getObjects($criteria) ?: [];
    }

    /**
     * Global alanları getir
     *
     * @return XpagesField[]
     */
    public function getGlobalFields(bool $onlyActive = true): array
    {
        return $this->getFieldsForPage(0, $onlyActive);
    }

    /**
     * Alan adının var olup olmadığını kontrol et
     */
    public function fieldNameExists(string $fieldName, int $pageId, int $excludeId = 0): bool
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('field_name', $fieldName));
        if ($excludeId > 0) {
            $criteria->add(new Criteria('field_id', $excludeId, '!='));
        }
        return $this->getCount($criteria) > 0;
    }
}
