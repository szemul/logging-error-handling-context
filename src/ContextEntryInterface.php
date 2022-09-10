<?php
declare(strict_types=1);

namespace Szemul\LoggingErrorHandlingContext;

interface ContextEntryInterface
{
    public const ERROR_HANDLER_TYPE_USER    = 'user';
    public const ERROR_HANDLER_TYPE_TAG     = 'tag';
    public const ERROR_HANDLER_TYPE_CONTEXT = 'context';
    public const ERROR_HANDLER_TYPE_EXTRA   = 'extra';

    public const SCOPE_LOG           = 'log';
    public const SCOPE_ERROR_HANDLER = 'errorHandler';

    /**
     * @return string[]
     */
    public function getValidScopes(): array;

    /**
     * @return string[]
     */
    public function getValidErrorHandlerTypes(): array;

    /**
     * @return string[]
     */
    public function getScopes(): array;

    public function getErrorHandlerType(): ?string;

    public function getKey(): string;

    public function getValue(): mixed;

    public function getTypeForScope(string $scope): string;
}
