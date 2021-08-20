<?php

namespace App\Controllers;

use \App\Models\Statistic;
use \App\Models\Other;


class Statistics extends \Core\Controller
{
    // Get users statistic information by years
    public function activeUsersAction()
    {
        header('Content-Type: application/json');
        $years_statistatics = Other::getValueByID(13)->string_value;
        $max_year = date("Y");
        $user_statistics = array();
        for ($year = $max_year - ($years_statistatics - 1); $year <= $max_year; $year++) {
            $user_statistics[$year]['active_users'] = Statistic::getActiveUsersCount($year, $year + 1)[0]['active_users'];
            $user_statistics[$year]['total_users']  = Statistic::getTotalUsersCount($year, $year + 1)[0]['total_users'];
        }
        echo json_encode($user_statistics);
    }


    // Get users information
    public function newUsersAction()
    {
        header('Content-Type: application/json');
        $monthNum = Other::getValueByID(14)->string_value;
        $startDate = date("Y-m-01",strtotime("-$monthNum month"));
        $currentDate = date("Y-m-d"); // Current date
        $newUsers = Statistic::getNewUsersByDate($startDate,$currentDate)[0]['new_users'];
        echo json_encode([
            'newUsers' => $newUsers,
            'monthNum' => $monthNum
        ]);
    }


    // Get orders information
    public function newOrdersAction()
    {
        header('Content-Type: application/json');
        $monthNum = Other::getValueByID(15)->string_value;
        $startDate = date("Y-m-01",strtotime("-$monthNum month"));
        $currentDate = date("Y-m-d"); // Current date
        $newOrders = Statistic::getNewOrdersByDate($startDate,$currentDate)[0]['new_orders'];
        echo json_encode([
            'newOrders' => $newOrders,
            'monthNum' => $monthNum
        ]);
    }


    // Get information about sold products
    public function soldProductsAction()
    {
        header('Content-Type: application/json');
        $monthNum = Other::getValueByID(16)->string_value;
        $startDate = date("Y-m-01",strtotime("-$monthNum month"));
        $currentDate = date("Y-m-d"); // Current date
        $soldProducts = Statistic::getSoldProductsByDate($startDate,$currentDate)[0]['sold_products'];
        echo json_encode([
            'soldProducts' => $soldProducts,
            'monthNum' => $monthNum
        ]);
    }

    
    //Get categories information
    public function categoriesStatsAction()
    {
        header('Content-Type: application/json');
        echo json_encode(Statistic::getCategoriesStatistics());
    }


         
}
