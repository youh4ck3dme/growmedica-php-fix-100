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

class PayMethods implements IParam {
    /**
     * @var PayMethodEnum[]
     */
    private $methods;
    /**
     * @var string
     */
    private $string;


    /**
     * PayMethods constructor.
     *
     * @param PayMethodEnum ...$methods
     */
    public function __construct(PayMethodEnum ...$methods) {
        $this->methods = array_unique($methods);
        $this->createString();
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->string;
    }

    /**
     * @return string
     */
    public function getParamName() {
        return Param::PAYMETHODS;
    }

    /**
     * @return PayMethodEnum[]
     */
    public function getValue() {
        return $this->methods;
    }

    /**
     * @param PayMethodEnum $method
     */
    public function addMethod($method) {
        $this->methods[] = $method;
        $this->methods = array_unique($this->methods);
        $this->createString();
    }

    private function createString() {
        $string = implode(',', $this->methods);
        $this->string = $string;
    }
}
