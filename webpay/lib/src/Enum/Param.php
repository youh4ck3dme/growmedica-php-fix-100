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
 * @method static Param MERCHANTNUMBER()
 * @method static Param OPERATION()
 * @method static Param ORDERNUMBER()
 * @method static Param AMOUNT()
 * @method static Param CURRENCY()
 * @method static Param DEPOSITFLAG()
 * @method static Param MERORDERNUM()
 * @method static Param RESPONSE_URL()
 * @method static Param DESCRIPTION()
 * @method static Param MD()
 * @method static Param USERPARAM()
 * @method static Param FASTPAYID()
 * @method static Param PAYMETHOD()
 * @method static Param DISABLEPAYMETHOD()
 * @method static Param PAYMETHODS()
 * @method static Param EMAIL()
 * @method static Param REFERENCENUMBER()
 * @method static Param ADDINFO()
 * @method static Param LANG()
 * @method static Param DIGEST()
 * @method static Param TOKEN()
 * @method static Param FAST_TOKEN()
 * @method static Param VRCODE()
 * @method static Param PANPATTERN()
 *
 */
final class Param extends Enum {
    use AutoInstances;

    const MERCHANTNUMBER = 'MERCHANTNUMBER';
    const OPERATION = 'OPERATION';
    const ORDERNUMBER = 'ORDERNUMBER';
    const AMOUNT = 'AMOUNT';
    const CURRENCY = 'CURRENCY';
    const DEPOSITFLAG = 'DEPOSITFLAG';
    const MERORDERNUM = 'MERORDERNUM';
    const RESPONSE_URL = 'URL';
    const DESCRIPTION = 'DESCRIPTION';
    const MD = 'MD';
    const USERPARAM = 'USERPARAM1';
    const FASTPAYID = 'FASTPAYID';
    const PAYMETHOD = 'PAYMETHOD';
    const DISABLEPAYMETHOD = 'DISABLEPAYMETHOD';
    const PAYMETHODS = 'PAYMETHODS';
    const EMAIL = 'EMAIL';
    const REFERENCENUMBER = 'REFERENCENUMBER';
    const ADDINFO = 'ADDINFO';
    const LANG = 'LANG';
    const DIGEST = 'DIGEST';
    const TOKEN = 'TOKEN';
    const FAST_TOKEN = 'FASTTOKEN';
    const VRCODE = 'VRCODE';
    const PANPATTERN = 'PANPATTERN';
}
