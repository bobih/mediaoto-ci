<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use \Firebase\JWT\JWT;

class MobileAppController extends BaseController {

    use ResponseTrait;

    public function getAds() {

        $data = [
            [
                "id" => 1,
                "title" => "ads1",
                "image" => "",
                "url" => ""
            ],
            [
                "id" => 2,
                "title" => "ads2",
                "image" => "",
                "url" => ""
            ],
            [
                "id" => 3,
                "title" => "ads3",
                "image" => "",
                "url" => ""
            ]
        ];

        //$data = [];

        $response = json_encode($data,JSON_NUMERIC_CHECK);

        return $this->respond($response, 200);

    }

}