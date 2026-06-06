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


namespace Pixidos\GPWebPay;

use Closure;
use Pixidos\GPWebPay\Config\PaymentConfigProvider;
use Pixidos\GPWebPay\Data\ResponseInterface;
use Pixidos\GPWebPay\Enum\Param;
use Pixidos\GPWebPay\Exceptions\GPWebPayException;
use Pixidos\GPWebPay\Exceptions\GPWebPayResultException;
use Pixidos\GPWebPay\Exceptions\SignerException;
use Pixidos\GPWebPay\Signer\SignerProviderInterface;

class ResponseProvider implements ResponseProviderInterface {
    /**
     * @var array<callable>
     */
    public $onSuccess = [];

    /**
     * @var array<callable>
     */
    public $onError = [];

    /**
     * @var SignerProviderInterface
     */
    private $signerProvider;

    /**
     * @var PaymentConfigProvider settings
     */
    private $settings;

    /**
     * Provider constructor.
     *
     * @param PaymentConfigProvider   $configProvider
     * @param SignerProviderInterface $signerProvider
     */
    public function __construct(
        PaymentConfigProvider   $configProvider,
        SignerProviderInterface $signerProvider
    ) {
        $this->signerProvider = $signerProvider;
        $this->settings = $configProvider;
    }


    /**
     * @param ResponseInterface $response
     */
    public function provide($response) {
        try {
            if (!$this->verifyPaymentResponse($response)) {
                throw new SignerException('Digest or Digest1 is incorrect!');
            }

            // verify PRCODE and SRCODE
            if ($response->hasError()) {
                throw new GPWebPayResultException(
                    'Response has an error.',
                    $response->getPrcode(),
                    $response->getSrcode(),
                    $response->getResultText()
                );
            }

            $this->onSuccess($response);
        } catch (GPWebPayException $exception) {
            $this->onError($exception, $response);
        }

        return $response;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return bool
     * @throws GPWebPayException
     * @throws GPWebPayResultException
     */
    public function verifyPaymentResponse($response) {
        // verify digest & digest1
        $signer = $this->signerProvider->get($response->getGatewayKey());

        $params = $response->getParams();
        $verify = $signer->verify($params, $response->getDigest());
        $params[Param::MERCHANTNUMBER] = $this->settings->getMerchantNumber($response->getGatewayKey());
        $verify1 = $signer->verify($params, $response->getDigest1());

        return !(false === $verify || false === $verify1);
    }

    /**
     * @param Closure $closure
     */
    public function addOnSuccess($closure) {
        $this->onSuccess[] = $closure;

        return $this;
    }

    /**
     * @param Closure $closure
     */
    public function addOnError($closure) {
        $this->onError[] = $closure;

        return $this;
    }

    private function onSuccess(ResponseInterface $response) {
        foreach ($this->onSuccess as $callback) {
            $callback($response);
        }
    }

    /**
     * @param GPWebPayException $exception
     * @param ResponseInterface $response
     */
    private function onError(GPWebPayException $exception, ResponseInterface $response) {
        if (0 === count($this->onError)) {
            throw $exception;
        }

        foreach ($this->onError as $callback) {
            $callback($exception, $response);
        }
    }
}
