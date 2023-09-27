<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Shared;

use App\Service\AppServices;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PdfController extends Controller
{
    /**
     * @Route("/generate-attendees-pdf/", name="generate_attendees_pdf")
     */
    public function generatePdf(Request $request, AppServices $services)
    {
        $reference = ($request->query->get('reference')) == "" ? "all" : $request->query->get('reference');
        $event = ($request->query->get('event')) == "" ? "all" : $request->query->get('event');
        $eventdate = ($request->query->get('eventdate')) == "" ? "all" : $request->query->get('eventdate');
        $eventticket = ($request->query->get('eventticket')) == "" ? "all" : $request->query->get('eventticket');
        $user = ($request->query->get('user')) == "" ? "all" : $request->query->get('user');
        $organizer = ($request->query->get('organizer')) == "" ? "all" : $request->query->get('organizer');
        $datefrom = ($request->query->get('datefrom')) == "" ? "all" : $request->query->get('datefrom');
        $dateto = ($request->query->get('dateto')) == "" ? "all" : $request->query->get('dateto');
        $status = 1;
        $paymentgateway = ($request->query->get('paymentgateway')) == "" ? "all" : $request->query->get('paymentgateway');

        $ordersQuery = $services->getOrders(array("reference" => $reference, "event" => $event, "eventdate" => $eventdate, "eventticket" => $eventticket, "user" => $user, "organizer" => $organizer, "datefrom" => $datefrom, "dateto" => $dateto, "status" => $status, "paymentgateway" => $paymentgateway))->getQuery();

// Export current orders query results into Excel / Csv
        $orders = $ordersQuery->getResult();

        // Fetch the HTML content of the table (replace 'your_template.html.twig' with your actual template)
        $html = $this->renderView('Dashboard/Shared/Order/pdf-manage-attendees-table.html.twig', [
            'orders' => $orders, // Replace with your data
        ]);

        // Create PDF options
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf
        $dompdf = new Dompdf($pdfOptions);

        // Load HTML content
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the PDF (choose to render as stream or attachment)
        $dompdf->render();

        // Generate the PDF response
        $pdfOutput = $dompdf->output();

        // Create a Symfony Response and set the content type to PDF
        $response = new Response($pdfOutput);
        $response->headers->set('Content-Type', 'application/pdf');

        // Optionally, set a custom filename for the PDF download
        $response->headers->set('Content-Disposition', 'inline; filename="orders.pdf"');

        return $response;
    }

    /**
     * @Route("/generate-attendees-excel/", name="generate_attendees_excel")
     */
    public function generateExcel(Request $request, AppServices $services)
    {
        $reference = ($request->query->get('reference')) == "" ? "all" : $request->query->get('reference');
        $event = ($request->query->get('event')) == "" ? "all" : $request->query->get('event');
        $eventdate = ($request->query->get('eventdate')) == "" ? "all" : $request->query->get('eventdate');
        $eventticket = ($request->query->get('eventticket')) == "" ? "all" : $request->query->get('eventticket');
        $user = ($request->query->get('user')) == "" ? "all" : $request->query->get('user');
        $organizer = ($request->query->get('organizer')) == "" ? "all" : $request->query->get('organizer');
        $datefrom = ($request->query->get('datefrom')) == "" ? "all" : $request->query->get('datefrom');
        $dateto = ($request->query->get('dateto')) == "" ? "all" : $request->query->get('dateto');
        $status = ($request->query->get('status')) == "" ? "all" : $request->query->get('status');
        $paymentgateway = ($request->query->get('paymentgateway')) == "" ? "all" : $request->query->get('paymentgateway');

        $ordersQuery = $services->getOrders(array("reference" => $reference, "event" => $event, "eventdate" => $eventdate, "eventticket" => $eventticket, "user" => $user, "organizer" => $organizer, "datefrom" => $datefrom, "dateto" => $dateto, "status" => $status, "paymentgateway" => $paymentgateway))->getQuery();

// Export current orders query results into Excel / Csv
        $orders = $ordersQuery->getResult();

        try {
            // Create a new Spreadsheet instance
            $spreadsheet = new Spreadsheet();

            // Get the active sheet
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers for your Excel file
            $headers = ['Full name', 'Date Booked', 'Status']; // Replace with your column names
            $columnIndex = 1;
            foreach ($headers as $header) {
                $sheet->setCellValueByColumnAndRow($columnIndex, 1, $header);
                $columnIndex++;
            }

            // Set data from your orders
            $rowIndex = 2; // Start from the second row (after headers)
            foreach ($orders as $order) {
                $sheet->setCellValueByColumnAndRow(1, $rowIndex, $order->getUser()->getCrossRoleName()); // Replace with actual fields
                $sheet->setCellValueByColumnAndRow(2, $rowIndex, $order->getCreatedAt()->format('D d M Y, h:i A'));
                $sheet->setCellValueByColumnAndRow(3, $rowIndex, $order->getStatusStringified());
                $rowIndex++;
            }

            // Format the cells (e.g., set alignment)
            $sheet->getStyle('A1:C1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Create a temporary file path to save the Excel file
            $tempFilePath = tempnam(sys_get_temp_dir(), 'excel_');
            $tempFilePath .= '.xlsx';

            // Save the Excel file to the temporary path
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempFilePath);

            // Create a Symfony BinaryFileResponse for downloading the saved Excel
            $response = new BinaryFileResponse($tempFilePath);

            // Set response headers for download
            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', 'attachment; filename="attendees.xlsx"');

            return $response;
        } catch (Exception $e) {
            // Handle any exceptions here
            return new Response('Error: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}