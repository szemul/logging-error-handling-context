<?php
declare(strict_types=1);

namespace Szemul\LoggingErrorHandlingContext\Test;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Szemul\LoggingErrorHandlingContext\ContextEntry;

class ContextEntryTest extends TestCase
{
    private const KEY   = 'key';
    private const VALUE = 'value';

    public function testConstructWithNoScopes_shouldSetAllScopes(): void
    {
        $key    = self::KEY;
        $value  = self::VALUE;
        $type   = ContextEntry::ERROR_HANDLER_TYPE_CONTEXT;
        $scopes = [ContextEntry::SCOPE_ERROR_HANDLER, ContextEntry::SCOPE_LOG];

        $entry = new ContextEntry($key, $value, $type);

        $this->assertEntryMatches(
            $entry,
            $key,
            $value,
            $type,
            $scopes,
        );
    }

    public function testConstructWithLogScope_shouldWorkNormally(): void
    {
        $key    = self::KEY;
        $value  = self::VALUE;
        $type   = null;
        $scopes = [ContextEntry::SCOPE_LOG];

        $entry = new ContextEntry($key, $value, $type, $scopes);

        $this->assertEntryMatches(
            $entry,
            $key,
            $value,
            $type,
            $scopes,
        );
    }

    public function testConstructWithErrorHandlerScopeAndType_shouldWorkNormally(): void
    {
        $key    = self::KEY;
        $value  = self::VALUE;
        $type   = ContextEntry::ERROR_HANDLER_TYPE_CONTEXT;
        $scopes = [ContextEntry::SCOPE_ERROR_HANDLER];

        $entry = new ContextEntry($key, $value, $type, $scopes);

        $this->assertEntryMatches(
            $entry,
            $key,
            $value,
            $type,
            $scopes,
        );
    }

    public function testConstructWithErrorHandlerScopeAndNoType_shouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $key    = self::KEY;
        $value  = self::VALUE;
        $type   = null;
        $scopes = [ContextEntry::SCOPE_ERROR_HANDLER];

        new ContextEntry($key, $value, $type, $scopes);
    }

    public function testConstructWithNoScopesAndNoType_shouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $key    = self::KEY;
        $value  = self::VALUE;
        $type   = null;

        new ContextEntry($key, $value, $type);
    }

    public function testConstructWithInvalidType_shouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $key    = self::KEY;
        $value  = self::VALUE;
        $type   = 'invalid';
        $scopes = [ContextEntry::SCOPE_ERROR_HANDLER];

        new ContextEntry($key, $value, $type, $scopes);
    }

    public function testConstructWithInvalidScope_shouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $key    = self::KEY;
        $value  = self::VALUE;
        $type   = ContextEntry::ERROR_HANDLER_TYPE_CONTEXT;
        $scopes = [ContextEntry::SCOPE_ERROR_HANDLER, 'invalid'];

        new ContextEntry($key, $value, $type, $scopes);
    }

    public function testGetTypeForScopeWithErrorHandlerForErrorHandler_shouldReturnType(): void
    {
        $entry = new ContextEntry(self::KEY, self::VALUE, ContextEntry::ERROR_HANDLER_TYPE_CONTEXT);

        $this->assertSame(
            ContextEntry::ERROR_HANDLER_TYPE_CONTEXT,
            $entry->getTypeForScope(ContextEntry::SCOPE_ERROR_HANDLER),
        );
    }

    public function testGetTypeForScopeWithoutErrorHandlerForErrorHandler_shouldReturnEmptyString(): void
    {
        $entry = new ContextEntry(
            self::KEY,
            self::VALUE,
            ContextEntry::ERROR_HANDLER_TYPE_CONTEXT,
            [ContextEntry::SCOPE_LOG],
        );

        $this->assertSame('', $entry->getTypeForScope(ContextEntry::SCOPE_ERROR_HANDLER));
    }

    public function testGetTypeForScopeForLog_shouldReturnEmptyString(): void
    {
        $entry = new ContextEntry(
            self::KEY,
            self::VALUE,
            null,
            [ContextEntry::SCOPE_LOG],
        );

        $this->assertSame('', $entry->getTypeForScope(ContextEntry::SCOPE_LOG));
    }

    /**
     * @param string[] $scopes
     */
    private function assertEntryMatches(ContextEntry $entry, string $key, mixed $value, ?string $type, array $scopes): void
    {
        $this->assertSame($key, $entry->getKey());
        $this->assertSame($value, $entry->getValue());
        $this->assertSame($type, $entry->getErrorHandlerType());
        $this->assertEquals($scopes, $entry->getScopes());
    }
}
