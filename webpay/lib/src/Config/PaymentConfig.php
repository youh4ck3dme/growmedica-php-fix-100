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

namespace Pixidos\GPWebPay\Config;

use Pixidos\GPWebPay\Param\DepositFlag;
use Pixidos\GPWebPay\Param\MerchantNumber;
use Pixidos\GPWebPay\Param\ResponseUrl;

class PaymentConfig {
    /**
     * @var string
     */
    private $url;
    /**
     * @var MerchantNumber
     */
    private $merchantNumber;
    /**
     * @var DepositFlag
     */
    private $depositFlag;
    /**
     * @var string
     */
    private $gateway;
    /**
     * @var ResponseUrl|null
     */
    private $responseUrl;

    /**
     * Settings constructor.
     *
     * @TODO: url as object
     *
     * @param string           $url
     * @param MerchantNumber   $merchantNumber
     * @param DepositFlag      $depositFlag
     * @param string           $gateway
     * @param ResponseUrl|null $responseUrl
     */
    public function __construct(
        $url,
        $merchantNumber,
        $depositFlag,
        $gateway,
        $responseUrl = null
    ) {
        $this->url = $url;
        $this->merchantNumber = $merchantNumber;
        $this->depositFlag = $depositFlag;
        $this->gateway = $gateway;
        $this->responseUrl = $responseUrl;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @return MerchantNumber
     */
    public function getMerchantNumber() {
        return $this->merchantNumber;
    }

    /**
     * @return DepositFlag
     */
    public function getDepositFlag() {
        return $this->depositFlag;
    }

    /**
     * @return ResponseUrl|null
     */
    public function getResponseUrl() {
        return $this->responseUrl;
    }


    /**
     * @return string
     */
    public function getGateway() {
        return $this->gateway;
    }
}
