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
 * @method static DepositFlag YES()
 * @method static DepositFlag NO()
 */
final class DepositFlag extends Enum {
    use AutoInstances;

    const YES = 1;
    const NO = 0;
}
