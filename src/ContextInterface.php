<?php
declare(strict_types=1);

namespace Szemul\LoggingErrorHandlingContext;

use InvalidArgumentException;

interface ContextInterface
{
    public function addContext(): string;

    /**
     * @throws InvalidArgumentException
     */
    public function backToContext(string $contextId): void;

    public function dropCurrentContext(): void;

    /**
     * @throws InvalidArgumentException
     */
    public function addValues(ContextEntryInterface ...$contextEntries): void;

    /**
     * @return array<string,mixed>
     */
    public function getValues(string $scope, string $type = ''): array;

    /**
     * @return array<string,mixed>
     */
    public function getErrorHandlerTags(): array;

    /**
     * @return array<string,mixed>
     */
    public function getErrorHandlerContexts(): array;

    /**
     * @return array<string,mixed>
     */
    public function getErrorHandlerExtras(): array;

    /**
     * @return array<string,mixed>
     */
    public function getErrorHandlerUser(): array;

    /**
     * @return array<string,mixed>
     */
    public function getLogValues(): array;
}
