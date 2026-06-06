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

use Pixidos\GPWebPay\Enum\Operation as EnumOperation;
use Pixidos\GPWebPay\Enum\Param;
use Pixidos\GPWebPay\Param\IParam;
use Pixidos\GPWebPay\Param\Md;
use Pixidos\GPWebPay\Param\MerOrderNum;
use Pixidos\GPWebPay\Param\Operation;
use Pixidos\GPWebPay\Param\OrderNumber;
use Pixidos\GPWebPay\Param\ResponseParam;
use Pixidos\GPWebPay\Param\Utils\Sorter;

class Response implements ResponseInterface {
    /**
     * @var IParam[] $params
     */
    private $params;
    /**
     * @var string digest
     */
    private $digest;
    /**
     * @var string digest1
     */
    private $digest1;
    /**
     * @var string gatewayKey
     */
    private $gatewayKey;

    /**
     * @param string $operation
     * @param string $ordernumber
     * @param string $merordernum
     * @param string $md
     * @param int    $prcode
     * @param int    $srcode
     * @param string $resulttext
     * @param string $digest
     * @param string $digest1
     * @param string $gatewayKey
     */
    public function __construct(
        $operation,
        $ordernumber,
        $merordernum,
        $md,
        $prcode,
        $srcode,
        $resulttext,
        $digest,
        $digest1,
        $gatewayKey
    ) {
        $this->addParam(
            new Operation(EnumOperation::fromScalar($operation))
        );
        $this->addParam(new OrderNumber($ordernumber));

        if ('' !== $merordernum) {
            $this->addParam(new MerOrderNum($merordernum));
        }

        if ('' !== $md) {
            $this->addParam(new Md($md));
        }
        $this->addParam(new ResponseParam((string) $prcode, self::PRCODE));
        $this->addParam(new ResponseParam((string) $srcode, self::SRCODE));
        $this->addParam(new ResponseParam($resulttext, self::RESULTTEXT));


        $this->digest = $digest;
        $this->digest1 = $digest1;
        $this->gatewayKey = $gatewayKey;
    }


    /**
     * @return string
     */
    public function getDigest() {
        return $this->digest;
    }

    /**
     * @return bool
     */
    public function hasError() {
        return (bool) $this->params[self::PRCODE]->getValue()
            || (bool) $this->params[self::SRCODE]->getValue();
    }

    /**
     * @return string
     */
    public function getDigest1() {
        return $this->digest1;
    }

    /**
     * @return string|null
     */
    public function getMerOrderNumber() {
        return isset($this->params[Param::MERORDERNUM]) ? (string) $this->params[Param::MERORDERNUM] : null;
    }

    /**
     * @return string|null
     */
    public function getMd() {
        if (!isset($this->params[Param::MD])) {
            return null;
        }
        $explode = explode('|', (string) $this->params[Param::MD], 2);

        return isset($explode[1]) ? $explode[1] : null;
    }

    /**
     * @return string
     */
    public function getGatewayKey() {
        return $this->gatewayKey;
    }

    /**
     * @return string
     */
    public function getOrderNumber() {
        return (string) $this->params[Param::ORDERNUMBER];
    }

    /**
     * @return int
     */
    public function getSrcode() {
        return (int) $this->params[self::SRCODE]->__toString();
    }

    /**
     * @return int
     */
    public function getPrcode() {
        return (int) $this->params[self::PRCODE]->__toString();
    }

    /**
     * @return string
     */
    public function getResultText() {
        return (string) $this->params[self::RESULTTEXT];
    }

    /**
     * @return string|null
     */
    public function getUserParam1() {
        return isset($this->params[Param::USERPARAM]) ? (string) $this->params[Param::USERPARAM] : null;
    }


    /**
     * @param IParam $param
     */
    public function addParam($param) {
        $this->params[$param->getParamName()] = $param;
    }

    /**
     * @param string $paramName
     */
    public function getParam($paramName) {
        return isset($this->params[$paramName]) ? $this->params[$paramName] : null;
    }

    /**
     * @return IParam[]
     */
    public function getParams() {
        return $this->params;
    }

    public function sortParams() {
        $this->params = Sorter::sortResponseParams($this->params);
    }
}
