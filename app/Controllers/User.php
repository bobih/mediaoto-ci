<?php

namespace App\Controllers;

use \Firebase\JWT\JWT;
use Spatie\Image\Image;

use App\Models\UserModel;
use CodeIgniter\Files\File;
use Spatie\Image\Manipulations;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use Exception;

class User extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $users = new UserModel;
        return $this->respond(['users' => $users->findAll()], 200);

        //echo FCPATH;

    }

    public function getUserInfo()
    {

        //return $this->respond("OK", 200);

        $userid = $this->request->getVar('userid');
        $version = $this->request->getVar('version');
        $fcmtoken = $this->request->getVar('fcmtoken');

        if (is_null($userid)) {
            return $this->respond(['error' => 'Invalid User'], 401);
        }

        $db = db_connect();


        //$userid = 25;
        $sql = "SELECT * from users WHERE id='" . $userid . "' LIMIT 1";
        $query = $db->query($sql);
        $return = $query->getResultArray();

        $data = [];
        $x = 0;
        foreach ($return as $rows) {

            if ($rows['quota'] == 0) {
                $rows['quota'] = "0";
            }
            $data[$x]['id'] = $rows['id'];
            $data[$x]['nama'] = trim(ucwords($rows['nama']));
            $data[$x]['email'] = $rows['email'];
            $data[$x]['provider'] = $rows['provider'];
            $data[$x]['phone'] = $rows['phone'];
            $data[$x]['quota'] =  $rows['quota']; // $rows['phone'];
            $data[$x]['alamat'] = $rows['alamat'];
            $data[$x]['lokasi'] = $rows['lokasi'];
            $data[$x]['ktp'] = $rows['ktp'];
            $data[$x]['npwp'] = $rows['npwp'];

            // Check if image Webp
            //$data[$x]['image'] = $rows['image'];
            $imageFile = $data[$x]['image'];
            
            if (!$imageFile == '') {
                $imageArr = explode(".", $imageFile);
                if (!$imageArr[1] == 'webp') {
                    // if not webp
                    $convert = $this->getThumbnail($imageFile);
                    if ($convert == true) {
                        $imageFile = $imageArr[0] . ".webp";
                        // Update Database
                        $sql = "UPDATE `users` SET `image` = '" . $imageFile . "' WHERE `users`.`id` = '" . $userid . "';";
                        $query = $db->query($sql);
                    }
                }
            }
            $data[$x]['image'] = $imageFile;
            $data[$x]['brand'] = $rows['brand'];
            $data[$x]['fcmtoken'] = $rows['fcmtoken'];
            $data[$x]['register'] =  $rows['created_at'];


            switch ($rows['acctype']) {
                case 2:
                    $data[$x]['acctype'] =  'SILVER';
                    break;
                case 3:
                    $data[$x]['acctype'] =  'GOLD';
                    break;
                case 4:
                    $data[$x]['acctype'] =  'DIAMOND';
                    break;
                default:
                    $data[$x]['acctype'] =  'BRONZE';
            }


            // Save to APpInfo
            $tanggal = date("Y-m-d H:i:s");

            if ($version == 'Unknown') {
                $sql = "INSERT INTO appinfo (`userid`, `fcmtoken`,`updated_at`) 
                VALUES ('" . $userid . "','" . $fcmtoken . "','" . $tanggal . "')
                ON DUPLICATE KEY UPDATE  `fcmtoken`='" . $fcmtoken . "', updated_at='" . $tanggal . "'";
                $query = $db->query($sql);
            } else {
                $sql = "INSERT INTO appinfo (`userid`, `version`,`fcmtoken`,`updated_at`) 
                        VALUES ('" . $userid . "','" . $version . "','" . $fcmtoken . "','" . $tanggal . "')
                ON DUPLICATE KEY UPDATE `version`='" . $version . "', `fcmtoken`='" . $fcmtoken . "', updated_at='" . $tanggal . "'";
                $query = $db->query($sql);
            }



            $key = getenv('JWT_SECRET');
            $iat = time(); // current timestamp value
            $exp = $iat + 36000;

            $payload = array(
                "iss" => "mediaoto",
                "aud" => "mobile",
                "sub" => "api",
                "iat" => $iat, //Time the JWT issued at
                "exp" => $exp, // Expiration time of token
                "email" => $rows['email'],
            );

            $token = JWT::encode($payload, $key, 'HS256');
            $data[$x]['token'] = $token;

            $x++;
        }
        array_walk_recursive($data, function (&$item) {
            $item = strval($item);
        });
        $response = json_encode($data);
        return $this->respond($response, 200);
    }


    public function updateImage()
    {


        $userid = trim($this->request->getVar('userid', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $nama = trim($this->request->getVar('nama', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $phone = trim($this->request->getVar('phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $alamat = trim($this->request->getVar('alamat', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

        $file = $this->request->getFile('file');

        if (!$file->isValid()) {
            return $this->respond(['error' => 'Update Faled'], 401);
        };

        $newName = $file->getRandomName();
        $imagePath = FCPATH . "../images";
        // if ($file->move('/DATA/mediaoto/public_html/images', $newName)) {
        if ($file->move($imagePath, $newName)) {

            // Convert Image
            $convert = $this->getThumbnail($newName);
            if ($convert == true) {
                $imageArr = explode('.', $newName);
                $newName = $imageArr[0] . ".webp";
            }

            $db = db_connect();
            $sql = "UPDATE `users` SET `nama`='" . $nama . "', `phone`='" . $phone . "', `alamat`='" . $alamat . "', `image` = '" . $newName . "' WHERE `users`.`id` = '" . $userid . "';";
            $query = $db->query($sql);
            $db->close();
            // remove old filename
            $oldfile = '/DATA/mediaoto/public_html/images/' . basename(trim($this->request->getVar('oldfilename')));
            //delete_files($path);
            if (file_exists($oldfile)) {
                unlink($oldfile);
            }

            return $this->respond(['message' => 'Update Successfully'], 200);
        } else {
            return $this->respond(['error' => 'Update Faled'], 401);
        }
    }

    public function updateUserInfo()
    {
        $userid = trim($this->request->getVar('userid', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $nama = trim($this->request->getVar('nama', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $phone = trim($this->request->getVar('phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $alamat = trim($this->request->getVar('alamat', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

        $db = db_connect();
        $sql = "UPDATE `users` SET `nama`='" . $nama . "', `phone`='" . $phone . "', `alamat`='" . $alamat . "' WHERE `users`.`id` = '" . $userid . "';";
        if ($db->query($sql)) {
            $db->close();
            return $this->respond(['message' => 'Update Successfully'], 200);
        } else {
            return $this->respond(['error' => 'Update Faled'], 401);
        }
    }

    public function changePassword()
    {

        $userModel = new UserModel();

        $userid = trim($this->request->getVar('userid', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $oldPassword  = $this->request->getVar('oldpassword');
        //$oldPassword  = password_hash(trim($this->request->getVar('oldpassword')), PASSWORD_DEFAULT);
        $newPassword  = password_hash(trim($this->request->getVar('newpassword')), PASSWORD_DEFAULT);

        $user = $userModel->where('id', $userid)->first();
        $pwd_verify = password_verify($oldPassword, $user['password']);

        $db = db_connect();

        if ($pwd_verify) {
            $sql = "UPDATE `users` SET password = '" . $newPassword . "' WHERE `users`.`id` = '" . $userid . "';";
            if ($db->query($sql)) {
                return $this->respond(['message' => 'Update Successfully'], 200);
            } else {
                return $this->respond(['error' => 'Update Faled'], 401);
            }
        } else {
            return $this->respond(['error' => 'Update Faled'], 401);
        }
    }


    public function getThumbnail($imageFile)
    {

        $imagePath = FCPATH . "../images/";
        $imageArr = explode('.', $imageFile);
        $imageLocation = $imagePath . $imageFile;
        try {
            if (file_exists($imageLocation)) {
                $image = Image::load($imageLocation)
                    ->width(100)
                    ->format(Manipulations::FORMAT_WEBP);
                $image->save($imagePath . $imageArr[0] . ".webp");
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}
