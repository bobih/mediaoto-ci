<?php

namespace App\Controllers;

use Carbon;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;


class NewsController extends BaseController
{

    use ResponseTrait;
    public function getNews()
    {

        $page = $this->request->getVar('page');
        $search = $this->request->getVar('search');

        $db = db_connect();
        $sql = "SELECT `news_posts`.`id`, `users`.`nama` as author, `users`.`image` as authorimg, `source`, `title`,`slug`,`description`, `content`, `published_at`, `news_posts`.`image` 
        FROM `news_posts`
        LEFT JOIN `users` ON `users`.`id` = `news_posts`.`userid` 
                WHERE `news_posts`.`active` = 1 ";

        if ($search != '') {

            if (strlen($search) < 20) {

                $sql .= " AND `news_posts`.`content` like '%" . $search . "%' ";
            }
        }

        $sql .= " ORDER BY `published_at` desc";
        if ($page == 0 || $page == '') {
            $sql .= " LIMIT 5 ";
        } else {
            $sql .= " LIMIT " . $page . ", 5 ";
        }

        $query = $db->query($sql);
        $return = $query->getResultArray();

        $data = [];
        $x = 0;

        foreach ($return as $rows) {
            $imageUrl = $this->getImage($rows['image'], $rows['id']);
            $data[$x]['id'] = $rows['id'];
            $data[$x]['image'] = $imageUrl;
            $data[$x]['slug'] = $rows['slug'];
            $data[$x]['author'] = $rows['author'];
            $data[$x]['authorimg'] = 'https://www.mediaoto.id/images/' . $rows['authorimg'];
            $data[$x]['title'] = $rows['title'];
            $data[$x]['source'] = strtoupper($rows['source']);
            $data[$x]['description'] = $rows['description'];
            $data[$x]['pulished_str'] = $this->time_elapsed_string($rows['published_at']);
            $data[$x]['pulished_at'] = $rows['published_at'];
            $data[$x]['content'] = '';
            $x++;
        }

        //array_walk_recursive($data,function(&$item){$item=strval($item);});
        $response = json_encode($data);
        return $this->respond($response, 200);
    }



    function getImage($image, $post)

    {

        // Check if contain Http

        if (strpos($image, "http") !== false || strpos($image, "https") !== false) {

            return $image;
        } else {

            // Get From Database;

            $db = db_connect();

            $sql = "SELECT `id`, `file_name` FROM `media` where `model_id` =" . $post;

            $query = $db->query($sql)->getRow();



            return "https://www.mediaoto.id/images/" . $query->id . "/" . $query->file_name;
        }
    }





    public function getDetail()

    {



        $newsid = $this->request->getVar('newsid');





        $db = db_connect();

        $sql = "SELECT `news_posts`.`id`, `users`.`nama` as author, `users`.`image` as authorimg, `source`, `title`,`slug`,`description`, `content`, `published_at`, `news_posts`.`image` 

        FROM `news_posts`

        LEFT JOIN `users` ON `users`.`id` = `news_posts`.`userid` 

                WHERE  `news_posts`.`id` = " . $newsid;



        $query = $db->query($sql);

        $return = $query->getResultArray();



        $data = [];

        $x = 0;

        foreach ($return as $rows) {

            $imageUrl = $this->getImage($rows['image'], $rows['id']);

            $data[$x]['id'] = $rows['id'];

            $data[$x]['image'] = $imageUrl;

            $data[$x]['slug'] = $rows['slug'];

            $data[$x]['author'] = $rows['author'];

            $data[$x]['authorimg'] = 'https://www.mediaoto.id/images/' . $rows['authorimg'];

            $data[$x]['title'] = $rows['title'];

            $data[$x]['source'] = strtoupper($rows['source']);

            $data[$x]['description'] = $rows['description'];

            $data[$x]['pulished_str'] = $this->time_elapsed_string($rows['published_at']);

            $data[$x]['pulished_at'] = $rows['published_at'];

            //$data[$x]['content'] = $rows['content'];
            $content = $rows['content'];
            // Replace Image

            $content = str_replace('src="/images', 'src="https://www.mediaoto.id/images', $content);

            $content = str_replace('<p', '<p style="justify-center; text-justify: inter-word;"', $content);

            $data[$x]['content'] = $content;


            $x++;
        }

        //array_walk_recursive($data,function(&$item){$item=strval($item);});

        $response = json_encode($data);

        return $this->respond($response, 200);
    }





    function time_elapsed_string($ptime)

    {

        $etime = time() - strtotime($ptime);



        if ($etime < 1) {

            return '0 seconds';
        }



        $a = array(

            365 * 24 * 60 * 60  =>  'year',

            30 * 24 * 60 * 60  =>  'month',

            24 * 60 * 60  =>  'day',

            60 * 60  =>  'hour',

            60  =>  'minute',

            1  =>  'second'

        );

        $a_plural = array(

            'year'   => 'years',

            'month'  => 'months',

            'day'    => 'days',

            'hour'   => 'hours',

            'minute' => 'minutes',

            'second' => 'seconds'

        );



        foreach ($a as $secs => $str) {

            $d = $etime / $secs;

            if ($d >= 1) {

                $r = round($d);

                return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
            }
        }
    }
}
