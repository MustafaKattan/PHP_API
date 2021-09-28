<?php

class GoldmineHandler{

    public static function getCreditReportData($companyId){
        $url = "https://api-goldmine.bierensgroup.net/serviceCreditSafe/requestCreditReport.php?CompanyId=%%COMPANY_ID%%&ReportType=json";
        $url = str_replace("%%COMPANY_ID%%", $companyId, $url);

        $headers = [
            "Accept" => "application/json",
            "Content-Type" => "application/json",
        ];
        $ch = curl_init();
        if(substr($_SERVER['HTTP_HOST'],0, 9) == "localhost"){
            curl_setopt($ch, CURLOPT_CAINFO, 'C:\xampp\php\extras\ssl\bierensgroup.pem');
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