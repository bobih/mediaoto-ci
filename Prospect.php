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
        $data = [
            [
                "id" => 1,
                "title" => "Semua Prospek",
                "total" => 52,
                "today" => 0
            ],
            [
                "id" => 2,
                "title" => "Baru",
                "total" => 15,
                "today" => 0
            ],
            [
                "id" => 3,
                "title" => "Sudah Pernah Dilihat",
                "total" => 31,
                "today" => 0
            ],
            [
                "id" => 4,
                "title" => "Telepon Kembali",
                "total" => 4,
                "today" => 0
            ],
            [
                "id" => 5,
                "title" => "Tes Jalan",
                "total" => 1,
                "today" => 0
            ],
            [
                "title" => "Tidak Ada Langkah yang Ditentukan",
                "total" => 1,
                "today" => 0
            ],
            [
                "title" => "Pembaruan",
                "total" => 0,
                "today" => 0
            ]
        ];

        //$response = json_decode($data);
        $response = json_encode($data);

        return $this->respond($response, 200);


    }


    public function getList()
    {


        $data = '[
{
"id": 1,
"nama": "Rosa Bovananto",
"mobile": "85880258151",
"car": "Honda Brio",
"lokasi": "Jakarta Selatan",
"angsuran": 61709000,
"tenor": 12,
"tdp": 112200000,
"regdate": "2023-11-17 10:10:10"
},
{
"id": 2,
"nama": "Bobby Aja",
"mobile": "85880258151",
"car": "Honda City",
"lokasi": "Jakarta Selatan",
"angsuran": 61709000,
"tenor": 6,
"tdp": 112200000,
"regdate": "2023-11-17 10:10:10"
},
{
"id": 3,
"nama": "Resty Doang",
"mobile": "85880258151",
"car": "Honda Brio",
"lokasi": "Jakarta Selatan",
"angsuran": 61709000,
"tenor": 7,
"tdp": 112200000,
"regdate": "2023-11-17 10:10:10"
},
{
"id": 4,
"nama": "Rosa Bovananto",
"mobile": "85880258151",
"car": "Ioniq 5",
"lokasi": "Jakarta Selatan",
"angsuran": 61709000,
"tenor": 12,
"tdp": 112200000,
"regdate": "2023-11-17 10:10:10"
},
{
"id": 5,
"nama": "Rosa Bovananto",
"mobile": "85880258151",
"car": "Ioniq 5",
"lokasi": "Jakarta Selatan",
"angsuran": 61709000,
"tenor": 12,
"tdp": 112200000,
"regdate": "2023-11-17 10:10:10"
}
]';

        $response = json_decode($data, true);

        return $this->respond($response, 200);

    }
    public function getDetail()
    {


        $data = '{
"id": 1,
"nama": "Rosa Bovananto",
"nickname": "Rosa",
"email": "rosabovananto@gmail.com",
"alamat": "Jalan Setiabudi",
"mobile": "85880258151",
"car": "Honda Brio",
"lokasi": "Jakarta Selatan",
"angsuran": 61709000,
"tenor": 12,
"tdp": 112200000,
"regdate": "2023-11-17 10:10:10",
"lastview": "2023-11-17 10:10:10",
"catatan": [
{
"id": 1,
"tanggal": "2023-11-10 10:10:10",
"note": "telpon tidak diangkat"
},
{
"id": 2,
"tanggal": "2023-11-10 11:10:10",
"note": "user sudah beli"
}
]
}';

        $response = json_decode($data, true);

        return $this->respond($response, 200);

    }

    function getLeadById(){
        
        //$leadid = $this->request->getVar('leadid');
	// new data by bobby

        $leadid = '65584c930f6222797a3befc4';
        $sql = 'SELECT * from leads_backp where id = ' . $leadid;
        echo $sql . "</br>"; 

        $query = $this->db->query($sql);

        $data = $this->db->get_result_array();

            echo "<pre>>";
            print_r($data);
            echo "</pre>";

            

    }

}
