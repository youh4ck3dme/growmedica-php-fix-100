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

use Pixidos\GPWebPay\Exceptions\InvalidArgumentException;
use Pixidos\GPWebPay\Param\DepositFlag;
use Pixidos\GPWebPay\Param\IParam;
use Pixidos\GPWebPay\Param\MerchantNumber;
use Pixidos\GPWebPay\Param\Utils\DigestParamsFilter;
use Pixidos\GPWebPay\Param\Utils\Sorter;
use UnexpectedValueException;

class Request implements RequestInterface {
    /**
     * @var  OperationInterface $operation
     */
    private $operation;
    /**
     * @var array<string, string> $params
     */
    private $params;
    /**
     * @var string $url
     */
    private $url;

    /**
     * @param OperationInterface $operation
     * @param MerchantNumber     $merchantNumber
     * @param DepositFlag        $depositFlag
     * @param string             $url
     *
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function __construct(
        OperationInterface $operation,
        MerchantNumber     $merchantNumber,
        DepositFlag        $depositFlag,
                           $url
    ) {
        $this->operation = $operation;
        $this->url = $url;
        $this->setParam($merchantNumber);
        $this->setParam($depositFlag);

        $this->setParams();
    }

    /**
     * @return array<string, string>
     */
    public function getParams() {
        return $this->params;
    }

    public function sortParams() {
        $params = Sorter::sortRequestParams($this->params);
        $this->params = $params;
    }


    /**
     * @return array<string, string>
     */
    public function getDigestParams() {
        $this->sortParams();

        return DigestParamsFilter::filter($this->params);
    }

    /**
     * @param IParam $param
     */
    public function setParam($param) {
        $this->params[$param->getParamName()] = (string) $param;
    }

    /**
     * @param bool $asPost
     */
    public function getRequestUrl($asPost = false) {
        if ($asPost) {
            return $this->url;
        }

        return $this->url.'?'.http_build_query($this->getParams());
    }


    /**
     * Sets params to array
     */
    private function setParams() {
        foreach ($this->operation->getParams() as $param) {
            $this->setParam($param);
        }
    }
}
