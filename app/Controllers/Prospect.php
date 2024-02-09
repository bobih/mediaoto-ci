<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use \Firebase\JWT\JWT;

class Prospect extends BaseController
{
    use ResponseTrait;
    


    //Add By Bobby from PC
    public function getSummary()
    {
        $db = db_connect();

        $userid =  $this->request->getVar('userid');

        //Summary Total
        $query = "SELECT count(`id`) as total FROM `prospek` WHERE userid='".$userid."' AND `lost` = 0;";
        $total = $db->query($query)->getRow();
        //Summary Total Har Ini
        $query = "SELECT count(`id`) as total FROM `prospek` WHERE DATE(`created_at`) = CURRENT_DATE and `userid`='".$userid."' AND lost = 0 ;";
        $total_today = $db->query($query)->getRow();

        //NEW
        $query = "SELECT count(`id`) as total FROM `prospek` WHERE `view` = 0 AND `userid`='".$userid."';";
        $baru = $db->query($query)->getRow();

        $query = "SELECT count(`id`) as total FROM `prospek` WHERE `view` = 0 AND DATE(`created_at`) = CURRENT_DATE and `userid`='".$userid."';";
        $baru_today = $db->query($query)->getRow();

        // VIEW
        $query = "SELECT count(`id`) as total FROM `prospek` WHERE `view` = 1 AND `userid`='".$userid."';";
        $viewed = $db->query($query)->getRow();

        $query = "SELECT count(`id`) as total FROM `prospek` WHERE `view` = 1 AND DATE(`created_at`) = CURRENT_DATE and `userid`='".$userid."';";
        $viewed_today = $db->query($query)->getRow();


        // Total Lost
         //Summary Lost
         $query = "SELECT count(`id`) as total FROM `prospek` WHERE userid='".$userid."' AND `lost` > 0;";
         $lost = $db->query($query)->getRow();
         //Summary Total Lost Ini
         $query = "SELECT count(`id`) as total FROM `prospek` WHERE DATE(`created_at`) = CURRENT_DATE and `userid`='".$userid."' AND lost > 0 ;";
         $lost_today = $db->query($query)->getRow();

         // REMINDER
         /*
         $query = "select distinct(leads.id), `leads`.*, `prospek`.`view`, `prospek`.`created_at` as `regdate`, `prospek`.`favorite`, `prospek`.`id` as `pid` 
                    from `reminder` right join `prospek` on `reminder`.`leadsid` = `prospek`.`id` 
                    inner join `leads` on `leads`.`id` = `prospek`.`leadsid` 
                    where `reminder`.`userid` = '".$userid."' and date(`reminder`.`tanggal`) >= '".date('Y-m-d H:i:s')."';";
        */

        $query = "SELECT DISTINCT(`reminder`.`leadsid`) 
                from `reminder` 
                where `reminder`.`userid` = '".$userid."' 
                AND `reminder`.`tanggal` >= NOW()";
        $reminder = $db->query($query)->getNumRows();


        $query = "SELECT DISTINCT(`reminder`.`leadsid`)
                  from `reminder` 
                  where `reminder`.`userid` = '".$userid."' 
                  AND `reminder`.`tanggal` >= NOW() 
                  AND `reminder`.`tanggal` < CURRENT_DATE() + INTERVAL 1 DAY ";
        $reminder_today = $db->query($query)->getNumRows();


        $data = [
            [
                "id" => 0,
                "title" => "Semua Prospek",
                "total" => $total->total,
                "today" => $total_today->total
            ],
            [
                "id" => 1,
                "title" => "Baru",
                "total" => $baru->total,
                "today" => $baru_today->total
            ],
            [
                "id" => 2,
                "title" => "Sudah Pernah Dilihat",
                "total" => $viewed->total,
                "today" => $viewed_today->total
            ],
            [
                "id" => 3,
                "title" => "Telepon Kembali",
                "total" => $reminder,
                "today" => $reminder_today,
            ],
            [
                "id" => 4,
                "title" => "Lost",
                "total" => $lost->total,
                "today" => $lost_today->total,
            ],
            /*
            [
                "id" => 6,
                "title" => "Tidak Ada Langkah yang Ditentukan",
                "total" => 1,
                "today" => 0
            ],
            *
            [
                "id" => 7,
                "title" => "Pembaruan",
                "total" => 0,
                "today" => 0
            ]
            */
        ];


        //$response = json_decode($data);
        //$data = json_decode($data);
        //array_walk_recursive($data,function(&$item){$item=strval($item);});
        $response = json_encode($data,JSON_NUMERIC_CHECK);

        return $this->respond($response, 200);


    }



