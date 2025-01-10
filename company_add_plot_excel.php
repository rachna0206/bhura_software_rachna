<?php
require_once 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// use ZipArchive;
function dynamic_excel_generate($result, $fileName)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = [];
    while ($field = $result->fetch_field()) {
        $headers[] = $field->name;
    }

    foreach ($headers as $colIndex => $header) {
        $sheet->setCellValueByColumnAndRow($colIndex + 1, 1, $header);
    }

    $rowIndex = 2;
    while ($row = $result->fetch_assoc()) {
        foreach ($headers as $colIndex => $header) {
            $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex, $row[$header]);
        }
        $rowIndex++;
    }

    $writer = new Xlsx($spreadsheet);
    $writer->save($fileName);
}

// Function to create and download ZIP file
function createAndDownloadZip($filePaths, $zipName = 'files.zip')
{
    if (!is_array($filePaths) || empty($filePaths)) {
        throw new Exception("No files provided to zip.");
    }

    $zip = new ZipArchive();
    $tempFile = tempnam(sys_get_temp_dir(), 'zip');

    if ($zip->open($tempFile, ZipArchive::CREATE) !== true) {
        throw new Exception("Could not create zip file.");
    }

    foreach ($filePaths as $filePath) {
        if (file_exists($filePath)) {
            $zip->addFile($filePath, basename($filePath));
        } else {
            throw new Exception("File not found: $filePath");
        }
    }

    $zip->close();

    header('Content-Type: application\zip');
    header('Content-Disposition: attachment; filename="' . $zipName . '"');
    header('Content-Length: ' . filesize($tempFile));


    // header('Content-Type: application\zip');
    // header('Content-Disposition: attachment; filename="export_' . urlencode($post_fields->Firm_Name) . '.zip"');
    // header('Content-Length: ' . filesize($zipPath));
    // header('Content-Disposition: attachment; filename="export_' . urlencode($post_fields->Firm_Name) . '.zip"');
    // header('Content-Length: ' . filesize($zipPath));



    readfile($tempFile);

    unlink($tempFile);
    foreach ($filePaths as $filePath) {
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    exit;
}












?>