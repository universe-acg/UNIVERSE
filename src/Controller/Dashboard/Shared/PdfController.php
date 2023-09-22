<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Controller\Dashboard\Shared;
    
    use App\Service\AppServices;
    use Dompdf\Dompdf;
    use Dompdf\Options;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
            $status = ($request->query->get('status')) == "" ? "all" : $request->query->get('status');
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
    }