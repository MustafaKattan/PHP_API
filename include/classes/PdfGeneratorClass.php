<?php

 class PdfGenerator{
    private $companyData = null;
    private $result = "";
    private $templateHandler = null;
    private $templateSegments = [];
    private $statusColors = [
        "Active" => "green",
        "Pending" => "orange",
        "NonActive" => "red",
    ];
    private $riskStatus = [
        "Low Risk" => "green",
        "Moderate Risk" => "orange",
        "High Risk" => "red",
    ];

    public function __construct($companyData){
        $this->companyData = $companyData;
    }

    public function generate($name = 'client'){
    $result = false;
    $this->templateHandler = new TemplateHandler($name);
        if($this->templateHandler->load()){
            $this->templateSegments = $this->templateHandler->output();
            foreach($this->templateSegments as $segment){
                if(gettype($segment) !== "array"){
                    preg_match_all("/(%%[\w]*%%)/", $segment, $matches);
                    $check = [
                        'hasArray' => false,
                        'exists' => true,
                        'arrayNames' => [],
                    ];
                    foreach($matches[1] as $placeHolder) {
                        if (substr($placeHolder, 2, 4) == "Arr_") {
                            $check['hasArray'] = true;
                            $check['arrayNames'][] = substr($placeHolder, 2, strlen($placeHolder) - 4);
                        }
                    }
                    if($check['hasArray']){
                        foreach($check['arrayNames'] as $arrayName) {
                            if (!isset($this->companyData[$arrayName])) {
                                $check['exists'] = false;
                            }
                        }
                        if($check['exists']){
                            $this->result .= $segment;
                        }
                    } else {
                        $this->result .= $segment;
                    }
                }
            }
            preg_match_all("/(%%[\w]*%%)/", $this->result, $matches);

	    $historyData = isset($this->companyData['Arr_HistoryCreditRatings'])?$this->companyData['Arr_HistoryCreditRatings']:false;
            $chart = ChartsGenerator::getChart($historyData);

            foreach($matches[1] as $placeHolder){
                if(substr($placeHolder, 2, 4) == "IMG_") {
                    $this->result = str_replace($placeHolder, $chart, $this->result);
                } elseif(substr($placeHolder,0, 6) == "%%Arr_") {
                    $arr_key = substr($placeHolder, 2, (strlen($placeHolder) - 4));
                    $repeatData = $this->companyData[$arr_key];
                    $repeatBlockName = strtolower(substr($arr_key, 4));
                    $blockResult = "";
                    if (isset($this->templateSegments['repeat_blocks_vertical'][$repeatBlockName])) {
                        $repeatBlockType = "vertical";
                    } elseif (isset($this->templateSegments['repeat_blocks_horizontal'][$repeatBlockName])) {
                        $repeatBlockType = "horizontal";
                    }
                    preg_match_all("/(%%[\w-]*%%)/", $this->templateSegments['repeat_blocks_' . $repeatBlockType][$repeatBlockName], $matches2);
                    if ($repeatBlockType == "vertical") {
                        foreach ($repeatData as $item) {
                            $tmp = $this->templateSegments['repeat_blocks_' . $repeatBlockType][$repeatBlockName];
                            foreach ($matches2[1] as $placeHolder2) {
                                $placeHolder2 = substr($placeHolder2, 2, strlen($placeHolder2) - 4);
                                foreach ($item as $key => $value) {
                                    if ($key == $placeHolder2) {
                                        $tmp = str_replace("%%" . $placeHolder2 . "%%", $value, $tmp);
                                    }
                                }
                            }
                            $blockResult .= $tmp;
                        }
                    } elseif ($repeatBlockType == "horizontal") {
                        $blockResult = $this->templateSegments['repeat_blocks_' . $repeatBlockType][$repeatBlockName];
                        // TODO: format $repeatData to a items array with the last 3 years or N/A
                        foreach ($matches2[1] as $placeHolder2) {
                            $columnIndex = ((int)substr($placeHolder2, 6, 1)) - 1;
                            $dataName = substr($placeHolder2, 8, (strlen($placeHolder2) - 10));
                            $blockResult = str_replace($placeHolder2, $repeatData[$columnIndex][$dataName], $blockResult);
                        }
                    }
                    $this->result = str_replace($placeHolder, $blockResult, $this->result);
                } else {
                    if(isset($this->companyData[substr($placeHolder, 2, (strlen($placeHolder) - 4))])){
                        $this->result = str_replace($placeHolder, $this->companyData[substr($placeHolder, 2, (strlen($placeHolder) - 4))], $this->result);
                        if($placeHolder == "%%CompanyStatus%%"){
                            $this->result = str_replace("%%%status-color%%%", $this->statusColors[$this->companyData[substr($placeHolder, 2, (strlen($placeHolder) - 4))]], $this->result);
                        }
                        elseif($placeHolder == "%%InternationalDescription%%"){
                            $this->result = str_replace("%%%riskStatus%%%", $this->riskStatus[$this->companyData[substr($placeHolder, 2, (strlen($placeHolder) - 4))]], $this->result);
                        }
                    } else {

                    }
                }
            }
            $result = true;
        }
        return $result;
    }

    public function output(){
        return $this->result;
    }

}