    public function getList()
    {

        $userid =  $this->request->getVar('userid');
        $listid = $this->request->getVar('listid');


        $db = db_connect();
        $sql = "SELECT `leads`.*,`prospek`.`view`, `prospek`.`created_at` as regdate, `prospek`.`favorite`,  `prospek`.`lost` ,  `prospek`.`id` as pid FROM `prospek` 
        LEFT JOIN `leads` ON `leads`.`id` = `prospek`.`leadsid` ";

        switch($listid){
            case 0 :
                $sql .= " WHERE `prospek`.`userid`='".$userid."' AND lost = 0";
            break;
            case 1:
                $sql .= " WHERE `prospek`.`view` = 0 AND `userid`='".$userid."' ";
            break;
            case 2:
                $sql .= " WHERE `prospek`.`view` = 1 AND `userid`='".$userid."' ";
            break;
            case 3:
                /*
                $sql = "SELECT distinct(leads.id), `leads`.*, `prospek`.`view`, `prospek`.`created_at` as `regdate`, `prospek`.`favorite`, `prospek`.`id` as `pid` 
                from `reminder` right join `prospek` on `reminder`.`leadsid` = `prospek`.`id` 
                inner join `leads` on `leads`.`id` = `prospek`.`leadsid` 
                where `reminder`.`userid` = '".$userid."' and date(`reminder`.`tanggal`) >= '".date('Y-m-d H:i:s')."'";
                */

                $sql = "SELECT DISTINCT(`reminder`.`leadsid`), `leads`.*, `prospek`.`view`, `prospek`.`created_at` as `regdate`, `prospek`.`favorite`, `prospek`.`lost`, `prospek`.`id` as `pid` 
                from `reminder` left join `prospek` on `reminder`.`leadsid` = `prospek`.`id` 
                inner join `leads` on `leads`.`id` = `prospek`.`leadsid` 
                where `reminder`.`userid` = '".$userid."' and 
                `reminder`.`tanggal` > '".date('Y-m-d H:i:s')."'";
            break;
            case 5:
                $sql .= " WHERE `userid`='0' "; 
            break;
            case 4:
                $sql .= " WHERE `prospek`.`userid`='".$userid."' AND `prospek`.`lost` > 0";
            break;
        }

        

        $sql .= " ORDER BY `prospek`.`id` DESC ";

        $page = $this->request->getVar('page');
        if($page == 0){
            $sql .= " LIMIT 10 ";
            
        } else {
            $sql .= " LIMIT ".$page.", 10 ";
        }

        //return $this->respond($sql, 200);

        $query = $db->query($sql);
        $return = $query->getResultArray();

        $data = [];
        $x = 0;
        foreach($return as $rows){
            $data[$x]['id'] = $rows['pid'];
            $data[$x]['nama'] = trim( ucwords($rows['name']));

            // Test Bobby
            
            if( $userid == 115){
                $data[$x]['mobile'] =  '812345678';
            } else {
                $data[$x]['mobile'] =  $rows['phone'];
            }

            //$data[$x]['mobile'] =  $rows['phone'];

           

            $cartype = html_entity_decode($rows['variant']);
            if(strlen($cartype) > 15){
                $cartype =  substr(html_entity_decode($rows['variant']), 0, 15) . '...'; 
            }
            $data[$x]['car'] = $rows['model'] . " " .  $cartype;  // $rows['model'] . " " . html_entity_decode($rows['variant']);
            $data[$x]['model'] = $rows['model'];
            $data[$x]['type'] = $cartype; // html_entity_decode($rows['variant']);

            $data[$x]['lokasi'] = $rows['city'];
            $data[$x]['angsuran'] = "0";
            $data[$x]['lost'] = $rows['lost'];
            $data[$x]['tenor'] = "0";
            $data[$x]['tdp'] = "0";
            $data[$x]['favorite'] = $rows['favorite'];
            $data[$x]['view'] = $rows['view'];
            $data[$x]['regdate'] =  $rows['regdate'];
            $x++;
        }
        array_walk_recursive($data,function(&$item){$item=strval($item);});
        $response = json_encode($data);
        return $this->respond($response, 200);


    }

