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
use function Pixidos\GPWebPay\assertIsInteger;

class Amount implements IAmount {
    /**
     * @var int
     */
    private $amount;

    /**
     * @param float $amount
     * @param bool  $converToPennies
     *
     * @throws InvalidArgumentException
     * @deprecated use \Pixidos\GPWebPay\Param\AmountInPennies instead
     *             Amount constructor.
     *
     */
    public function __construct($amount, $converToPennies = true) {
        // prevod na halere/centy
        if ($converToPennies) {
            $amount *= 100;
        }

        assertIsInteger($amount, 'AMOUNT');

        $this->amount = (int) $amount;
    }


    /**
     * @return string
     */
    public function getParamName() {
        return Param::AMOUNT;
    }

    /**
     * @return int
     */
    public function getValue() {
        return $this->amount;
    }


    /**
     * @return string
     */
    public function __toString() {
        return (string) $this->amount;
    }
}
