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

namespace Pixidos\GPWebPay\Param\Utils;

use Pixidos\GPWebPay\Data\Response;
use Pixidos\GPWebPay\Enum\Param;

final class DigestParamsFilter {
    /**
     * @var string[] DIGEST_PARAMS_KEYS
     */
    const DIGEST_PARAMS_KEYS = [
        Param::MERCHANTNUMBER,
        Param::OPERATION,
        Param::ORDERNUMBER,
        Param::AMOUNT,
        Param::CURRENCY,
        Param::DEPOSITFLAG,
        Param::MERORDERNUM,
        Param::RESPONSE_URL,
        Param::DESCRIPTION,
        Param::MD,
        Param::PAYMETHOD,
        Param::DISABLEPAYMETHOD,
        Param::PAYMETHODS,
        Param::EMAIL,
        Param::REFERENCENUMBER,
        Param::ADDINFO,
        Param::TOKEN,
        Param::FAST_TOKEN,
        Param::USERPARAM,
        Param::FASTPAYID,
        Response::RESULTTEXT,
        Response::SRCODE,
        Response::PRCODE,
        Response::EXPIRY,
        Response::ACSRES,
        Response::ACCODE,
        Response::DAYTOCAPTURE,
        Response::TOKENREGSTATUS,
    ];

    /**
     * @param array<string, string> $params
     *
     * @return array<string, string>
     */
    public static function filter(array $params) {
        return array_intersect_key($params, array_flip(self::DIGEST_PARAMS_KEYS));
    }
}