    public function setFavorite(){
        $db = db_connect();
        $leadid =  $this->request->getVar('leadid');
        $userid =  $this->request->getVar('userid');
        $status =  $this->request->getVar('status');

        // Update Status as View
        $sql = "UPDATE `prospek` SET `favorite` = '".$status."' WHERE `prospek`.`id` = '".$leadid."';";
        $query = $db->query($sql);
        $db->close();
        return $this->respond("Updated", 200);
    }

    public function setNote(){
        $db = db_connect();
        $leadid =  $this->request->getVar('leadid');
        $userid =  $this->request->getVar('userid');
        $note =  $this->request->getVar('note');

        // Update Status as View
        $sql = "UPDATE `prospek` SET `note` = '".$note."' WHERE `prospek`.`id` = '".$leadid."';";
        $query = $db->query($sql);
        $db->close();
        if($query){
            return $this->respond("Updated", 200);
        } else {
            return $this->respond(['error' => 'Invalid email or password.'], 401);
        }
    }

    public function setLost(){
        $db = db_connect();
        $leadid =  $this->request->getVar('leadid');
        $lost =  $this->request->getVar('lost');

        // Update Status as View
        $sql = "UPDATE `prospek` SET `lost` = '".$lost."' WHERE `prospek`.`id` = '".$leadid."';";
        $query = $db->query($sql);
        $db->close();
        if($query){
            return $this->respond("Updated", 200);
        } else {
            return $this->respond(['error' => 'Invalid email or password.'], 401);
        }
    }
    public function setReminder(){
        $db = db_connect();
        $leadid =  $this->request->getVar('leadid');
        $notifid =  $this->request->getVar('notifid');
        $userid =  $this->request->getVar('userid');
        $tanggal =  $this->request->getVar('tanggal');

        // Update Status as View
        $sql = "INSERT INTO `reminder` ( `leadsid`, `userid`, `notifid`, `tanggal`) 
                VALUES ('".$leadid."','".$userid."' , '".$notifid."', '".$tanggal."');";
        $query = $db->query($sql);
        $db->close();
        if($query){
            return $this->respond("Updated", 200);
        } else {
            return $this->respond(['error' => 'Invalid email or password.'], 401);
        }
    }

    public function phoneLog(){
        $db = db_connect();
        $leadid =  $this->request->getVar('leadid');
        $userid =  $this->request->getVar('userid');
       
        // Update Status as View
        $sql = "INSERT INTO `list_call` ( `leadsid`, `userid`,  `tanggal`) 
                VALUES ('".$leadid."','".$userid."' ,  CURRENT_TIMESTAMP());";
        $query = $db->query($sql);
        $db->close();
        if($query){
            return $this->respond("Updated", 200);
        } else {
            return $this->respond(['error' => 'Invalid email or password.'], 401);
        }
    }


