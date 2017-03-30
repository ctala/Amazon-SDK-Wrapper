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

    function __construct() {
        parent::__construct();
        $this->sender = "Cristian Tala S. <yomismo@cristiantala.cl>";
        $this->subject = "Sin Título";

        /*
         * Si no existen particulares se usan las generales.
         */
        $this->credentials = array(
            'key' => env('AMAZON_KEY_SES', $this->credentials["key"]),
            'secret' => env('AMAZON_SECRET_SES', $this->credentials["secret"]),
        );

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

}
