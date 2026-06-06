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
use Pixidos\GPWebPay\Enum\PayMethod as PayMethodEnum;

class PayMethod implements IParam {
    /**
     * @var PayMethodEnum
     */
    private $value;

    /**
     * PayMethod constructor.
     *
     * @param PayMethodEnum $method
     */
    public function __construct(PayMethodEnum $method) {
        $this->value = $method;
    }

    public function __toString() {
        return (string) $this->value;
    }


    public function getParamName() {
        return Param::PAYMETHOD;
    }

    /**
     * @return PayMethodEnum
     */
    public function getValue() {
        return $this->value;
    }
}
