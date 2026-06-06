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

namespace Pixidos\GPWebPay\Param;

use Pixidos\GPWebPay\Enum\Param;

class AmountInPennies implements IAmount {
    /**
     * @var int
     */
    private $amount;

    public function __construct($amount) {
        $this->amount = $amount;
    }


    public function getParamName() {
        return Param::AMOUNT;
    }


    public function getValue() {
        return $this->amount;
    }


    public function __toString() {
        return (string) $this->amount;
    }
}
