<?php
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
    public $comparator;

    public function __construct($inputFileName, $outputFileName, $dataType, $wayOfSort){ //, $comparator
        $this->inputFileName = $inputFileName;
        $this->outputFileName = $outputFileName;
        $this->dataType = $dataType;
        $this->wayOfSort = $wayOfSort;
    }

    public function ifAllDataInputAndTrue($argv, $argc) {
        try {
            if (!isset($argv) || $argc !== 5) {
                throw new Exception ("Введите имя входного файла, имя выходного файла, режим сортировки, а также тип сортируемых данных");
            }
            $arrInputName = explode(".", $this->inputFileName);
            if ((preg_match("/[a-z0-9]{3,20}/i", $arrInputName[0]) == 0) || ($arrInputName[1] !== "txt")) {
                throw new Exception("Имя файла должно содержать только буквы латинского алфавита и цифры. Формат файла должен быть txt. Input-файл должен находиться в директории filesForSort");
            }
            $arrOutputName = explode(".", $this->outputFileName);            
            if ((preg_match("/[a-z0-9]{3,20}/i", $arrOutputName[0]) == 0) || ($arrOutputName[1] !== "txt")) {
                throw new Exception("Имя итогового файла должно содержать только буквы латинского алфавита и цифры. Формат файла должен быть txt");
            }
            if ($this->dataType !== "integer" && $this->dataType !== "string") {
                throw new Exception("Тип данных может быть integer или string");
            }
            if ($this->wayOfSort !== "increase" && $this->wayOfSort !== "decrease") {
                throw new Exception("Режим сортировки может быть increase или decrease");
            }

            $uploaddir = __DIR__ . "/filesForSort/";
            $ourFile = $uploaddir.$this->inputFileName;
            if (!file_exists($ourFile)) {
                throw new Exception("Файл с таким именем не существует!");
            }

            $arrForSort = array();
            if($handle = fopen($ourFile,"r+")){ 
                  while (!feof($handle)){ 
                    if ($this->dataType == "integer") { //
                        $arrForSort[] = (int)fgets($handle, 4096); // Convert string to INTEGER
                    }
                    else {
                        $arrForSort[] = fgets($handle, 4096);
                    }
                    $this->countElem = $this->countElem + 1;
                  } 
                  fclose($handle); 
            } 
            
            if ($this->countElem > 100) {
                throw new Exception("Число сортируемых элементов не должно превышать 100!");
            }
            
            $comparator = $this->wayOfSort;
            $dataType = $this->dataType;
            $resultArray = $this->insertionSortWithParam($arrForSort, $comparator, $dataType);

            $file = __DIR__ . "/resultFiles/".$this->outputFileName;
            if ($dataType == "string") {
                file_put_contents($file, implode("", $resultArray)); // 
            }
            else {
                file_put_contents($file, implode("\n", $resultArray));
            } 
        }
        catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


    public function insertionSortWithParam($arrForSort, $comparator, $dataType) {
        if ($comparator == "increase") {
            for($i = 1; $i < count($arrForSort); $i++) {
                $rightValue = $arrForSort[$i]; 
                $leftValue = $i - 1; 
                while($leftValue >= 0 && $arrForSort[$leftValue] > $rightValue) {
                    $arrForSort[$leftValue+1] = $arrForSort[$leftValue];
                    $leftValue--;
                }
                $arrForSort[++$leftValue] = $rightValue;
            }
        }
        else { //if ($comparator == "decrease")
            for($i = 1; $i < count($arrForSort); $i++) {
                $rightValue = $arrForSort[$i]; 
                $leftValue = $i - 1; 
                while($leftValue >= 0 && $arrForSort[$leftValue] <= $rightValue) {
                    $arrForSort[$leftValue+1] = $arrForSort[$leftValue];
                    $leftValue--;
                }
                $arrForSort[++$leftValue] = $rightValue;
            }
        }
        return $arrForSort;
    }
}

class FileDataSortView {
    public $fileDataSortModel;
    public function __construct ($inputFileName, $outputFileName, $dataType, $wayOfSort) { //, $comparator
        $this->fileDataSortModel = new FileDataSortModel ($inputFileName, $outputFileName, $dataType, $wayOfSort); 
    }
    public function generateView($argv, $argc) {
        $result = $this->fileDataSortModel->ifAllDataInputAndTrue($argv, $argc);
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
    public function fileDataSortAction($argv, $argc) {
        $this->fileDataSortView->generateView($argv, $argc);
    }

}

$objSort = new FileDataSortController($inputFileName, $outputFileName, $dataType, $wayOfSort); 
$objSort->fileDataSortAction($argv, $argc);