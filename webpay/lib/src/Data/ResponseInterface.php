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

namespace Pixidos\GPWebPay\Data;

use Pixidos\GPWebPay\Param\IParam;

interface ResponseInterface {
    const PRCODE = 'PRCODE';
    const SRCODE = 'SRCODE';
    const RESULTTEXT = 'RESULTTEXT';
    const EXPIRY = 'EXPIRY';
    const ACSRES = 'ACSRES';
    const ACCODE = 'ACCODE';
    const DAYTOCAPTURE = 'DAYTOCAPTURE';
    const TOKENREGSTATUS = 'TOKENREGSTATUS';
    const DIGEST1 = 'DIGEST1';

    const RESPONSE_PARAMS = [
        self::EXPIRY,
        self::ACCODE,
        self::ACSRES,
        self::DAYTOCAPTURE,
        self::TOKENREGSTATUS,
    ];

    /**
     * @return IParam[]
     */
    public function getParams();

    /**
     * @return string
     */
    public function getDigest();

    /**
     * @return bool
     */
    public function hasError();

    /**
     * @return string
     */
    public function getDigest1();

    /**
     * @return string|null
     */
    public function getMerOrderNumber();

    /**
     * @return string|null
     */
    public function getMd();

    /**
     * @return string
     */
    public function getGatewayKey();

    /**
     * @return string
     */
    public function getOrderNumber();

    /**
     * @return int
     */
    public function getSrcode();

    /**
     * @return int
     */
    public function getPrcode();

    /**
     * @return string
     */
    public function getResultText();

    /**
     * @return string|null
     */
    public function getUserParam1();

    /**
     * Sorting params order by documentation
     */
    public function sortParams();
}
