<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client as Client;
use stdClass;

class FlightsController extends Controller
{
    private $flightsRoute = 'http://prova.123milhas.net/api/flights';

    /**
     * consome a api de teste da 123milhas e retorna a sua resposta no formato json
     */
    public function list () {
        $endpoint = $this->flightsRoute;
        $client = new Client();

        $response = $client->request('GET', $endpoint);

        return json_decode($response->getBody());
    }

    /**
     * consome a api de teste da 123milhas e retorna uma agrupamento da sua resposta no formato json
     */
    public function groups ()
    {
        $flights = $this->list(); // obtem as flights

        $response = [];
        if (is_array($flights)) {
            $response = $this->agroup($flights); // realiza o agrupamento das flights
        }

        return $response;
    }

    /**
     * realiza o agrupamento das flights
     */
    private function agroup (array $flights) {
        $set = [];
        $groups = [];
        $fares = [];

        $outbounds = [];
        $inbounds = [];

        // agrupa as inbounds e outbounds por fare
        foreach ($flights as $flight) {
            if (isset($flight->fare) && !empty($flight->fare) && isset($flight->price) && !empty($flight->price)) {
                if (is_object($flight) && isset($flight->inbound) && $flight->inbound == 1) {
                    $inbounds[$flight->fare][] = $flight;
                } else if (is_object($flight) && isset($flight->outbound) && $flight->outbound == 1) {
                    $outbounds[$flight->fare][] = $flight;
                }
                if (!in_array($flight->fare, $fares)) {
                    $fares[] = $flight->fare;
                }
            }
        }

        $cheapestGroup = null;

        foreach ($fares as $fare) {
            if (isset($outbounds[$fare])) {
                $group = new stdClass();
                $group->uniqueId = $fare; // como cada grupo pertence a uma fare então pode-se atribuir como id
                $group->inbound = $this->sort_by($inbounds[$fare], 'price'); // inbounds de uma fare ordernados
                $group->outbound= $this->sort_by($outbounds[$fare], 'price'); // outbounds de uma fare ordernados

                $group->totalPrice = $group->inbound[0]->price + $group->outbound[0]->price; // o primeiro elemento de group->inbound e group->outbound é sempre o menor de cada conjunto
                $groups[] = $group;

                // captura o melhor grupo
                if (is_null(($cheapestGroup)) || $cheapestGroup->totalPrice > $group->totalPrice) {
                    $cheapestGroup = $group;
                }
            }
        }

        $groups = $this->sort_by($groups, 'totalPrice'); // ordena os grupos por menor preço

        $set['flights'] = $flights;
        $set['groups'] = $groups;
        $set['totalGroups'] = count($groups);
        $set['totalFlights'] = count($flights);
        $set['cheapestPrice'] = $cheapestGroup->totalPrice;
        $set['cheapestGroup'] = $cheapestGroup->uniqueId;

        return $set;
    }

    /**
     * orderna de forma ascendente um array de objetos por um determinado atributo
     */
    private function sort_by (array $array, $attribute) {
        usort($array, function ($a, $b) use ($attribute) {
            return $a->{$attribute} < $b->{$attribute} ? -1 : 1;
        });
        return $array;
    }

    //
}
