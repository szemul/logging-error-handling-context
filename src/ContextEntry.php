<?php
declare(strict_types=1);

namespace Szemul\LoggingErrorHandlingContext;

use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;

class ContextEntry implements ContextEntryInterface
{
    /** @var string[] */
    protected array   $scopes;
    protected ?string $errorHandlerType;

    /**
     * @param string[] $scopes
     */
    public function __construct(
        protected string $key,
        protected mixed $value,
        ?string $errorHandlerType,
        array $scopes = [],
    ) {
        if (empty($errorHandlerType) && (empty($scopes) || in_array(self::SCOPE_ERROR_HANDLER, $scopes))) {
            throw new InvalidArgumentException('If the scopes include the error handler the error handler type is required');
        }

        $invalidScopes = array_diff($scopes, $this->getValidScopes());

        if (!empty($invalidScopes)) {
            throw new InvalidArgumentException('Invalid scopes received: ' . json_encode($invalidScopes));
        }

        if (null !== $errorHandlerType && !in_array($errorHandlerType, $this->getValidErrorHandlerTypes())) {
            throw new InvalidArgumentException('Invalid error handler type: ' . $errorHandlerType);
        }

        $this->scopes           = empty($scopes) ? $this->getValidScopes() : $scopes;
        $this->errorHandlerType = in_array(self::SCOPE_ERROR_HANDLER, $this->scopes) ? $errorHandlerType : null;
    }

    public function getValidScopes(): array
    {
        return [
            self::SCOPE_ERROR_HANDLER,
            self::SCOPE_LOG,
        ];
    }

    public function getValidErrorHandlerTypes(): array
    {
        return [
            self::ERROR_HANDLER_TYPE_CONTEXT,
            self::ERROR_HANDLER_TYPE_EXTRA,
            self::ERROR_HANDLER_TYPE_TAG,
            self::ERROR_HANDLER_TYPE_USER,
        ];
    }

    #[Pure]
    public function getTypeForScope(string $scope): string
    {
        return match ($scope) {
            self::SCOPE_ERROR_HANDLER => (string)$this->getErrorHandlerType(),
            default                   => '',
        };
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function getErrorHandlerType(): ?string
    {
        return $this->errorHandlerType;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
