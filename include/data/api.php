<?php
class Credit_Report
{
    function getCreditData($companyId)
    {
        $url = "https://api-example.com";
        $url = str_replace("%%COMPANY_ID%%", $companyId, $url);

        $headers = [
            "Accept" => "application/json",
            "Content-Type" => "application/json",
        ];
        $ch = curl_init();
        if(substr($_SERVER['HTTP_HOST'],0, 9) == "localhost"){
            curl_setopt($ch, CURLOPT_CAINFO, '\ssl\example.pem');
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if (!$response) {
            $reponseData = 'Curl error: ' . curl_error($ch);
        } else {
            $reponseData = json_decode($response, true);
        }

        return $reponseData;
    }

}
