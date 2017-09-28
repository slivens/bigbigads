<?php

namespace App\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Curl 
{
    static public function request ($params, $url)
    {   
        $jsonData = json_encode($params);   
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData))
        );
        $response = curl_exec($ch);
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200' || curl_getinfo($ch, CURLINFO_HTTP_CODE) == '201') {
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $headerSize);
            $result = substr($response, $headerSize);
        } else {
            $error = ['message' => 'server is busy, please refresh again', 'reason' => $response];
            return $error;
        }
        curl_close($ch);
        $result = json_decode($result, true);

        return $result;
    }
}