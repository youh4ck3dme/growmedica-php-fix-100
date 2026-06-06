<?php

/*
 * @Copyright This file is part of the Pixidos package.
 *
 *  (c) Ondra Votava <ondra@votava.dev>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

if (\PHP_VERSION_ID < 80000) {
    interface Stringable {
        /**
         * @return string
         */
        public function __toString();
    }
}
