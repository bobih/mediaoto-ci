<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;


class FcmController extends BaseController
{


    public function sendPushNotification($fcmtoken, $title, $payload,$msgType)
    {
        /*
        $db = db_connect();
        $sql = "SELECT `fcmtoken` from `users` where `id`=36 LIMIT 1";
        $query = $db->query($sql)->getRow();
        
        $userToken = $query->fcmtoken;
        */
        $userToken = $fcmtoken;

        $url = "https://fcm.googleapis.com/fcm/send";
        $server_key = "AAAAnXAErDs:APA91bFNBiYEq7DtFkzdk80XjuKKL-Th5hukyDzTBKRW4VbxFVcYHs2_blwTZaliuKA5xvvA3iBbwvZxnr4dGYYdaysX9Sd4J46PGECiGLqlwpNRODrIINMpAfXLmSCHfnnQNfn8W4aq";
       
       
       
        $params = array(

            "title" => 'Incoming Leads!',
            "body" => "Please Check Out",
            "icon" => '',
            "color" => '',
            "sound" => '',
            "tag" => 'tag',
            "click_action" => 'FLUTTER_NOTIFICATION_CLICK',
            "body_loc_key" => 'body_lock_key',
            "body_loc_args" => array(
                "body_loc"
            ),
            "title_loc_key" => 'title_loc',
            "title_loc_args" => array(
                'Title_loc'
            ),

        );


        //$payload = json_encode(array("page" => "ProspectDetail", "requestData" => "6"));
        $data = array(
            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
            "sound" => "default",
            "status" => "done",
            "screen" => $payload,

        );

        $message = array(
            "notification" => $params,
            "data" => $data,
            "to" => $userToken,
        );

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => array(
                "Authorization: key=" . $server_key,
                "Content-Type: application/json",
            ),
            CURLOPT_POSTFIELDS => json_encode($message),
        );

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        //curl_close($curl);
        //return $response;

        if ($response == false) {
            return curl_error($curl);
        } else {
            return "OK";
          
        }
    }


    public function sendWelcomeNotification($fcmtoken, $title, $payload,$msgType)
    {
        /*
        $db = db_connect();
        $sql = "SELECT `fcmtoken` from `users` where `id`=36 LIMIT 1";
        $query = $db->query($sql)->getRow();
        
        $userToken = $query->fcmtoken;
        */
        $userToken = $fcmtoken;

        $url = "https://fcm.googleapis.com/fcm/send";
        $server_key = "AAAAnXAErDs:APA91bFNBiYEq7DtFkzdk80XjuKKL-Th5hukyDzTBKRW4VbxFVcYHs2_blwTZaliuKA5xvvA3iBbwvZxnr4dGYYdaysX9Sd4J46PGECiGLqlwpNRODrIINMpAfXLmSCHfnnQNfn8W4aq";
       
       
      

        $params = array(

            "title" => 'Welcome to Mediaoto',
            "body" => "Paket Membership Gold anda telah Aktif",
            "icon" => '',
            "color" => '',
            "sound" => '',
            "tag" => 'tag',
            "click_action" => 'FLUTTER_NOTIFICATION_CLICK',
            "body_loc_key" => 'body_lock_key',
            "body_loc_args" => array(
                "body_loc"
            ),
            "title_loc_key" => 'title_loc',
            "title_loc_args" => array(
                'Title_loc'
            ),

        );
       
    


        //$payload = json_encode(array("page" => "ProspectDetail", "requestData" => "6"));
        $data = array(
            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
            "sound" => "default",
            "status" => "done",
            "screen" => $payload,

        );

        $message = array(
            "notification" => $params,
            "data" => $data,
            "to" => $userToken,
        );

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => array(
                "Authorization: key=" . $server_key,
                "Content-Type: application/json",
            ),
            CURLOPT_POSTFIELDS => json_encode($message),
        );

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        //curl_close($curl);
        //return $response;

        if ($response == false) {
            return curl_error($curl);
        } else {
            return "OK";
          
        }
    }
}

