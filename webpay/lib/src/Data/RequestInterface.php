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

interface RequestInterface {
    /**
     * Return all parameters
     *
     * @return array<string, string>
     */
    public function getParams();

    /**
     * @param IParam $param
     */
    public function setParam($param);

    /**
     * Return only parameters what are included in digest
     *
     * @return array<string, string>
     */
    public function getDigestParams();

    /**
     * @param bool $asPost
     *
     * @return string
     */
    public function getRequestUrl($asPost = false);

    /**
     * Sorting Param by documentation
     */
    public function sortParams();
}
