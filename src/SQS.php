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
 * Description of SQS
 *
 * @author Cristian Tala <yomismo@cristiantala.cl>
 */
class SQS extends Sdk {

    var $queueName;
    var $queueUrl = null;

    /**
     * 
     * @param type $credentials
     * @param type $region
     * @param type $version
     * @param type $debug
     */
    function __construct($credentials = null, $region = "us-west-2", $version = "latest", $debug = false) {
        parent::__construct($credentials, $region, $version, $debug);

        $this->queueName = "MyOwnQueue";
        $this->credentials = array(
            'key' => env('AMAZON_KEY_SQS', $this->credentials["key"]),
            'secret' => env('AMAZON_SECRET_SQS', $this->credentials["secret"]),
        );

        $this->sharedConfig = [
            'region' => $this->region,
            'version' => $this->version,
            'credentials' => $this->credentials
        ];
        $this->logThis(print_r($this->sharedConfig, true));
        // Creamos la clase SDK.
        $this->sdk = new AWSSdk($this->sharedConfig);
        $this->client = $this->sdk->createSqs();
    }

    function getQueueName() {
        return $this->queueName;
    }

    function setQueueName($queueName) {
        $this->queueName = $queueName;
    }

    /*
     * Crearemos la Queue en caso de ser necesario
     * Para devolver la URL
     * Si la Url ya fue calculada la usamos
     */

    function getQueueUrl($force = false) {
        if ($force || $this->queueUrl == null):
            // Creamos la QUEUE
            $queue_options = array(
                'QueueName' => $this->queueName
            );
            //Se crea la queue
            try {
                $this->client->createQueue($queue_options);
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }
            // Obtenemos la URL de la queue.
            $result = $this->client->getQueueUrl(array('QueueName' => $this->queueName));
            $queue_url = $result->get('QueueUrl');
            $this->queueUrl = $queue_url;
            return $queue_url;
        else:
            return $this->queueUrl;
        endif;
    }

    /**
     * 
     * @param type $message
     */
    function sendMessage($message) {
        // Send the message
        try {
            $this->client->sendMessage(array(
                'QueueUrl' => $this->getQueueUrl(),
                'MessageBody' => json_encode($message)
            ));
        } catch (Exception $ex) {
            error_log('Error enviando el mensaje a la queue ' . $ex->getTraceAsString());
        }
    }

    function receiveMessage() {
        try {
            $message = $this->client->receiveMessage(array(
                'QueueUrl' => $this->getQueueUrl()
            ));
        } catch (Exception $exc) {
            error_log($exc->getTraceAsString());
            $message = null;
        }
        return $message;
    }

}
