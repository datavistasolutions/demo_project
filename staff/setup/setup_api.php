<?php
//Session File
include_once '../SessionInfo.php';
//Database File
include_once '../../config/database.php';


use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\WriterFactory;

use Box\Spout\Common\Type;

require_once '../../assets/plugins/spout-2.4.3/src/Spout/Autoloader/autoload.php';

$_SESSION['schoolzone']['SectionMaster_Id'] = '1';
$_SESSION['schoolzone']['ActiveStaffLogin_Id'] = '1';
//-----------------------------------------------------------------------------------------------------------------------
if(isset($_GET['Edit_ExamHeaderInstance'])){

    extract($_POST);


    $updating_CalenderInstance = mysqli_query($mysqli,"Update result_exam_header Set 
    Header_Name ='".htmlspecialchars($edit_Header_Name, ENT_QUOTES)."',
    Header_Type = '".htmlspecialchars($edit_Header_Type, ENT_QUOTES)."',
    abbr='".htmlspecialchars($edit_abbr, ENT_QUOTES)."',
    sequence='".htmlspecialchars($edit_sequence, ENT_QUOTES)."'
    where Id  = '$edit_InstanceId'");

    echo "200";
    
}
//-----------------------------------------------------------------------------------------------------------------------


//-----------------------------------------------------------------------------------------------------------------------
if(isset($_GET['Delete_ExamHeaderInstance'])){

    extract($_POST);

    $deleting_formheader = mysqli_query($mysqli,"DELETE FROM result_exam_header where Id = '$delete_instance_Id'");

    echo "200";
    
}
//-----------------------------------------------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------------------------------------
if(isset($_GET['Add_ExamHeaderInstance'])){
   
    extract($_POST);

    $ActiveStaffLogin_Id = $_SESSION['schoolzone']['ActiveStaffLogin_Id'];
    $SectionMaster_Id = $_SESSION['schoolzone']['SectionMaster_Id'];

 

    $Inserting_StaffQualification = mysqli_query($mysqli,"Insert into result_exam_header
    (Header_Name, Header_Type, abbr, sequence, sectionmaster_Id) 
    Values
    ('".htmlspecialchars($add_Header_Name, ENT_QUOTES)."', '".htmlspecialchars($add_Header_Type, ENT_QUOTES)."', '".htmlspecialchars($add_abbr, ENT_QUOTES)."', '".htmlspecialchars($add_sequence, ENT_QUOTES)."', '$SectionMaster_Id')");
    

    $res['status'] = 'success';
    echo json_encode($res);

    
}
//-----------------------------------------------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------------------------------------
if(isset($_GET['Add_ExamHeaderInstance_InBulk'])){

    $SectionMaster_Id = $_SESSION['schoolzone']['SectionMaster_Id'];  


    // check file name is not empty
    $pathinfo = pathinfo($_FILES["upload_file"]["name"]); //file extension

    $folderMap = '../';
    $fileLocation = "FileUploadLogs/ExamHeaderMaster_".$SectionMaster_Id."_".date("Y-m-d-h-i-s").'.xlsx';
    $targetfolder =  $folderMap."".$fileLocation;
    move_uploaded_file($_FILES['upload_file']['name'], $targetfolder);

    $writer = WriterFactory::create(Type::XLSX); // for XLSX files

    $fileName= $targetfolder;
    $writer->openToFile($fileName); // write data to a file or to a PHP stream
    // $writer->openToBrowser($fileName); // stream data directly to the browser

    $singleRow =['Sr No','Header Name','Abbreviation','Header Type','Sequence','Upload Status'];
    $writer->addRow($singleRow); // add a row at a time


   
    if (($pathinfo['extension'] == 'xlsx' || $pathinfo['extension'] == 'xls') && $_FILES['upload_file']['size'] > 0 ) { //check if file is an excel file && is not empty
        $inputFileName = $_FILES['upload_file']['tmp_name'];  // Temporary file name
        
        // Read excel file by using ReadFactory object.
        $reader = ReaderFactory::create(Type::XLSX);

        // Open file
        $reader->open($inputFileName);


        $count = 1;
        $flag = 0;



        // Number of sheet in excel file
        foreach ($reader->getSheetIterator() as $sheet) {
            // Number of Rows in Excel sheet
            foreach ($sheet->getRowIterator() as $row) {
                // It reads data after header. In the my excel sheet, header is in the first row.
                if ($count > 1) {  if(!empty($row[0])){


//---------------------------------------------------------------------------------------------------------------                  

    
        $Inserting_StaffQualification = mysqli_query($mysqli,"Insert into result_exam_header
        (Header_Name, Header_Type, sectionmaster_Id, abbr, sequence) 
        Values
        ('".htmlspecialchars($row[1], ENT_QUOTES)."', '".htmlspecialchars($row[3], ENT_QUOTES)."', '$SectionMaster_Id', '".htmlspecialchars($row[2], ENT_QUOTES)."', '".htmlspecialchars($row[4], ENT_QUOTES)."')");

            

        if(mysqli_error($mysqli)){
            $displayMessage[]  = $row[1].' : Query Failed';
            $uploadMessage = "Query Failed";
        }else{
            $displayMessage[]  = $row[1].' : Exam Header Added';
            $uploadMessage = "Exam Header Added";    
        }

            
        $multipleRows=[
            [$row[0],$row[1],$row[2],$row[3],$uploadMessage],
        ];
        $writer->addRows($multipleRows); // add multiple rows at a time

        
   
    unset($uploadMessage);
//----------------------------------------------------------------------------------------------------------------------
                   

        }}
        $count++;
        }

        }




        }


        $writer->close();


        $res['UploadedFilePath'] = $fileLocation;
        $res['displayMessage'] = $displayMessage;
        echo json_encode($res);

}
//-----------------------------------------------------------------------------------------------------------------------
