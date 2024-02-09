<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;


class WelcomePushController extends BaseController
{

    public function sendWelcome(){

        echo "OK";


       
        $userid = 66;
        $userModel = new UserModel();
        $user = $userModel->find($userid);

        
        
        $fcmtoken = $user['fcmtoken'];

       $fcmController = new FcmController();
        $title = "";
        $message = "";
        $payload = "";
       
        echo "<pre>";
        print_r($user);
        echo "</pre>";
        echo $fcmtoken . "-" . $user['nama'];
      //$pushWelcome = $fcmController->sendWelcomeNotification($fcmtoken,$title,$payload,$message);

       

    }


}