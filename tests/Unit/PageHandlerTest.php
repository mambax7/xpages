<?php

declare(strict_types=1);

namespace XoopsModules\Xpages\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use XpagesPage;
use XpagesPageHandler;

require_once dirname(__DIR__, 2) . '/class/page.php';

/**
 * Coverage for XpagesPageHandler's query-delegating methods.
 *
 * We partial-mock the handler so its inherited getObjects() / getCount()
 * calls return fixtures we control. The production methods under test
 * stay real; only the base-class delegates get swapped out.
 */
#[CoversClass(XpagesPageHandler::class)]
final class PageHandlerTest extends TestCase
{
    /**
     * getByAlias() should build a `Criteria('alias', ...)`, hand it to
     * getObjects(), and return the first object when there's a match.
     */
    public function testGetByAliasReturnsFirstMatch(): void
    {
        $page = new XpagesPage();
        $page->setVar('page_id', 42);
        $page->setVar('alias', 'about-us');

        $handler = $this->buildPartialHandler(['getObjects']);
        $handler->expects($this->once())
            ->method('getObjects')
            ->willReturnCallback(function ($criteria) use ($page) {
                // Single-column Criteria, not CriteriaCompo.
                self::assertInstanceOf(\Criteria::class, $criteria);
                self::assertNotInstanceOf(\CriteriaCompo::class, $criteria);
                self::assertSame('alias', $criteria->column);
                self::assertSame('about-us', $criteria->value);
                return [$page];
            });

        $result = $handler->getByAlias('about-us');

        self::assertSame($page, $result);
    }

    /**
     * No match → null (not empty array, not false).
     */
    public function testGetByAliasReturnsNullWhenMissing(): void
    {
        $handler = $this->buildPartialHandler(['getObjects']);
        $handler->method('getObjects')->willReturn([]);

        self::assertNull($handler->getByAlias('nonexistent'));
    }

    /**
     * generateAlias() — happy path: title → clean slug when no
     * collision exists.
     */
    public function testGenerateAliasSlugifiesTitle(): void
    {
        $handler = $this->buildPartialHandler(['getCount']);
        // aliasExists() calls getCount; first lookup reports 0 → unique.
        $handler->method('getCount')->willReturn(0);

        self::assertSame('hello-world', $handler->generateAlias('Hello World'));
        self::assertSame('about-us',    $handler->generateAlias('About Us!'));
    }

    /**
     * generateAlias() collision flow: when aliasExists() returns true
     * on the first check, loop should append '-1'. Second probe with
     * the -1 variant returns 0 → the function exits.
     */
    public function testGenerateAliasAppendsCounterOnCollision(): void
    {
        $handler = $this->buildPartialHandler(['getCount']);

        // First probe (for 'hello-world'): collides → 1
        // Second probe (for 'hello-world-1'): unique → 0
        $handler->method('getCount')
            ->willReturnOnConsecutiveCalls(1, 0);

        self::assertSame('hello-world-1', $handler->generateAlias('Hello World'));
    }

    /**
     * Empty / all-punctuation titles collapse to 'page' so the
     * returned alias is never empty.
     */
    public function testGenerateAliasFallsBackForEmptyInput(): void
    {
        $handler = $this->buildPartialHandler(['getCount']);
        $handler->method('getCount')->willReturn(0);

        self::assertSame('page', $handler->generateAlias(''));
        self::assertSame('page', $handler->generateAlias('---'));
        self::assertSame('page', $handler->generateAlias('!!!'));
    }

    /**
     * generateAlias() also slugifies Turkish and other UTF-8 input:
     * mb_strtolower + alphanum/-hyphen allowlist collapses accents to
     * `-` runs, then trim+dedup hyphens.
     */
    public function testGenerateAliasHandlesUtf8(): void
    {
        $handler = $this->buildPartialHandler(['getCount']);
        $handler->method('getCount')->willReturn(0);

        // "Türkçe" → "t-rk-e" (Turkish chars become separators, then
        // -+ dedup and trim).
        self::assertSame('t-rk-e', $handler->generateAlias('Türkçe'));
        // Mixed punctuation normalises.
        self::assertSame('some-title-1-2', $handler->generateAlias('Some Title (1.2)'));
    }

    /**
     * Build a partial mock that skips the parent constructor and only
     * stubs the base-class query delegates the caller asks for.
     *
     * @param list<string> $methodsToStub method names to override
     */
    private function buildPartialHandler(array $methodsToStub): XpagesPageHandler
    {
        return $this->getMockBuilder(XpagesPageHandler::class)
            ->disableOriginalConstructor()
            ->onlyMethods($methodsToStub)
            ->getMock();
    }
}
