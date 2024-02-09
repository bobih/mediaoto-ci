<?php

namespace App\Controllers;

use Exception;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;


class Register extends BaseController
{
    use ResponseTrait;

    public function index()
    {

        /*
        
        $response = [
            'errors' => 401,
            'message' => 'Invalid Inputs'
        ];
        return $this->fail($response, 401);
        */




        $rules = [
            'email' => [
                'rules' => 'required|min_length[4]|max_length[255]|valid_email|is_unique[users.email]',
                'errors' => [
                    'is_unique' => 'Email Already Regitered'
                ]
            ],
            'nama' => ['rules' => 'required|alpha_numeric_space|max_length[255]'],
            'fcmtoken' => ['rules' => 'required'],
            'password' => ['rules' => 'required|min_length[4]|max_length[255]'],
            'confirm_password' => ['label' => 'confirm password', 'rules' => 'matches[password]']
        ];



        if ($this->validate($rules)) {
            $model = new UserModel();
            $data = [
                'email' => trim($this->request->getVar('email')),
                'nama' => trim($this->request->getVar('nama')),
                'fcmtoken' => trim($this->request->getVar('fcmtoken')),
                'password' => password_hash(trim($this->request->getVar('password')), PASSWORD_DEFAULT)
            ];

            $model->save($data);
            return $this->respond(['message' => 'Registered Successfully'], 200);
        } else {
            $response = [
                'errors' => $this->validator->getErrors(),
                'message' => 'Invalid Inputs'
            ];
            return $this->fail($response, 409);

        }

    }


    public function regProvider()
    {

        $rules = [
            'email' => [
                'rules' => 'required',
                'errors' => [
                    'is_unique' => 'Email Already Regitered'
                ]
            ],
            'nama' => ['rules' => 'required'],
            'provider' => ['rules' => 'required'],
            'fcmtoken' => ['rules' => 'required']
        ];

        // Check User
        
        if ($this->validate($rules)) {


            /*
            $filename =  time() . uniqid() .".jpg";
            try{
            file_put_contents('/DATA/mediaoto/public_html/images/'.$filename, file_get_contents($url));
            } catch (Exception $e){
                return $this->respond(['message' =>  $url], 400); 
            }
            */



            $model = new UserModel();
            $data = [
                'email' => trim($this->request->getVar('email')),
                'nama' => trim($this->request->getVar('nama')),
                'phone' => trim($this->request->getVar('phone')),
                'image' => trim($this->request->getVar('phone')),
                'provider' => trim($this->request->getVar('provider')),
                'fcmtoken' => trim($this->request->getVar('fcmtoken')),
                'password' => password_hash(trim(md5($this->request->getVar('email'))), PASSWORD_DEFAULT)
            ];

            $model->save($data);
            return $this->respond(['message' => 'Registered Successfully'], 200);
        } else {
            $response = [
                'errors' => $this->validator->getErrors(),
                'message' => 'Invalid Inputs'
            ];
            return $this->fail($response, 409);

        }

    }
}