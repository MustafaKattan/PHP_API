<?php
require_once __DIR__ . '../../vendor/autoload.php';
require_once('../include/jpgraph/src/jpgraph.php');
require_once('../include/jpgraph/src/jpgraph_line.php');
require_once('../include/jpgraph/src/jpgraph_canvas.php');
require_once('../include/jpgraph/src/jpgraph_bar.php');
/* AUTOLOADER */
spl_autoload_register(function ($class) {
    if(file_exists('../include/classes/'.$class."Class.php")){
        include '../include/classes/' . $class . 'Class.php';
    } else {
        echo "ERROR: File '".$class."Class.php' not found!".PHP_EOL;
        exit();
    }
});

// get the company ID from POSTS or GET argument
$companyId = InputHandler::getInput('id');
if($companyId == false){
    echo "ERROR: No CompanyID! (html argument 'id')".PHP_EOL;
    exit();
}

// get the company data from the goldmine
$companyData = GoldmineHandler::getCreditReportData($companyId)['CreditReport_Data'];

// check company data
if(is_string($companyData) && substr($companyData,0, 10) == "Curl error"){
    echo "ERROR: Invalid ComapnyID".PHP_EOL;
    exit();
// generate the PDF report
} else {
    $pdfReport = new PdfGenerator($companyData);
    $check = $pdfReport->generate();

    $html = $pdfReport->output();

    $mpdf= new \Mpdf\Mpdf(['mode' => 'utf-8','format' => 'A4','margin_left' => 0,'margin_right' => 0,'margin_top' => 5,'margin_bottom' => 6,'margin_header' => 0,'margin_footer' => 1]); //use this customization
    $mpdf->SetHTMLHeader('
<div style="padding: 10px 0; width: 100%; background:  #253b82;"></div>
');
    $mpdf->SetHTMLFooter('
<div style=" width: 100%; background:  #253b82;">
<table width="100%">
    <tr>
        <td style="color: #fff" width="33%">{DATE j-m-Y}</td>
        <td style="color: #fff" width="33%" align="center">{PAGENO}/{nbpg}</td>
        <td width="33%" style="color: #fff; text-align: right; ">Credit report</td>
    </tr>
</table>
</div>
');
    $mpdf->WriteHTML($html);

    $mpdf->Output( );
}


