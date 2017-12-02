<?php

namespace App\Util;

use DateTime;

class NequiClient
{

    private $host    = "a7zgalw2j0.execute-api.us-east-1.amazonaws.com";
    private $channel = "MF-001";

    /**
     * Encapsula el consumo del servicio de validacion de cliente del API y retorna la respuesta del servicio
     */
    public function validateClient($clientId, $phoneNumber, $value)
    {
        $servicePath = "/qa/-services-clientservice-validateclient";
        $body        = $this->getBodyValidateClient($this->channel, $clientId, $phoneNumber, $value);
        $response    = AwsSigner::makeSignedRequest($this->host, $servicePath, 'POST', $body);

        return $response;
    }

    /**
     * Forma el cuerpo para consumir el servicio de validación de cliente del API
     */
    private function getBodyValidateClient($channel, $clientId, $phoneNumber, $value)
    {
        $messageId = substr(strval((new DateTime())->getTimestamp()), 0, 9);
        return array(
            "RequestMessage" => array(
                "RequestHeader" => array(
                    "Channel"     => $channel,
                    "RequestDate" => gmdate("Y-m-d\TH:i:s\\Z"),
                    "MessageID"   => $messageId,
                    "ClientID"    => $clientId),
                "RequestBody"   => array(
                    "any" => array(
                        "validateClientRQ" => array(
                            "phoneNumber" => $phoneNumber,
                            "value"       => $value,
                        ),
                    ),
                ),
            ),
        );
    }

    public function getPoints($latitude, $longitude, $radius = 0.1, $filter = 3)
    {
        $servicePath = "/qa/-services-nequipointservice-getnequipoints";
        $messageId   = substr(strval((new DateTime())->getTimestamp()), 0, 9);
        $body        = array(
            "RequestMessage" => array(
                "RequestHeader" => array(
                    "Channel"     => $this->channel,
                    "RequestDate" => gmdate("Y-m-d\TH:i:s\\Z"),
                    "MessageID"   => $messageId,
                    "ClientID"    => "",
                ),
                "RequestBody"   => array(
                    "any" => array(
                        "getNequiPointsRQ" => array(
                            "latitude"  => $latitude,
                            "longitude" => $longitude,
                            "radius"    => $radius,
                            "filter"    => $filter,
                        ),
                    ),
                ),
            ),
        );
        $response = AwsSigner::makeSignedRequest($this->host, $servicePath, 'POST', $body);

        return $response;
    }

}