    public function waLog(){
        $db = db_connect();
        $leadid =  $this->request->getVar('leadid');
        $userid =  $this->request->getVar('userid');
       

        // Update Status as View
        $sql = "INSERT INTO `list_wa` ( `leadsid`, `userid`,  `tanggal`) 
                VALUES ('".$leadid."','".$userid."' ,  CURRENT_TIMESTAMP());";
        $query = $db->query($sql);
        $db->close();
        if($query){
            return $this->respond("Updated", 200);
        } else {
            return $this->respond(['error' => 'Invalid email or password.'], 401);
        }
    }

    public function getFavorite()
    {
        $db = db_connect();
        $userid =  $this->request->getVar('userid');

        // Get Data
        $sql = "SELECT `leads`.*, `prospek`.`view`, `prospek`.`favorite`, `prospek`.`created_at` as regdate, `prospek`.`note`, `prospek`.`lost`, `prospek`.`id` as pid  FROM `prospek` 
                LEFT JOIN `leads` ON `leads`.`id` = `prospek`.`leadsid` 
                WHERE `prospek`.`userid`='".$userid."' AND  `prospek`.`favorite` = 1 ORDER BY  `prospek`.`id` DESC ";
       
       
       $page = $this->request->getVar('page');
       if($page == 0 || $page == ''){
           $sql .= " LIMIT 10 ";
           
       } else {
           $sql .= " LIMIT ".$page.", 10 ";
       }
       

        $query = $db->query($sql);
        $return = $query->getResultArray();

        $data = [];
        $x = 0;
        foreach($return as $rows){
            $data[$x]['id'] = $rows['pid'];
            $data[$x]['nama'] = trim( ucwords($rows['name']));

            if( $userid == 115){
                $data[$x]['mobile'] =  '812345678';
            } else {
                $data[$x]['mobile'] =  $rows['phone'];
            }

            //$data[$x]['mobile'] = $rows['phone'];
           
            
            $cartype = html_entity_decode($rows['variant']);
            if(strlen($cartype) > 15){
                $cartype =  substr(html_entity_decode($rows['variant']), 0, 15) . '...'; 
            }
            $data[$x]['car'] = $rows['model'] . " " .  $cartype;  // $rows['model'] . " " . html_entity_decode($rows['variant']);
            $data[$x]['model'] = $rows['model'];
            $data[$x]['type'] = $cartype; // html_entity_decode($rows['variant']);
            

            //$data[$x]['car'] = $rows['model'] . " " . html_entity_decode($rows['variant']);
            //$data[$x]['model'] = $rows['model'];
            //$data[$x]['type'] = html_entity_decode($rows['variant']);
           
           
            $data[$x]['lokasi'] = $rows['city'];
            $data[$x]['angsuran'] = "0";
            $data[$x]['tenor'] = "0";
            $data[$x]['tdp'] = "0";
            $data[$x]['favorite'] = $rows['favorite'];
            $data[$x]['view'] = $rows['view'];
            $data[$x]['lost'] = $rows['lost'];
            $data[$x]['regdate'] =  $rows['regdate'];
            $x++;
        }

        array_walk_recursive($data,function(&$item){$item=strval($item);});
        $response = json_encode($data);
        return $this->respond($response, 200);
    }
    public function getLeadById(){
        $db = db_connect();

        $leadid =  $this->request->getVar('leadid');
        $userid =  $this->request->getVar('userid');

        // Update Status as View
        $sql = "UPDATE `prospek` SET `view` = '1' WHERE `prospek`.`id` = '".$leadid."';";
        $query = $db->query($sql);


        // Get Data
        $sql = "SELECT `leads`.*, `prospek`.`view`, `prospek`.`favorite`, `prospek`.`created_at` as regdate, `prospek`.`note`,`prospek`.`lost`, `prospek`.`id` as pid  FROM `prospek` 
                LEFT JOIN `leads` ON `leads`.`id` = `prospek`.`leadsid` 
                WHERE  `prospek`.`id` = '" . $leadid . "' LIMIT 1";
        
        //return $this->respond($sql, 200);


        $query = $db->query($sql);
        $return = $query->getResultArray();

        // Get reminder
        $sql = "SELECT * FROM `reminder` WHERE `leadsid` = '".$leadid."' AND `userid` = '".$userid."' ORDER BY tanggal DESC";
        $query = $db->query($sql);
        $reminder = $query->getResultArray();


        $data = [];
        $x = 0;
        foreach($return as $rows){
            $data[$x]['id'] = $rows['pid'];
            $data[$x]['nama'] = trim( ucwords($rows['name']));
            $data[$x]['nickname'] = '';
            $data[$x]['email'] = '';

            
            if( $userid == 115){
                $data[$x]['mobile'] =  '812345678';
            } else {
                $data[$x]['mobile'] =  $rows['phone'];
            }
            

           // $data[$x]['mobile'] =  $rows['phone'];
            $data[$x]['car'] = $rows['model'] . " " . html_entity_decode($rows['variant']);
            $data[$x]['lokasi'] = $rows['city'];
            $data[$x]['angsuran'] = "0";
            $data[$x]['tenor'] = "0";
            $data[$x]['tdp'] = "0";
            $data[$x]['favorite'] = $rows['favorite'];
            $data[$x]['view'] = $rows['view'];
            $data[$x]['lost'] = $rows['lost'];
            $data[$x]['regdate'] =  $rows['regdate'];
            $data[$x]['lastview'] =  "2023-11-17 10:10:10";
            $data[$x]['catatan'] = $rows['note'];
            $x++;
        }

        $data[0]['reminder'] = $reminder;



        array_walk_recursive($data,function(&$item){$item=strval($item);});
        $response = json_encode($data);
        return $this->respond($response, 200);


    }

