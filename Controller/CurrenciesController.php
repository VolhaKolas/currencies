<?php
/**
 * Created by PhpStorm.
 * User: olgakolos
 * Date: 02.11.17
 * Time: 19:22
 */
namespace Controller;
use Core\Database;
use Exception;
class CurrenciesController
{
    public function get() {
        if(isset($_SESSION['message']) and $_SESSION['message'] != null) {
            $message = $_SESSION['message']; //this var includes input errors
            $_SESSION['message'] = null;
        }
        else {
            $message = '';
        }
        require_once "views/currencies.php";
    }

    public function post() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $request = $_POST['chosenDate'];
            if($request >= 1995-03-29 and $request <= date('Y-m-d', time())) {
                $arrayCurrencies = ['USD', 'EUR', 'RUB'];
                $currenciesCountSql = "SELECT COUNT(*) count FROM currencies";
                $currenciesCount = Database::getInstance()->select($currenciesCountSql)[0]['count']; //check DB on existence of currencies names
                if (0 == $currenciesCount) { //if there aren't currencies names in DB, enter them
                    foreach ($arrayCurrencies as $arrayCurrency) {
                        Database::getInstance()->insert('currencies', [
                            'c_id' => null,
                            'c_name' => $arrayCurrency
                        ]);
                    }
                }
                $url = "http://www.nbrb.by/API/ExRates/Rates?onDate=" . $request . "&Periodicity=0"; //url of Nat. Bank
                try {
                    $Headers = @get_headers($url);
                    if(preg_match("|200|", $Headers[0])) { //написать проверку на существование url
                        $currencies = file_get_contents($url);
                        $currencies = json_decode($currencies);
                        $date = $currencies[0]->Date;
                        $dateCountSql = "SELECT COUNT(*) count FROM date WHERE d_date = \"$date\"";// check DB on existence of input date
                        $dateCount = Database::getInstance()->select($dateCountSql)[0]['count'];
                        if (0 == $dateCount) { //if there isn't input date in DB, enter it
                            Database::getInstance()->insert('date', [ //вводим дату в базу
                                'd_id' => null,
                                'd_date' => $date
                            ]);
                            $dateIdSql = "SELECT d_id FROM date WHERE d_date = \"$date\"";
                            $dateId = Database::getInstance()->select($dateIdSql)[0]['d_id']; //id of input date
                            $currenciesIdsSql = "SELECT c_id, c_name FROM currencies";
                            $currenciesIds = Database::getInstance()->select($currenciesIdsSql);//ids of currencies names
                            foreach ($currencies as $currency) {
                                foreach ($currenciesIds as $currenciesId) {
                                    if ($currency->Cur_Abbreviation == $currenciesId['c_name']) {//if abbreviation from Nat.Bank url == currency name
                                        Database::getInstance()->insert('m2m_date_currencies', [// add Rates to DB on input date
                                            'dc_id' => null,
                                            'd_id' => $dateId,
                                            'c_id' => $currenciesId['c_id'],
                                            'dc_value' => $currency->Cur_OfficialRate,
                                            'dc_scale' => $currency->Cur_Scale
                                        ]);
                                    }
                                }
                            }
                            $_SESSION['message'] = "Данные успешно добавлены";
                        } else {
                            $_SESSION['message'] = "Данная дата уже есть в базе";
                        }
                    }
                    else {
                        throw new Exception('Нет связи с сайтом нац.банка');
                    }
                }
                catch (\Exception $e) {
                    $_SESSION['message'] = "Произошла ошибка: " . $e->getMessage();
                }
            }
            else {
                $_SESSION['message'] = "Дата должна быть в диапазоне от 1995-03-29 по текущую дату";
            }
        }
        header("Location: /");
    }
}