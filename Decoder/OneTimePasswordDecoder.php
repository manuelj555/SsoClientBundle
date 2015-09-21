<?php
/*
 * This file is part of the Manuel Aguirre Project.
 *
 * (c) Manuel Aguirre <programador.manuel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ku\SsoServerBundle\Encoder;


/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
class OneTimePasswordDecoder
{
    /**
     * @var string
     */
    private $key;

    /**
     * @param string $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * @param $hash
     *
     * @return array
     */
    public function decodeHash($hash)
    {
        return explode(':', base64_decode($hash));
    }

    /**
     * @param $hash1
     * @param $hash2
     *
     * @return bool
     */
    public function compareHashes($hash1, $hash2)
    {
        if (strlen($hash1) !== $c = strlen($hash2)) {
            return false;
        }
        $result = 0;
        for ($i = 0; $i < $c; $i++) {
            $result |= ord($hash1[$i]) ^ ord($hash2[$i]);
        }
        return 0 === $result;
    }
}