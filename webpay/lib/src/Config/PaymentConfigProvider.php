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

use Pixidos\GPWebPay\Exceptions\InvalidArgumentException;
use Pixidos\GPWebPay\Exceptions\LogicException;
use Pixidos\GPWebPay\Param\DepositFlag;
use Pixidos\GPWebPay\Param\MerchantNumber;
use Pixidos\GPWebPay\Param\ResponseUrl;

class PaymentConfigProvider {
    /**
     * @var string defaultGatewayKey
     */
    private $defaultGateway = '';
    /**
     * @var PaymentConfig[]
     */
    private $paymentConfigs = [];

    /**
     * @param PaymentConfig $paymentConfig
     */
    public function addPaymentConfig($paymentConfig) {
        $this->paymentConfigs[$paymentConfig->getGateway()] = $paymentConfig;
    }

    /**
     * @param string $gateway
     *
     * @return string
     */
    public function getUrl($gateway) {
        return $this->paymentConfigs[$this->getGateway($gateway)]->getUrl();
    }

    /**
     * @param string $gateway
     *
     * @return MerchantNumber
     */
    public function getMerchantNumber($gateway) {
        return $this->paymentConfigs[$this->getGateway($gateway)]->getMerchantNumber();
    }

    /**
     * @param string $gateway
     *
     * @return DepositFlag
     */
    public function getDepositFlag($gateway) {
        return $this->paymentConfigs[$this->getGateway($gateway)]->getDepositFlag();
    }

    /**
     * @return string
     */
    public function getDefaultGateway() {
        if ('' === $this->defaultGateway) {
            throw new LogicException(
                sprintf('You need first set default key by %s::setDefaultGateway', self::class)
            );
        }

        return $this->defaultGateway;
    }

    /**
     * @param string $gateway
     */
    public function setDefaultGateway($gateway) {
        $this->defaultGateway = $gateway;
    }

    /**
     * @param null|string $gateway
     *
     * @return string
     */
    public function getGateway($gateway = null) {
        if (null === $gateway) {
            $gateway = $this->getDefaultGateway();
        }

        if (!array_key_exists($gateway, $this->paymentConfigs)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Config for key: "%s" not exist. Possible keys are: "%s"',
                    $gateway,
                    implode(', ', array_keys($this->paymentConfigs))
                )
            );
        }

        return $gateway;
    }

    /**
     * @param string|null $gateway
     *
     * @return ResponseUrl|null
     */
    public function getResponseUrl($gateway = null) {
        return $this->paymentConfigs[$this->getGateway($gateway)]->getResponseUrl();
    }
}
