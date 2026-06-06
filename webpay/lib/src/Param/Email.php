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
use function Pixidos\GPWebPay\assertIsEmail;
use function Pixidos\GPWebPay\assertMaxLenght;

class Email implements IParam {
    /**
     * @var string
     */
    private $value;

    /**
     * Email constructor.
     *
     * @param string $value
     *
     * @throws InvalidArgumentException
     */
    public function __construct($value) {
        $value = trim($value);
        $this->validate($value);
        $this->value = $value;
    }

    public function getValue() {
        return $this->value;
    }

    public function __toString() {
        return $this->value;
    }

    public function getParamName() {
        return Param::EMAIL;
    }

    /**
     * @param string $value
     *
     * @throws InvalidArgumentException
     */
    protected function validate($value) {
        assertIsEmail($value);
        assertMaxLenght($value, 255, 'EMAIL');
    }
}
