<?php

declare(strict_types=1);

namespace XoopsModules\Xpages\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use XpagesField;
use XpagesFieldHandler;

require_once dirname(__DIR__, 2) . '/class/field.php';

/**
 * Coverage for XpagesFieldHandler's query-delegating methods.
 */
#[CoversClass(XpagesFieldHandler::class)]
final class FieldHandlerTest extends TestCase
{
    /**
     * fieldNameExists() builds a Criteria with the field_name + an
     * optional excludeId clause, and reports true when getCount > 0.
     */
    public function testFieldNameExistsReportsCollision(): void
    {
        $handler = $this->buildPartialHandler(['getCount']);
        $handler->expects($this->once())
            ->method('getCount')
            ->with($this->callback(function ($criteria) {
                self::assertInstanceOf(\CriteriaCompo::class, $criteria);
                self::assertCount(1, $criteria->criterias);
                $fieldNameCriterion = $criteria->criterias[0]['criterion'];
                self::assertSame('field_name', $fieldNameCriterion->column);
                self::assertSame('headline',   $fieldNameCriterion->value);
                return true;
            }))
            ->willReturn(1);

        self::assertTrue($handler->fieldNameExists('headline', 0, 0));
    }

    public function testFieldNameExistsReportsNoCollision(): void
    {
        $handler = $this->buildPartialHandler(['getCount']);
        $handler->method('getCount')->willReturn(0);

        self::assertFalse($handler->fieldNameExists('brand_new', 0, 0));
    }

    /**
     * Passing $excludeId > 0 should add a second criterion scoped to
     * `field_id != $excludeId` so the field being edited doesn't
     * collide with itself.
     */
    public function testFieldNameExistsSkipsCurrentFieldId(): void
    {
        $handler = $this->buildPartialHandler(['getCount']);
        $handler->expects($this->once())
            ->method('getCount')
            ->with($this->callback(function ($criteria) {
                self::assertInstanceOf(\CriteriaCompo::class, $criteria);
                self::assertCount(2, $criteria->criterias);
                $excludeCriterion = $criteria->criterias[1]['criterion'];
                self::assertSame('field_id', $excludeCriterion->column);
                self::assertSame(7,          $excludeCriterion->value);
                self::assertSame('!=',       $excludeCriterion->op);
                return true;
            }))
            ->willReturn(0);

        self::assertFalse($handler->fieldNameExists('same_name', 0, 7));
    }

    /**
     * getFieldsForPage() assembles:
     *   - a nested OR'd scope matching `page_id = $pageId OR page_id = 0`
     *   - optionally field_status = 1 when $onlyActive is true
     * and returns getObjects() verbatim.
     */
    public function testGetFieldsForPageReturnsHandlerResult(): void
    {
        $row1 = new XpagesField();
        $row1->setVar('field_id', 1);
        $row2 = new XpagesField();
        $row2->setVar('field_id', 2);

        $handler = $this->buildPartialHandler(['getObjects']);
        $handler->expects($this->once())
            ->method('getObjects')
            ->with($this->isInstanceOf(\CriteriaCompo::class))
            ->willReturn([$row1, $row2]);

        $result = $handler->getFieldsForPage(5);

        self::assertSame([$row1, $row2], $result);
    }

    /**
     * Returns an empty array (not false / not null) when getObjects
     * returns the legacy `false` sentinel the XOOPS base sometimes
     * emits. The handler's `?: []` guard ensures the array return
     * type is honoured.
     */
    public function testGetFieldsForPageNormalisesFalseReturn(): void
    {
        $handler = $this->buildPartialHandler(['getObjects']);
        $handler->method('getObjects')->willReturn(false);

        self::assertSame([], $handler->getFieldsForPage(5));
    }

    /**
     * getGlobalFields() is a thin wrapper around getFieldsForPage(0, ...).
     */
    public function testGetGlobalFieldsDelegatesToFieldsForPageZero(): void
    {
        $fld = new XpagesField();
        $fld->setVar('field_id', 99);

        $handler = $this->buildPartialHandler(['getObjects']);
        $handler->method('getObjects')->willReturn([$fld]);

        self::assertSame([$fld], $handler->getGlobalFields());
    }

    /**
     * @param list<string> $methodsToStub
     */
    private function buildPartialHandler(array $methodsToStub): XpagesFieldHandler
    {
        return $this->getMockBuilder(XpagesFieldHandler::class)
            ->disableOriginalConstructor()
            ->onlyMethods($methodsToStub)
            ->getMock();
    }
}
