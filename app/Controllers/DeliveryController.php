<?php



namespace App\Controllers;



use App\Controllers\BaseController;

use App\Models\UserModel;





class DeliveryController extends BaseController
{

  public function test()
  {

    $db = db_connect();
    $userModel = new UserModel();
    $debug = true;
    $userid = 120;
    //$userid = 36;
    $requestDate = '2024-12-30'; // Request DATA
    $tanggal = '2024-02-06'; //'2023-12-18';
    // '2023-12-07 00:15:33'; // Start Push - empty to push now
    $dailypush = 3;

    $user = $userModel->where('id', $userid)->first();
    if (empty($user)) {

      return "User Not Exist";
    }

    $brand = $user['brand'];
    if ($brand == '') {
      return "Brand Not Exist";
    }

    $quota = $user['quota'];

    if ($quota == '') {

      return "Quota Not Exist";
    }

    //$quota = 10;

    $showroom = $user['showroom'];

    //$showroom = '';

    if ($showroom == '') {

      $showroom = 0;
    }



    $quota = 10;





    // $brand = 17;

    $quota = 5;

    // $showroom = 1181;





    if ($debug) {

      echo '<h1>DEBUG MODE</h1>';

      echo "<pre>";

      print_r($user);

      echo "</pre>";





      //$newquery = "SELECT count(*) as total from push_list where userid = '".$userid."' group by userid ";

      //$pushinfo = $db->query($newquery)->getRow();



      //echo "<pre>";

      //print_r($pushinfo);

      //echo "</pre>";



      //exit();

      //echo $pushinfo['total'];

      //if($pushinfo->total > 0){

      //echo '<h1>WARNING user Already in list ('.$pushinfo->total.')</h1>';

      // }

    }







    $arrException = $this->getExceptionList($userid, $showroom);



    $modelException = $this->getModelException($userid);







    if ($tanggal == '') {

      $tanggal = date('Y-m-d');
    } else {

      $tanggal = strtotime($tanggal);

      $tanggal = date('Y-m-d', $tanggal);
    }



    // Set start and end times

    $startTime = '09:00:00';

    $endTime = '21:00:00';

    // Set the number of days to generate timestamps for

    $numberOfDays = 90;



    // Generate 2 random timestamps for each day in the next 30 days

    $timestamps = [];

    for ($day = 0; $day < $numberOfDays; $day++) {

      $date = date('Y-m-d', strtotime("+$day day", strtotime($tanggal)));



      // total Push daily

      for ($y = 0; $y < $dailypush; $y++) {

        $data[]['tanggal'] = $this->randomTimestamp("$date $startTime", "$date $endTime");
      }
    }







    //$sql = "SELECT id from leads limit 15,100";

    // Wuling = 46

    //$sql = "SELECT id, variant, `create` from leads where brand = 46 and `create`>= '2022-01-01' and model like '%Fortuner%' order by `create` ASC LIMIT 56;";

    //$sql = "SELECT id, variant, `create` from leads where brand = 46 and `create`>= '2022-01-01' order by `create` ASC LIMIT 56;";



    $sql = "SELECT `leads`.`id`, `leads`.`city` , `brands`.`brand`, `leads`.`model`, `leads`.`variant`, `leads`.`create` 

FROM `leads` 

LEFT JOIN `brands` ON `brands`.`id` = `leads`.`brand` 

WHERE leads.brand = '" . $brand . "' AND leads.`status`= 0  

AND leads.`create`<= '" . $requestDate . "' ";



    if (count($arrException) > 0) {

      $sql .= "AND leads.id NOT IN ('" . implode("','", $arrException) . "') ";
    }

    if (count($modelException) > 0) {

      $sql .= "AND leads.model IN ('" . implode("','", $modelException) . "') ";
    }







    // Check model

    // $sql .="AND leads.model like '%Xpande%'"; 







    // $sql .="AND `leads`.`model` like '%cx 5%'";

    // $sql .="AND `leads`.`model` like '%cx5%'";



    // Check if same Showroom

    /*

if($showroom > 0){

$sql .="AND leads.id NOT IN (SELECT `prospek`.`leadsid` FROM `prospek` WHERE `prospek`.`showroom` = '".$showroom."') ";



}

*/













    $sql .= "ORDER BY `leads`.`create` DESC LIMIT " . $quota;



    if ($debug) {

      echo "<br />" . $sql;
    }



    $query = $db->query($sql)->getResultArray();









    // echo $data[0];

    $newarr = [];

    for ($x = 0; $x < count($query); $x++) {

      $newarr[$x]['tanggal'] = $data[$x]['tanggal'];

      $newarr[$x]['leadsid'] = $query[$x]['id'];

      $newarr[$x]['userid'] = $userid;

      $newarr[$x]['nama'] = $user['nama'];

      $newarr[$x]['data'] = $query[$x]['create'];

      $newarr[$x]['brand'] = $query[$x]['brand'] . " " . $query[$x]['model'] . " " . $query[$x]['variant'];

      $newarr[$x]['lokasi'] = $query[$x]['city'];



      if ($debug) {



        echo "<br />" . "INSERT INTO `prospek` 

SET `prospek`.`userid`='" . $userid . "', 

`prospek`.`leadsid` = '" . $query[$x]['id'] . "', 

`prospek`.`showroom` = '1181', 

`prospek`.`created_at` = NOW(), 

`prospek`.`updated_at` = NOW();";
      } else {

        $saveData = $this->insertPushList($userid, $query[$x]['id'], $data[$x]['tanggal']);
      }
    }





    // Insert Database;





    echo "<pre>";

    print_r($newarr);

    echo "</pre>";





    // Output the generated timestamps

    //  foreach ($timestamps as $date => $dayTimestamps) {

    //      echo "Date: $date, Timestamp 1: {$dayTimestamps['timestamp1']}, Timestamp 2: {$dayTimestamps['timestamp2']}" . "<br />";

    //  }





  }





