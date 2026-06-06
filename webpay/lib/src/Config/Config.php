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

class Config {
    /**
     * @var PaymentConfigProvider
     */
    private $paymentConfigProvider;
    /**
     * @var SignerConfigProvider
     */
    private $signerConfigProvider;


    public function __construct(
        PaymentConfigProvider $paymentConfigProvider,
        SignerConfigProvider  $signerConfigProvider
    ) {
        $this->paymentConfigProvider = $paymentConfigProvider;
        $this->signerConfigProvider = $signerConfigProvider;
    }

    public function getPaymentConfigProvider() {
        return $this->paymentConfigProvider;
    }

    public function getSignerConfigProvider() {
        return $this->signerConfigProvider;
    }
}
