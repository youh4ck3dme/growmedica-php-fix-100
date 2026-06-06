<?php

/**
 * Contains all exceptions used in project.
 *
 * - Usage exceptions: leads directly to fix by programmer. They are never caught and should never happen on production.
 * - Runtime exception: they represent valid case in domain logic. They should be handled at runtime and caught by user.
 *     Therefore every error should have separate exception type (they can create inheritance tree)
 */

namespace Pixidos\GPWebPay\EnumClass;

// Project root exceptions:
class UsageException extends \LogicException {
}

abstract class RuntimeException extends \RuntimeException {
}

final class MissingValueDeclarationException extends RuntimeException {
}

final class ReflectionFailedException extends UsageException {
    public function __construct(\ReflectionException $previous) {
        parent::__construct('PHP reflection failed.', 0, $previous);
    }
}
