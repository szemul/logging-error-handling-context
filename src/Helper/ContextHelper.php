<?php
declare(strict_types=1);

namespace Szemul\LoggingErrorHandlingContext\Helper;

use InvalidArgumentException;
use Szemul\LoggingErrorHandlingContext\ContextEntryInterface;

class ContextHelper
{
    /**
     * @throws InvalidArgumentException
     */
    public function cleanupValueForScopeAndType(string $scope, string $type, mixed $value): mixed
    {
        return match ($scope) {
            ContextEntryInterface::SCOPE_ERROR_HANDLER => $this->cleanUpValueForErrorHandler($type, $value),
            default                                    => $value,
        };
    }

    protected function cleanUpValueForErrorHandler(string $type, mixed $value): mixed
    {
        switch ($type) {
            case ContextEntryInterface::ERROR_HANDLER_TYPE_TAG:
                if (!is_scalar($value)) {
                    throw new InvalidArgumentException('Tag type values must be strings');
                }

                $value = (string)$value;
                break;

            case ContextEntryInterface::ERROR_HANDLER_TYPE_CONTEXT:
                if (!is_array($value) || empty($value)) {
                    throw new InvalidArgumentException('Context type values must be non empty associative arrays');
                }

                $nonNumericArrayKeys = array_filter(
                    array_keys($value),
                    function ($currentValue) {
                        return !is_numeric($currentValue);
                    },
                );

                if (empty($nonNumericArrayKeys)) {
                    throw new InvalidArgumentException('Context type values must be non empty associative arrays');
                }

                break;
        }

        return $value;
    }
}
