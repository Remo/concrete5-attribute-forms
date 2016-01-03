<?php
namespace Concrete\Package\AttributeForms\Service\Excel;

use PHPExcel_Style_Border,
    PHPExcel_Style_Fill,
    PHPExcel_IOFactory,
    PHPExcel_Style,
    PHPExcel_Cell,
    PHPExcel,
    User;

class Export
{
    /** @var PHPExcel */
    private $phpExcel = null;
    
    private $tabid       = 0; //id of tabulation index on excel file
    private $startRow    = 1;

    public function __construct()
    {
        $this->phpExcel = new PHPExcel();
    }

    /**
     * Set Line number from where the insertion will start
     * @param int $startRow
     */
    public function setStartRow($startRow)
    {
        $this->startRow = intval($startRow);
    }

    private function setFileProprietes()
    {
        $u = new User();
        // Set document properties
        $this->phpExcel->getProperties()
                            ->setCreator($u->getUserName())
                            ->setLastModifiedBy($u->getUserName());
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->phpExcel->setActiveSheetIndex(0);
    }

    /**
     * Download Excel File
     * @param string $fileName
     */
    public function download($fileName)
    {
        ob_clean();
        set_time_limit(0);
        $this->setFileProprietes();

        if (!ends_with($fileName, '.xlsx')) {
            $fileName .= '.xlsx';
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header("Content-Title: " . $this->phpExcel->getActiveSheet()->getTitle());
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header("Cache-control: private");
        header('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpExcel, 'Excel2007');
        $objWriter->save('php://output');
        die();
    }

    /**
     * Save The Excel file under the given directory
     * @param string $fileName
     * @param string $dirPath
     */
    public function saveAs($fileName, $dirPath)
    {
        $this->setFileProprietes();

        if (!ends_with($fileName, '.xlsx')) {
            $fileName .= '.xlsx';
        }

        if (!ends_with($dirPath, '/') && !ends_with($dirPath, '\\')) {
            $dirPath .= '/';
        }

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpExcel, 'Excel2007');
        $objWriter->save($dirPath.$fileName);
    }

    /**
     * @param string $tabName tabulationName
     * @param array $headers columns names
     * @param array $data rows
     * @param boolean $createNewTab boolean indicate that we like to create New tab
     *
     * @return array(PHPExcel_Worksheet, int)
     * @throws \PHPExcel_Exception
     */
    public function addTabContent($tabName = "", array $headers = array(), array $data = array() , $createNewTab = false)
    {
        if ($createNewTab == true) {
            $this->phpExcel->createSheet();
            $this->tabid++;
        }

        $this->phpExcel->setActiveSheetIndex($this->tabid);
        $activeSheet = $this->phpExcel->getActiveSheet();
        $activeSheet->setTitle($tabName, false);

        $rowCount = $this->startRow;
        $contentStartRow = $this->startRow;

        if (!empty($headers)) {
            foreach ($headers as $i => $value) {
                $activeSheet->setCellValueByColumnAndRow($i, $rowCount, $value);
                //attribute width dynamique to columns
                for ($i = 0; $i < sizeof($headers); $i++) {
                    $activeSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setAutoSize(true);
                }
            }
            
            $headerStyle  = new PHPExcel_Style();
            $headerStyle->applyFromArray(array(
                'font' => array('bold' => TRUE, 'color' => array('rgb' => 'FFFFFF')),
                'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '428BCA')),
                'borders' => array(
                    'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                )
            ));

            $indexLastCell = PHPExcel_Cell::stringFromColumnIndex(sizeof($headers)-1);
            $activeSheet->duplicateStyle($headerStyle, "A{$rowCount}:{$indexLastCell}{$rowCount}");
            
            $rowCount++;
            $contentStartRow++;
        }
        
        foreach ($data as $row) {
            foreach ($row as $i => $value) {
                $activeSheet->setCellValueByColumnAndRow($i, $rowCount, $value);
            }
            $rowCount++;
        }

        $contentStyle  = new PHPExcel_Style();
        $contentStyle->applyFromArray(array(
            'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            )

        ));

        $indexLastCell = PHPExcel_Cell::stringFromColumnIndex($i);
        $lastRowIndex       = $rowCount - 1;
        $activeSheet->duplicateStyle($contentStyle, "A{$contentStartRow}:{$indexLastCell}{$lastRowIndex}");

        return array($activeSheet, $rowCount);
    }

    /**
     *
     * @return PHPExcel
     */
    public function getPHPExcelObject()
    {
        return $this->phpExcel;
    }
}