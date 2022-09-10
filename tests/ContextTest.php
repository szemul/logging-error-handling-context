<?php
declare(strict_types=1);

namespace Szemul\LoggingErrorHandlingContext\Test;

use InvalidArgumentException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Szemul\LoggingErrorHandlingContext\Context;
use Szemul\LoggingErrorHandlingContext\ContextEntry;
use Szemul\LoggingErrorHandlingContext\Helper\ContextHelper;

class ContextTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private const TEST_VALUE          = 'testValue';
    private const TEST_KEY            = 'testKey';
    private const DEFAULT_KEY         = 'default';
    private const DEFAULT_VALUE       = 'defaultValue';
    private const LOG_KEY             = 'log';
    private const LOG_VALUE           = 'logValue';
    private const ERROR_HANDLER_KEY   = 'errorHandler';
    private const ERROR_HANDLER_VALUE = 'errorHandlerValue';
    private const TAG_KEY             = 'tagKey';
    private const TAG_VALUE           = 'tagValue';
    private const EXTRA_KEY           = 'extraKey';
    private const EXTRA_VALUE         = 'extraValue';
    private const USER_KEY            = 'userKey';
    private const USER_VALUE          = 'userValue';

    private ContextHelper | MockInterface | LegacyMockInterface $contextHelper;
    private Context                                             $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->contextHelper = Mockery::mock(ContextHelper::class);
        $this->context       = new Context($this->contextHelper); // @phpstan-ignore-line

        // @phpstan-ignore-next-line
        $this->contextHelper->shouldReceive('cleanupValueForScopeAndType')
            ->withAnyArgs()
            ->andReturnArg(2);
    }

    public function testAddValueToCurrentContext(): void
    {
        $this->assertEmpty($this->context->getLogValues());
        $this->context->addValues(
            new ContextEntry(self::TEST_KEY, self::TEST_VALUE, ContextEntry::ERROR_HANDLER_TYPE_CONTEXT),
        );
        $this->assertSame([self::TEST_KEY => self::TEST_VALUE], $this->context->getLogValues());
    }

    public function testContextHandling(): void
    {
        $expectedBase = [
            'base' => 'baseValue',
        ];

        $expectedFirst = [
            'base'  => 'baseValue',
            'first' => 'firstValue',
        ];

        $expectedSecond = [
            'base'   => 'overridden',
            'first'  => 'firstValue',
            'second' => 'secondValue',
        ];

        $this->context->addValues(new ContextEntry('base', 'baseValue', ContextEntry::ERROR_HANDLER_TYPE_CONTEXT));
        $firstContext = $this->context->addContext();
        $this->context->addValues(new ContextEntry('first', 'firstValue', ContextEntry::ERROR_HANDLER_TYPE_CONTEXT));
        $this->assertEquals($expectedFirst, $this->context->getLogValues());

        $this->context->addContext();
        $this->context->addValues(
            new ContextEntry('base', 'overridden', ContextEntry::ERROR_HANDLER_TYPE_CONTEXT),
            new ContextEntry('second', 'secondValue', ContextEntry::ERROR_HANDLER_TYPE_CONTEXT),
        );
        $this->assertEquals($expectedSecond, $this->context->getLogValues());

        $this->context->backToContext($firstContext);
        $this->assertEquals($expectedFirst, $this->context->getLogValues());

        $this->context->dropCurrentContext();
        $this->assertEquals($expectedBase, $this->context->getLogValues());
    }

    public function testBackToContextWithInvalidId_shouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->context->backToContext('invalid');
    }

    public function testDropContextInDefaultContext_shouldThrowException(): void
    {
        $this->expectException(RuntimeException::class);

        $this->context->dropCurrentContext();
    }

    public function testGetLogValues(): void
    {
        $expectedValues = [
            self::DEFAULT_KEY => self::DEFAULT_VALUE,
            self::LOG_KEY     => self::LOG_VALUE,
        ];

        $this->populateContextEntries();
        $this->assertEquals($expectedValues, $this->context->getLogValues());
    }

    public function testGetErrorHandlerContexts(): void
    {
        $expectedValues = [
            self::DEFAULT_KEY       => self::DEFAULT_VALUE,
            self::ERROR_HANDLER_KEY => self::ERROR_HANDLER_VALUE,
        ];

        $this->populateContextEntries();
        $this->assertEquals($expectedValues, $this->context->getErrorHandlerContexts());
    }

    public function testGetErrorHandlerTags(): void
    {
        $expectedValues = [self::TAG_KEY => self::TAG_VALUE];

        $this->populateContextEntries();
        $this->assertEquals($expectedValues, $this->context->getErrorHandlerTags());
    }

    public function testGetErrorHandlerExtras(): void
    {
        $expectedValues = [self::EXTRA_KEY => self::EXTRA_VALUE];

        $this->populateContextEntries();
        $this->assertEquals($expectedValues, $this->context->getErrorHandlerExtras());
    }

    public function testGetErrorHandlerUser(): void
    {
        $expectedValues = [self::USER_KEY => self::USER_VALUE];

        $this->populateContextEntries();
        $this->assertEquals($expectedValues, $this->context->getErrorHandlerUser());
    }

    private function populateContextEntries(): void
    {
        $this->context->addValues(
            new ContextEntry(self::DEFAULT_KEY, self::DEFAULT_VALUE, ContextEntry::ERROR_HANDLER_TYPE_CONTEXT),
            new ContextEntry(
                self::TAG_KEY,
                self::TAG_VALUE,
                ContextEntry::ERROR_HANDLER_TYPE_TAG,
                [ContextEntry::SCOPE_ERROR_HANDLER],
            ),
            new ContextEntry(
                self::EXTRA_KEY,
                self::EXTRA_VALUE,
                ContextEntry::ERROR_HANDLER_TYPE_EXTRA,
                [ContextEntry::SCOPE_ERROR_HANDLER],
            ),
            new ContextEntry(
                self::USER_KEY,
                self::USER_VALUE,
                ContextEntry::ERROR_HANDLER_TYPE_USER,
                [ContextEntry::SCOPE_ERROR_HANDLER],
            ),
            new ContextEntry(self::LOG_KEY, self::LOG_VALUE, null, [ContextEntry::SCOPE_LOG]),
            new ContextEntry(
                self::ERROR_HANDLER_KEY,
                self::ERROR_HANDLER_VALUE,
                ContextEntry::ERROR_HANDLER_TYPE_CONTEXT,
                [ContextEntry::SCOPE_ERROR_HANDLER],
            ),
        );
    }
}
