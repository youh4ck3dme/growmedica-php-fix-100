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
use Pixidos\GPWebPay\Exceptions\InvalidArgumentException;
use function Pixidos\GPWebPay\assertMaxLenght;
use function Pixidos\GPWebPay\assertUrl;

class ResponseUrl implements IParam {
    /**
     * @var string
     */
    private $value;

    /**
     * ResponseUrl constructor.
     *
     * @param string $value
     *
     * @throws InvalidArgumentException
     */
    public function __construct($value) {
        $this->validate($value);
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getParamName() {
        return Param::RESPONSE_URL;
    }

    /**
     * @param string $value
     *
     * @throws InvalidArgumentException
     */
    protected function validate($value) {
        assertMaxLenght($value, 300, 'URL');
        assertUrl($value);
    }
}
