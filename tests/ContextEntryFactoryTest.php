<?php
declare(strict_types=1);

namespace Szemul\LoggingErrorHandlingContext\Test;

use PHPUnit\Framework\TestCase;
use Szemul\LoggingErrorHandlingContext\ContextEntry;
use Szemul\LoggingErrorHandlingContext\ContextEntryFactory;

class ContextEntryFactoryTest extends TestCase
{
    public function testCreateContextEntries(): void
    {
        $values = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $factory = new ContextEntryFactory();

        $results = $factory->createContextEntries(
            $values,
            ContextEntry::ERROR_HANDLER_TYPE_TAG,
            [ContextEntry::SCOPE_ERROR_HANDLER],
        );

        $this->assertCount(2, $results);
        $this->assertContainsOnlyInstancesOf(ContextEntry::class, $results);

        $this->assertSame([ContextEntry::SCOPE_ERROR_HANDLER], $results[0]->getScopes());
        $this->assertSame(ContextEntry::ERROR_HANDLER_TYPE_TAG, $results[0]->getErrorHandlerType());
        $this->assertSame('key1', $results[0]->getKey());
        $this->assertSame($values['key1'], $results[0]->getValue());

        $this->assertSame([ContextEntry::SCOPE_ERROR_HANDLER], $results[0]->getScopes());
        $this->assertSame(ContextEntry::ERROR_HANDLER_TYPE_TAG, $results[0]->getErrorHandlerType());
        $this->assertSame('key2', $results[1]->getKey());
        $this->assertSame($values['key2'], $results[1]->getValue());
    }
}
