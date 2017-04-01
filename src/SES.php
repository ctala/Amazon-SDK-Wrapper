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
 * Description of SES
 *
 * @author Cristian Tala <yomismo@cristiantala.cl>
 */
class SES extends Sdk {

    var $sender;
    var $toAddress;
    var $subject;
    var $body;
    var $cc = null;
    var $bcc = null;

    /**
     * 
     * @param type $credentials
     * @param type $region
     * @param type $version
     * @param type $debug
     */
    function __construct($credentials = null, $region = "us-west-2", $version = "latest", $debug = false) {

        parent::__construct($credentials, $region, $version, $debug);

        $this->sender = "Cristian Tala S. <yomismo@cristiantala.cl>";
        $this->subject = "Sin Título";

        /*
         * Si no existen particulares se usan las generales.
         */
        $this->credentials = array(
            'key' => myenv('AMAZON_KEY_SES', $this->credentials["key"]),
            'secret' => myenv('AMAZON_SECRET_SES', $this->credentials["secret"]),
        );
        $this->sharedConfig = [
            'region' => $this->region,
            'version' => $this->version,
            'credentials' => $this->credentials
        ];

        $this->logThis(print_r($this->sharedConfig, true));

        // Creamos la clase SDK.
        $this->sdk = new AWSSdk($this->sharedConfig);
        $this->client = $this->sdk->createSes();
    }

    /**
     * 
     * @param type $isHTML
     */
    public function sendMail($isHTML = true) {
        $request = array();
        $request['Source'] = $this->sender;
        $request['Destination']['ToAddresses'] = array($this->toAddress);
        if (!($this->cc == null) || !($this->cc == "")) {
            $request['Destination']['CcAddresses'] = array($this->cc);
        }

        if (!($this->bcc == null) || !($this->bcc == "")) {
            $request['Destination']['BccAddresses'] = array($this->cc);
        }
        $request['Message']['Subject']['Data'] = $this->subject;
        if ($isHTML) {
            $request['Message']['Body']['Html']['Data'] = $this->body;
            $request['Message']['Body']['Html']['Charset'] = "UTF-8";
        } else {
            $request['Message']['Body']['Text']['Data'] = $this->body;
            $request['Message']['Body']['Text']['Charset'] = "UTF-8";
        }
        try {
            $result = $this->client->sendEmail($request);
            $messageId = $result->get('MessageId');
            if ($this->debug) {
                error_log("Email sent! Message ID: $messageId" . "\n");
            }
        } catch (Exception $e) {
            error_log("The email was not sent. Error message: ");
            error_log($e->getMessage() . "\n");
        }
    }

    /**
     * https://tools.ietf.org/html/rfc2047#section-2
     * base64_encode for sender
     * @param type $email
     * @param type $name
     * @param type $encoding
     */
    function setSender($email, $name = "NO NAME", $encoding = "UTF-8") {

        $sender = "=?$encoding?B?" . base64_encode($name) . "?= <$email>";
        $this->sender = $sender;
    }

    /**
     * https://tools.ietf.org/html/rfc2047#section-2
     * base64_encode for subject
     * @param type $subject
     * @param type $encoding
     */
    function setSubject($subject, $encoding = "UTF-8") {
        $encodedSubject = "=?$encoding?B?" . base64_encode($subject) . "?=";
        $this->subject = $encodedSubject;
    }

}
