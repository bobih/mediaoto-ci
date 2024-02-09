<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Controllers\FcmController;

class CronController extends BaseController
{


    public function index()
    {
        $enablePush = true;
        if ($enablePush) {
            $this->testPush();
        }
    }


    public function testPush()
    {

        $pushList = $this->getPushList();
        /*
        echo "<pre>";
        print_r($pushList);
        echo "<pre>";
        */
        
        for ($i = 0; $i < count($pushList); $i++) {
            echo $pushList[$i]['userid'] . "<br />";
            if($pushList[$i]['showroom'] == ''){
                $pushList[$i]['showroom'] = 0;
            }

            //save DB
            $saveDB = $this->insertLead($pushList[$i]['userid'], $pushList[$i]['leadsid'],$pushList[$i]['showroom']);
            if ($saveDB) {
                

                // Delete Push List
                $deleteList = $this->deletePushList($pushList[$i]['pushid']);
                if ($deleteList) {

                    //Push Notification
                    $fcm = new FcmController();
                    $title = '';
                    $payload = json_encode(array("page" => "ProspectList", "requestData" => "1"));
                    $msgType = '';

                    $push = $fcm->sendPushNotification($pushList[$i]['fcmtoken'], '', $payload,$msgType);
                }
            } else {
               // echo "Not Saving";
            }
        }
    }


    /**
     * 
     * @return array
     */
    public function getPushList()
    {
        $db = db_connect();
        //$tanggal = strtotime('2023-12-05 20:15:33');
        //$tanggal = date('Y-m-d H:i',$tanggal);

        $tanggal = date('Y-m-d H:i');

       // $tanggal = '2023-12-15 11:11:00';

        

        $sql = "SELECT `push_list`.`id` as pushid,  `users`.`showroom`, `users`.`fcmtoken`, `push_list`.`userid`, `push_list`.`leadsid` 
                FROM `push_list` 
                LEFT JOIN `users` on `users`.`id` = `push_list`.`userid` 
            WHERE DATE_FORMAT(`push_list`.`tanggal`,'%Y-%m-%d %H:%i') = '" . $tanggal . "'";

        //echo $sql;
       // exit();
        //return $sql;
        $query = $db->query($sql)->getResultArray();
        return $query;


    }


    /**
     * 
     * @return array
     */
    private function getPushUser($userid)
    {
        /*
        $db = db_connect();
        if ($userid) {
            $sql = "SELECT `fcmtoken`, `id`  FROM `users` WHERE `users`.`id`='36'";

        } else {
            $sql = "SELECT `fcmtoken`, `id`  FROM `users` ";

        }
        $query = $db->query($sql)->getResultArray();
        return $query;
        */
        $sql = "SELECT `fcmtoken`, `id`  FROM `users` WHERE `users`.`id`='36'";
        return $sql;
    }




    /**
     * @return int userid
     */
    private function getDataUser($user)
    {

        $db = db_connect();
        $sql = "SELECT `leads`.`id` FROM `leads` 
        WHERE `leads`.`id` NOT IN 
                (SELECT `prospek`.`leadsid` FROM `prospek` WHERE `prospek`.`userid` = '" . $user . "') 
            AND `leads`.`id` BETWEEN 1 AND 1000
        ORDER BY RAND() 
        LIMIT 1";

        $query = $db->query($sql)->getRow();
        return $query->id;
    }

    /**
     * 
     * @return bool
     */
    private function insertLead($userid, $leadsid,$showroom)
    {
        $db = db_connect();
        $sql = "INSERT INTO `prospek` 
                SET `prospek`.`userid`='" . $userid . "', 
                    `prospek`.`leadsid` = '" . $leadsid . "', 
                    `prospek`.`showroom` = '" . $showroom . "', 
                    `prospek`.`created_at` = NOW(), 
                    `prospek`.`updated_at` = NOW()";
        $query = $db->query($sql);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function deletePushList($pushid)
    {
        $db = db_connect();
        $sql = "DELETE FROM `push_list` WHERE `id`='" . $pushid . "'";

        $query = $db->query($sql);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

}

