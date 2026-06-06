<?php

/**
 * This file is part of the Pixidos package.
 *
 *  (c) Ondra Votava <ondra@votava.dev>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Pixidos\GPWebPay\Enum;

use Pixidos\GPWebPay\EnumClass\AutoInstances;
use Pixidos\GPWebPay\EnumClass\Enum;

/**
 * @method static Operation CREATE_ORDER()
 * @method static Operation CARD_VERIFICATION()
 * @method static Operation FINALIZE_ORDER()
 */
final class Operation extends Enum {
    use AutoInstances;

    const CREATE_ORDER = 'CREATE_ORDER';
    const CARD_VERIFICATION = 'CARD_VERIFICATION';
    const FINALIZE_ORDER = 'FINALIZE_ORDER';
}
