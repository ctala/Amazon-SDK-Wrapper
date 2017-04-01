<?php

/*
 * The MIT License
 *
 * Copyright 2017 Cristian Tala <yomismo@cristiantala.cl>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace ctala\AWS;

use Aws\Sdk as AWSSdk;

/**
 * Description of Sdk
 *
 * @author Cristian Tala <yomismo@cristiantala.cl>
 */
class Sdk {

    var $region;
    var $version;
    protected $credentials;
    var $sharedConfig = NULL;
    var $sdk;
    protected $client;
    var $debug = false;

    /**
     * 
     * @param type $credentials
     * @param type $region
     * @param type $version
     * @param type $debug
     */
    function __construct($credentials = null, $region = "us-west-2", $version = "latest", $debug = false) {

        $this->region = myenv('AMAZON_REGION', $region);
        $this->version = myenv('AMAZON_VERSION', $version);

        if ($credentials == null) {
            $this->credentials = array(
                'key' => myenv('AMAZON_KEY', ''),
                'secret' => myenv('AMAZON_SECRET', ''),
            );
        } else {
            $this->credentials = $credentials;
        }


        $this->sharedConfig = [
            'region' => $this->region,
            'version' => $this->version,
            'credentials' => $this->credentials
        ];

        $this->debug = myenv('APP_DEBUG', $debug);
    }

    function getClient() {
        return $this->client;
    }

    function getRegion() {
        return $this->region;
    }

    function getVersion() {
        return $this->version;
    }

    function getCredentials() {
        return $this->credentials;
    }

    function getSharedConfig() {
        return $this->sharedConfig;
    }

    function getSdk() {
        return $this->sdk;
    }

    function getDebug() {
        return $this->debug;
    }

    function setRegion($region) {
        $this->region = $region;
    }

    function setVersion($version) {
        $this->version = $version;
    }

    function setCredentials($credentials) {
        $this->credentials = $credentials;
    }

    function setSharedConfig($sharedConfig) {
        $this->sharedConfig = $sharedConfig;
    }

    function setSdk($sdk) {
        $this->sdk = $sdk;
    }

    function setDebug($debug) {
        $this->debug = $debug;
    }

    function reCreateSDK() {
        $this->sharedConfig = [
            'region' => $this->region,
            'version' => $this->version,
            'credentials' => $this->credentials
        ];
        $this->sdk = new AWSSdk($this->sharedConfig);
    }

    function logThis($message) {
        if ($this->debug) {
            error_log($message);
        }
    }

}
