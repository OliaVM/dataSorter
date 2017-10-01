<?php
class ExceptionOfArrInputName extends Exception { }
class ExceptionOfArrOutputName extends Exception { }
class ExceptionOfDataType extends Exception { }
class ExceptionOfWayOfSort extends Exception { }
class ExceptionOfFileNameExist extends Exception { }
class ExceptionOfCountElem extends Exception { }

$inputFileName = $argv[1];
$outputFileName = $argv[2];
$dataType = $argv[3];
$wayOfSort = $argv[4];

class FileDataSortModel {
    public $wayOfSort;
    public $outputFileName;
    public $dataType;
    public $inputFileName;
    public $countElem = 0;
    public function __construct($inputFileName, $outputFileName, $dataType, $wayOfSort){
        $this->inputFileName = $inputFileName;
        $this->outputFileName = $outputFileName;
        $this->dataType = $dataType;
        $this->wayOfSort = $wayOfSort;
    }

    public function ifAllDataInput() {
        try {
            if (isset($argv) || $argc = 5) {
                $this->ifAllDataTrue();      
            } 
            else {
                throw new Exception ("Введите имя входного файла, имя выходного файла, режим сортировки, а также тип сортируемых данных");
            }
        }
        catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function ifAllDataTrue() { 
        try {
            $arrInputName = explode(".", $this->inputFileName);
            if (preg_match("/[a-z0-9]{3,20}/i", $arrInputName[0]) && $arrInputName[1] == "txt") {
               try {
                    $arrOutputName = explode(".", $this->outputFileName);
                    if (preg_match("/[a-z0-9]{3,20}/i", $arrOutputName[0]) && $arrOutputName[1] == "txt") {
                        try {
                                if ($this->dataType == "integer" || $this->dataType == "string") {
                                    try {
                                            if ($this->wayOfSort == "increase" || $this->wayOfSort == "decrease") {
                                                $this->fileDataInsertionSort();
                                            } else {
                                            throw new ExceptionOfWayOfSort("Режим сортировки может быть increase или decrease");
                                        }
                                    }
                                    catch (ExceptionOfWayOfSort $ex4) {
                                       throw $ex4;
                                    }

                                } else {
                                    throw new ExceptionOfDataType("Тип данных может быть integer или string");
                                }
                            }
                            catch (ExсeptionOfDataType $ex3) {
                               throw $ex3;
                            }
                    } else {
                        throw new ExceptionOfArrOutputName("Имя итогового файла должно содержать только буквы латинского алфавита и цифры. Формат файла должен быть txt");
                    }
                }
                catch (ExceptionOfArrOutputName $ex2) {
                   throw $ex2;
                }
            } else {
                throw new ExceptionOfArrInputName("Имя файла должно содержать только буквы латинского алфавита и цифры. Формат файла должен быть txt. Input-файл должен находиться в директории filesForSort");
            }
        }
        catch (ExceptionOfArrInputName $ex1) {
            throw $ex1;
        }
    }    


    public function fileDataInsertionSort() {
       	$uploaddir = __DIR__ . "/filesForSort/";
        $ourFile = $uploaddir.$this->inputFileName;
        try {
            if (file_exists($ourFile)) {
            $arrForSort = array();
           
                if($handle = fopen($ourFile,"r+")){ 
                      while (!feof($handle)){ 
                         $arrForSort[] = fgets($handle, 4096);
                         $this->countElem = $this->countElem + 1;
                      } 
                      fclose($handle); 
                } 
                try {
                    if ($this->countElem <= 100) {
                        if ($this->wayOfSort == "increase" && $this->dataType == "integer") {//a
                            $resultArray = $this->increaseInsertionIntSort($arrForSort);
                        }
                        else if ($this->wayOfSort == "decrease" && $this->dataType == "integer") {
                            $resultArray = $this->decreaseInsertionIntSort($arrForSort);
                        }
                        else if ($this->wayOfSort == "increase" && $this->dataType == "string") {
                            $resultArray = $this->increaseInsertionStrSort($arrForSort);
                        }
                        else {
                            $resultArray = $this->decreaseInsertionStrSort($arrForSort);
                        }
                        file_put_contents(__DIR__ . "/resultFiles/".$this->outputFileName, $resultArray);
                    }
                    else {
                        throw new ExceptionOfCountElem("Число сортируемых элементов не должно превышать 100!");
                    }
                }
                catch (ExceptionOfCountElem $exOfCountElem) {
                    throw $exOfCountElem;
                }
            }
            else {
                throw new ExceptionOfFileNameExist("Файл с таким именем не существует!");
            }
        }
        catch (ExceptionOfFileNameExist $exOfFileNameExist) {
             throw $exOfFileNameExist;
        }
    }
   
    public function increaseInsertionIntSort($arrForSort) {
        for($i = 1; $i < count($arrForSort); $i++) {
            $currentValue = $arrForSort[$i];
            $leftValue = $i - 1;
            while($leftValue >= 0 && $arrForSort[$leftValue] > $currentValue) {
                $arrForSort[$leftValue+1] = $arrForSort[$leftValue];
                $leftValue--;
            }
            $arrForSort[++$leftValue] = $currentValue;
        }
        return $arrForSort;
    }

    public function decreaseInsertionIntSort($arrForSort) {
        for($i = 1; $i < count($arrForSort); $i++)
        {
            $rightValue = $arrForSort[$i];
            $leftValue = $i - 1;
            while($leftValue >= 0 && $arrForSort[$leftValue] <= $rightValue)
            {
                $arrForSort[$leftValue+1] = $arrForSort[$leftValue];
                $leftValue--;
            }
            $arrForSort[++$leftValue] = $rightValue;
        }
        return $arrForSort;
    }

    public function decreaseInsertionStrSort($arrForSort) {
        for($i = 1; $i < count($arrForSort); $i++)
        {
            $rightValue = $arrForSort[$i];
            $leftValue = $i - 1;
            while($leftValue >= 0 && $arrForSort[$leftValue] <= $rightValue)
            {
                $arrForSort[$leftValue+1] = $arrForSort[$leftValue];
                $leftValue--;
            }
            $arrForSort[++$leftValue] = $rightValue;
        }
        return $arrForSort;
    }

    public function increaseInsertionStrSort($arrForSort) {
        for($i = 1; $i < count($arrForSort); $i++)
        {
            $rightValue = $arrForSort[$i];
            $leftValue = $i - 1;
            while($leftValue >= 0 && $arrForSort[$leftValue] > $rightValue)
            {
                $arrForSort[$leftValue+1] = $arrForSort[$leftValue];
                $leftValue--;
            }
            $arrForSort[++$leftValue] = $rightValue;
        }
        return $arrForSort;
    }
}

class FileDataSortView {
    public $fileDataSortModel;
    public function __construct ($inputFileName, $outputFileName, $dataType, $wayOfSort) {
        $this->fileDataSortModel = new FileDataSortModel ($inputFileName, $outputFileName, $dataType, $wayOfSort);
    }
    public function generateView() {
        $result = $this->fileDataSortModel->ifAllDataInput();
        if (gettype($result) !== "array") {
            echo $result;
        }
    }
}

class FileDataSortController {
    public $fileDataSortView;
    public function __construct ($inputFileName, $outputFileName, $dataType, $wayOfSort) {
        $this->fileDataSortView = new FileDataSortView($inputFileName, $outputFileName, $dataType, $wayOfSort);
    }
    public function fileDataSortAction() {
        $this->fileDataSortView->generateView();
    }

}

$objSort = new FileDataSortController($inputFileName, $outputFileName, $dataType, $wayOfSort);
$objSort->fileDataSortAction();
//php index.php "str.txt" "str_result_increase.txt" "string" "increase"
//php index.php str.txt str_result_decrease.txt string decrease
//php index.php "int.txt" "int_result_increase.txt" "integer" "increase"
//php index.php int.txt int_result_decrease.txt integer decrease