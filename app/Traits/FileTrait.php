<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Rap2hpoutre\FastExcel\FastExcel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf as DompdfWriter;

trait FileTrait
{
    /**
     * Uploads a file to the specified directory and returns the file path.
     *
     * @param mixed $file The file to upload.
     * @param string $directory The directory where the file should be stored.
     *
     * @return string The file path of the uploaded file.
     */
    protected function uploadFile(mixed $file, string $directory): string
    {
        $fileName = $this->getFileName($file);

        $realPath = $directory . $fileName;

        Storage::disk('public')->put($realPath, file_get_contents($file));

        $filePath   = 'storage' . $realPath;


        $request = request();
        // Add the uploaded file path to the request to enable file cleanup in case of exceptions
        $request->merge(['uploadedFiles' => array_merge($request->input('uploadedFiles', []), [$filePath])]);

        return $filePath;
    }

    public static function generateExcelFile($collection, $repository, $fileName): string
    {
        $repository = 'storage' . $repository;
        if (!File::exists($repository)) {
            File::makeDirectory($repository, 0777, true);
        }

        $filePath = $repository . Carbon::now()->format('Y_m_d_u') . '_' . $fileName;

        $fastExcel = new FastExcel($collection);
        $fastExcel->export(public_path($filePath));

        $spreadsheet = IOFactory::load(public_path($filePath));
        $sheet = $spreadsheet->getActiveSheet();

        $headerRange = 'A1:' . $sheet->getHighestColumn() . '1';
        $sheet->getStyle($headerRange)
            ->getFont()
            ->setBold(true)
            ->getColor()
            ->setRGB('FFFFFF');
        $sheet->getStyle($headerRange)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('0000FF');
        $sheet->getStyle($headerRange)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle($headerRange)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $dataRange = 'A2:' . $sheet->getHighestColumn() . $sheet->getHighestRow();
        $sheet->getStyle($dataRange)
            ->getFont()
            ->setSize(12);
        $sheet->getStyle($dataRange)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle($dataRange)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

            foreach ($sheet->getColumnIterator() as $column) {
            $columnIndex = $column->getColumnIndex();

            // Disable auto-sizing and set a fixed width
            $sheet->getColumnDimension($columnIndex)->setAutoSize(false);
            $sheet->getColumnDimension($columnIndex)->setWidth(20); // Adjust the value to make it larger than text width
        }


        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save(public_path($filePath));

        return $filePath;
    }

    public static function convertExcelToPdf(string $excelFilePath): string
    {
        $outputPdfPath = str_replace('.xlsx', '.pdf', $excelFilePath);

        $spreadsheet = IOFactory::load($excelFilePath);
        $sheet = $spreadsheet->getActiveSheet();

        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setPaperSize(PageSetup::PAPERSIZE_A4);
        $pageSetup->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
        $pageSetup->setFitToWidth(1);
        $pageSetup->setFitToHeight(1);

        $sheet->getPageMargins()->setTop(0);
        $sheet->getPageMargins()->setBottom(0);
        $sheet->getPageMargins()->setLeft(0);
        $sheet->getPageMargins()->setRight(0);

        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setWidth(30.5);
        }
        foreach ($sheet->getRowIterator() as $row) {
            $sheet->getRowDimension($row->getRowIndex())->setRowHeight(25);
        }

        foreach ($sheet->getColumnIterator() as $column) {
            $columnIndex = $column->getColumnIndex();

            $sheet->getStyle($columnIndex . '1:' . $columnIndex . $sheet->getHighestRow())
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }

        $pdfWriter = new DompdfWriter($spreadsheet);
        $pdfWriter->setPaperSize(PageSetup::PAPERSIZE_A4);
        $pdfWriter->setOrientation(PageSetup::ORIENTATION_PORTRAIT);

        $pdfWriter->save($outputPdfPath);

        return $outputPdfPath;
    }

    /**
     * Generates a unique file name based on the current timestamp and the original file name.
     *
     * @param object $file The file object.
     *
     * @return string The generated file name.
     */
    protected function getFileName(object $file): string
    {
        return  Carbon::now()->format('Y_m_d_u') . '_' . $file->getClientOriginalName();
    }

    /**
     * Deletes a file from the public directory.
     *
     * @param string $fileName The file name or path to delete.
     *
     * @return bool Returns true if the file was successfully deleted, or false if the file does not exist or couldn't be deleted.
     */
    protected function deleteFile($fileName): bool
    {
        if (file_exists(public_path($fileName))) {
            unlink(public_path($fileName));
            return true;
        }
        return false;
    }

    /**
     * Retrieves the file extension from the given file path.
     *
     * @param string $filePath The file path from which to extract the extension.
     *
     * @return string The file extension, or an empty string if the file extension couldn't be determined.
     */
    protected function getFileExtension(string $filePath): string
    {
        $infoPath = pathinfo(public_path($filePath));

        return $infoPath['extension'];
    }
}