  private function randomTimestamp($startTime, $endTime)
  {

    $startTimestamp = strtotime($startTime);

    $endTimestamp = strtotime($endTime);



    $randomTimestamp = mt_rand($startTimestamp, $endTimestamp);



    return date('Y-m-d H:i:s', $randomTimestamp);
  }



  private function insertPushList($userid, $leadsid, $tanggal)
  {



    $db = db_connect();

    $sql = "INSERT INTO `push_list` 

SET `userid` = '" . $userid . "' , `leadsid` = '" . $leadsid . "' , tanggal = '" . $tanggal . "'";

    $query = $db->query($sql);

    if ($query) {

      return true;
    } else {

      return false;
    }
  }



  private function getModelException($userid)
  {

    $db = db_connect();

    $sql = "SELECT `model` FROM `invoice` where `userid` = " . $userid . " ORDER BY `id` desc LIMIT 1";





    //$listException = $arrUsers['model'];

    $totalRecord = $db->query($sql)->getNumRows();

    echo "<br /> Total = " . $totalRecord;



    if ($totalRecord > 0) {

      $arrUsers = $db->query($sql)->getResultArray();

      echo "<pre>";

      print_r($arrUsers);

      echo "</pre>";



      $arrList = explode(',', $arrUsers[0]['model']);

      echo "<pre>";

      print_r($arrList);

      echo "</pre>";
    } else {

      $arrList = [];
    }



    //exit();

    return $arrList;
  }





  private function getExceptionList($userid, $showroom)
  {



    $arrException = [];

    $arruserid = [];



    // get all userid from the same showroom

    $db = db_connect();



    // get all id based on same showroom

    $sql = "SELECT id from users where showroom = '" . $showroom . "'";

    $arrUsers = $db->query($sql)->getResultArray();



    echo "<br /> Total User with same Showroom : " . $db->query($sql)->getNumRows();



    foreach ($arrUsers as $user) {

      $arruserid[] = $user['id'];
    }



    //echo "<pre>";

    //print_r($arruserid);

    //echo "<pre>";













    // GET ALL leads by showroom

    //$string="1,2,3,4,5";

    //$array=array_map('intval', explode(',', $string));

    $array = implode("','", $arruserid);



    $sql = "SELECT leadsid from prospek where userid IN ('" . implode("','", $arruserid) . "')";



    //echo "<br /> " . $sql;



    $arrList = $db->query($sql)->getResultArray();



    foreach ($arrList as $list) {

      // Add showroom to Exception;

      $arrException[] = $list['leadsid'];
    }

    echo "<br /> Total on Prospek : " . $db->query($sql)->getNumRows();

    //   echo "<pre>";

    //   print_r($arrException);

    //   echo "<pre>";





    // get ArrayList from push_list

    $sql = "SELECT leadsid from push_list where userid IN ('" . implode("','", $arruserid) . "')";

    $arrPush = $db->query($sql)->getResultArray();



    //echo "<br />" .  $sql;

    echo "<br /> Total on Pushlist : " . $db->query($sql)->getNumRows();



    foreach ($arrPush as $list) {

      // Add showroom to Exception;

      $arrException[] = $list['leadsid'];
    }







    // echo "<pre>";

    // print_r($arrException);

    // echo "<pre>";







    /*

$sql = "SELECT `prospek`.`leadsid` FROM `prospek` WHERE `prospek`.`userid` = '".$userid."'";

$query = $db->query($sql)->getResultArray();

*/



    return $arrException;
  }
}