    public function searchLeads(){
        $db = db_connect();

        $search =  $this->request->getVar('search');
        $userid =  $this->request->getVar('userid');

        // Get Data
        $sql = "SELECT `leads`.*, `prospek`.`view`, `prospek`.`favorite`, `prospek`.`created_at` as regdate, `prospek`.`note`, `prospek`.`lost`, `prospek`.`id` as pid  FROM `prospek` 
                LEFT JOIN `leads` ON `leads`.`id` = `prospek`.`leadsid` 
                WHERE `prospek`.`userid`='".$userid."' AND  `leads`.`name` LIKE '%" . $search . "%'";
        
        //return $this->respond($sql, 200);


        $query = $db->query($sql);
        $return = $query->getResultArray();

        $data = [];
        $x = 0;
        foreach($return as $rows){
            $data[$x]['id'] = $rows['pid'];
            $data[$x]['nama'] = trim( ucwords($rows['name']));

            if( $userid == 115){
                $data[$x]['mobile'] =  '812345678';
            } else {
                $data[$x]['mobile'] =  $rows['phone'];
            }
            

            //$data[$x]['mobile'] = $rows['phone'];
            
            
            $data[$x]['car'] = $rows['model'] . " " . html_entity_decode($rows['variant']);
            $data[$x]['model'] = $rows['model'];
            $data[$x]['type'] = html_entity_decode($rows['variant']);
            $data[$x]['lokasi'] = $rows['city'];
            $data[$x]['angsuran'] = "0";
            $data[$x]['tenor'] = "0";
            $data[$x]['tdp'] = "0";
            $data[$x]['favorite'] = $rows['favorite'];
            $data[$x]['view'] = $rows['view'];
            $data[$x]['lost'] = $rows['lost'];
            $data[$x]['regdate'] =  $rows['regdate'];;
            $x++;
        }

        array_walk_recursive($data,function(&$item){$item=strval($item);});
        $response = json_encode($data);
        return $this->respond($response, 200);
    }

}