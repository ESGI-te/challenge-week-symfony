<?php

namespace App\Service;
use Exception;
use SendinBlue\Client\Configuration;
use GuzzleHttp\Client;
use \SendinBlue\Client\Api\TransactionalEmailsApi;
use \SendinBlue\Client\Model\SendSmtpEmail;

class MailService
{
    public function __construct() {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', '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');
        $this->apiInstance = new TransactionalEmailsApi(
            new Client(),
            $config
        );
    }

    public function sendMail($parameters){
        $email = new SendSmtpEmail($parameters);

        try {
            $this->apiInstance->sendTransacEmail($email);
        } catch (Exception $e) {
            echo 'Exception when calling EmailCampaignsApi->createEmailCampaign: ', $e->getMessage(), PHP_EOL;
        }
    }
}