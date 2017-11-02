<?php
/**
 * Created by PhpStorm.
 * User: olgakolos
 * Date: 02.11.17
 * Time: 19:33
 */
namespace Controller;
use Core\Database;
class CoursesController
{
    public function get()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $request = $_GET['chosenDate'];
            if($request >= 1995-03-29 and $request <= date('Y-m-d', time())) {
                $dataSql = "SELECT d.d_date Date, c.c_name Currency, dc.dc_scale Scale, dc.dc_value Rate 
FROM m2m_date_currencies dc 
JOIN date d ON dc.d_id = d.d_id 
JOIN currencies c ON dc.c_id = c.c_id WHERE d.d_date LIKE \"$request%\""; //get data from DB on input date
                $dataResults = Database::getInstance()->selectObj($dataSql);
                if($dataResults != []) { //if there are data on input date in DB
                    $results = $dataResults;
                    foreach ($dataResults as $res) {
                        $results = array_merge($results, [clone($res)]); //double data to change the second part of array on reverse course
                    }
                    $count = count($results);
                    foreach ($results as $key => $result) {
                        if ($key < $count / 2) {
                            $results[$key]->Currency = $result->Currency . "-BYN";
                        } else { //change the second part of array on reverse course
                            $results[$key]->Currency = "BYN-" . $result->Currency;
                            $results[$key]->Rate = $results[$key]->Scale / $result->Rate;
                            $results[$key]->Scale = 1;
                            if ($results[$key]->Rate < 1) {//if we have a small value of the rate, we increase it until it'll become > 1
                                while ($results[$key]->Rate < 1) {
                                    $results[$key]->Scale = 10 * $results[$key]->Scale;
                                    $results[$key]->Rate = 10 * $results[$key]->Rate;
                                }
                            }
                            $results[$key]->Rate = (string)round($results[$key]->Rate, 4); //set readable form for course value
                            $results[$key]->Scale = (string)$results[$key]->Scale;
                        }
                    }
                    $dataJson = json_encode($results);
                    $dataJson = str_replace("\"},{\"", "\"},\r\n{\"", $dataJson); //set the line transfer between the data
                    echo nl2br($dataJson);
                }
                else {
                    $_SESSION['message'] = "$request. Текущей даты в базе нет. Сначала запросите данные в форме выше.";
                    header("Location: /");
                }
            }
            else {
                $_SESSION['message'] = "Дата должна быть в диапазоне от 1995-03-29 по текущую дату";
                header("Location: /");
            }
        }
    }
}