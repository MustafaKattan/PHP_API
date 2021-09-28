<?php
    class ChartsGenerator{
        public static function getChart($chartData){
        if($chartData !== false){
            //sort date as ASC
            $creditRatingDates = [];
            foreach($chartData as $key => $chartDataItem){
                $creditRatingDates[$key] = $chartDataItem['CreditRatingDate'];
            }
            array_multisort($creditRatingDates, SORT_ASC, $chartData);
            // generate image from array data
            $dataSet = [];
            foreach($chartData as $item){
                if(!isset($dataSet[date("MY", strtotime($item['CreditRatingDate']))])){
                    $dataSet[date("MY", strtotime($item['CreditRatingDate']))] = $item['ProviderScore'];
                } else {
                    if($item['ProviderScore'] > $dataSet[date("MY", strtotime($item['CreditRatingDate']))]){
                    $dataSet[date("MY", strtotime($item['CreditRatingDate']))] = $item['ProviderScore'];
                    }
                }
            }
            $graph = new Graph(375, 200);
            $graph->SetScale('textlin' );
            $graph->xaxis->SetTickLabels(array_slice(array_keys($dataSet), 0, 7, true));
            $graph->img->SetMargin(30, 30, 30, 30);
            $graph->img->SetAntiAliasing(false);
            $graph->title->SetFont(FF_FONT1, FS_BOLD,4);
            $p1 = new LinePlot(array_slice(array_values($dataSet), 0, 7, true));
            $p1->SetFillColor("#ff4500");
            $p1->mark->SetFillColor("#ff4500");
            $p1->value->SetMargin(20);
            // Create the line
            $graph->Add($p1);
            $p1->value->HideZero();
            $p1->value->show();
            // Output line
            $ih = $graph->Stroke(_IMG_HANDLER);
            ob_start();
            imagepng($ih);
            $return = ob_get_contents();
            ob_end_clean();
            $image = $return;
        } else {
            // generate "No data availeble" image
            $path = '../include/resources/img/noData.png';
            $image = file_get_contents($path);
        }
            return 'data:image/png;base64,'.base64_encode($image);
        }

    }
?>
