# Logging-error handling context

![CI pipeline](https://github.com/szemul/logging-error-handling-context/actions/workflows/php.yml/badge.svg)
[![codecov](https://codecov.io/gh/szemul/logging-error-handling-context/branch/main/graph/badge.svg?token=KZJ13OF577)](https://codecov.io/gh/szemul/logging-error-handling-context)

Provides an error handler and logging context to manage contextual data to be injected into errors and log messages.

## Context

The context is useful for storing values used to enrich logs and errors. The context class supports switching contexts.
When adding a new context, the existing values are preserved, and you can switch back to any previous context and
recover the state (switching back drops any changes in any newer context).
