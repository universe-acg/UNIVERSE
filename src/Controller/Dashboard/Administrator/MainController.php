<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Administrator;

use App\Service\AppServices;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\LineChart;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends Controller
{

    /**
     * @Route("/", name="index", methods="GET")
     */
    public function index(AppServices $services)
    {

        // Tickets Sales By Date Line Chart
        $datefrom = date_format(new DateTime, "Y-m-01");
        $dateto = date_format(new DateTime, "Y-m-t");
        $ordersQuantityByDate = $services->getOrders(array("ordersQuantityByDateStat" => true, "order" => "ASC", "datefrom" => $datefrom, "dateto" => $dateto))->getQuery()->getResult();
        foreach ($ordersQuantityByDate as $i => $resultArray) {
            $ordersQuantityByDate[$i] = array_values($resultArray);
            $ordersQuantityByDate[$i][1] = DateTime::createFromFormat('Y-m-j', $ordersQuantityByDate[$i][1]);
            $ordersQuantityByDate[$i] = array_reverse($ordersQuantityByDate[$i]);
        }
        array_unshift($ordersQuantityByDate, [['label' => "Date", 'type' => 'date'], ['label' => "Tickets offered", 'type' => 'number']]);
        $ticketsSalesByDateLineChart = new LineChart();
        $ticketsSalesByDateLineChart->getData()->setArrayToDataTable($ordersQuantityByDate);
        $ticketsSalesByDateLineChart->getOptions()->setTitle("Tickets this month");
        $ticketsSalesByDateLineChart->getOptions()->setCurveType('function');
        $ticketsSalesByDateLineChart->getOptions()->setLineWidth(2);
        $ticketsSalesByDateLineChart->getOptions()->getLegend()->setPosition('none');

        return $this->render('Dashboard/Administrator/index.html.twig', [
            'ticketsSalesByDateLineChart' => $ticketsSalesByDateLineChart
        ]);
    }

}
