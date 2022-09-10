<?php
declare(strict_types=1);

namespace Szemul\LoggingErrorHandlingContext;

use InvalidArgumentException;
use RuntimeException;
use Szemul\LoggingErrorHandlingContext\Helper\ContextHelper;

class Context implements ContextInterface
{
    protected string $currentContextId = '';
    /** @var array<string, array<string,array<string,array<string,mixed>>>> */
    protected array  $contexts;

    public function __construct(protected ContextHelper $contextHelper)
    {
        $this->contexts = [
            '' => [],
        ];
    }

    public function addContext(): string
    {
        do {
            $newContextId = uniqid((string)rand(1000000, 99999999), true);
        } while (array_key_exists($newContextId, $this->contexts));

        $this->contexts[$newContextId] = $this->contexts[$this->currentContextId] ?? [];
        $this->currentContextId        = $newContextId;

        return $newContextId;
    }

    public function backToContext(string $contextId): void
    {
        if (!array_key_exists($contextId, $this->contexts)) {
            throw new InvalidArgumentException('Invalid context ID: ' . $contextId);
        }

        foreach (array_reverse(array_keys($this->contexts)) as $currentContextId) {
            if ($contextId === $currentContextId) {
                break;
            }

            unset($this->contexts[$currentContextId]);
        }

        $this->currentContextId = $contextId;
    }

    public function dropCurrentContext(): void
    {
        if ('' === $this->currentContextId) {
            throw new RuntimeException('Can not drop the default context');
        }

        unset($this->contexts[$this->currentContextId]);

        $contextKeys            = array_keys($this->contexts);
        $this->currentContextId = end($contextKeys);
    }

    public function addValues(ContextEntryInterface ...$contextEntries): void
    {
        foreach ($contextEntries as $contextEntry) {
            foreach ($contextEntry->getScopes() as $scope) {
                $type  = $contextEntry->getTypeForScope($scope);
                $key   = $contextEntry->getKey();
                $value = $this->contextHelper->cleanupValueForScopeAndType($scope, $type, $contextEntry->getValue());

                $this->contexts[$this->currentContextId][$scope][$type][$key] = $value;
            }
        }
    }

    public function getValues(string $scope, string $type = ''): array
    {
        return $this->contexts[$this->currentContextId][$scope][$type] ?? [];
    }

    public function getErrorHandlerTags(): array
    {
        return $this->getValues(
            ContextEntryInterface::SCOPE_ERROR_HANDLER,
            ContextEntryInterface::ERROR_HANDLER_TYPE_TAG,
        );
    }

    public function getErrorHandlerContexts(): array
    {
        return $this->getValues(
            ContextEntryInterface::SCOPE_ERROR_HANDLER,
            ContextEntryInterface::ERROR_HANDLER_TYPE_CONTEXT,
        );
    }

    public function getErrorHandlerExtras(): array
    {
        return $this->getValues(
            ContextEntryInterface::SCOPE_ERROR_HANDLER,
            ContextEntryInterface::ERROR_HANDLER_TYPE_EXTRA,
        );
    }

    public function getErrorHandlerUser(): array
    {
        return $this->getValues(
            ContextEntryInterface::SCOPE_ERROR_HANDLER,
            ContextEntryInterface::ERROR_HANDLER_TYPE_USER,
        );
    }

    public function getLogValues(): array
    {
        return $this->getValues(ContextEntryInterface::SCOPE_LOG);
    }
}
