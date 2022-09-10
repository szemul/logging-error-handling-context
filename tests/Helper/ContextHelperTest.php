<?php
declare(strict_types=1);

namespace Szemul\LoggingErrorHandlingContext\Test\Helper;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Szemul\LoggingErrorHandlingContext\ContextEntry;
use Szemul\LoggingErrorHandlingContext\Helper\ContextHelper;

class ContextHelperTest extends TestCase
{
    protected ContextHelper $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new ContextHelper();
    }

    public function testCleanUpValueForErrorHandlerTagWithScalarTag_shouldReturnAsString(): void
    {
        $this->assertSame(
            '5',
            $this->sut->cleanupValueForScopeAndType(
                ContextEntry::SCOPE_ERROR_HANDLER,
                ContextEntry::ERROR_HANDLER_TYPE_TAG,
                5,
            ),
        );
    }

    public function testCleanUpValueForErrorHandlerContextWithAssociativeArrayContext_shouldReturnTheArray(): void
    {
        $context = [
            'foo' => 'bar',
        ];

        $this->assertSame(
            $context,
            $this->sut->cleanupValueForScopeAndType(
                ContextEntry::SCOPE_ERROR_HANDLER,
                ContextEntry::ERROR_HANDLER_TYPE_CONTEXT,
                $context,
            ),
        );
    }

    public function testCleanUpValueForErrorHandlerCustomTypeWithCustomValue_shouldReturnTheValue(): void
    {
        $this->assertSame(6, $this->sut->cleanupValueForScopeAndType(ContextEntry::SCOPE_ERROR_HANDLER, 'custom', 6));
    }

    public function testCleanUpValueForCustomScopeWithCustomValue_shouldReturnTheValue(): void
    {
        $this->assertSame(6, $this->sut->cleanupValueForScopeAndType('custom', '', 6));
    }

    public function testCleanUpValueForTypeWithArrayTag_shouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->sut->cleanupValueForScopeAndType(
            ContextEntry::SCOPE_ERROR_HANDLER,
            ContextEntry::ERROR_HANDLER_TYPE_TAG,
            ['foo' => 'bar'],
        );
    }

    public function testCleanUpValueForTypeWithNumericArrayContext_shouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->sut->cleanupValueForScopeAndType(
            ContextEntry::SCOPE_ERROR_HANDLER,
            ContextEntry::ERROR_HANDLER_TYPE_CONTEXT,
            ['foo', 'bar'],
        );
    }

    public function testCleanUpValueForTypeWithEmptyArrayContext_shouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->sut->cleanupValueForScopeAndType(
            ContextEntry::SCOPE_ERROR_HANDLER,
            ContextEntry::ERROR_HANDLER_TYPE_CONTEXT,
            [],
        );
    }

    public function testCleanUpValueForTypeWithStringContext_shouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->sut->cleanupValueForScopeAndType(
            ContextEntry::SCOPE_ERROR_HANDLER,
            ContextEntry::ERROR_HANDLER_TYPE_CONTEXT,
            'test',
        );
    }
}
