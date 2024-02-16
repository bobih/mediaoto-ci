<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

 /*
$routes->get('/', 'Home::index');
$routes->get('/getinfo', 'Home::getinfo');
$routes->get('/policy', 'Home::getPolicy');
$routes->get('/privacy', 'Home::getPrivacy');


$routes->get("crond", "CronController::index");
$routes->get("testpush", "CronController::testPush");
$routes->get("delivery", "DeliveryController::test");
$routes->get("fcmwelcome", "WelcomePushController::sendWelcome");
*/

// Update by bobby
$routes->group("/", function ($routes) {
    $routes->post("register", "Register::index");
    $routes->post("regprovider", "Register::regProvider");
    $routes->post("login", "Login::index");
    $routes->post("loginprovider", "Login::loginProvider");
    $routes->post("updateuser", "Login::updateUser");
    $routes->post("refreshtoken", "Login::refreshToken");
    $routes->post("users", "User::index");
    $routes->post("changepass", "User::changePassword");
    //$routes->post("users", "User::index", ['filter' => 'authFilter']);
    //$routes->post("users", "User::index");
    $routes->post("userinfo", "User::getUserInfo");
    $routes->post("updateimage", "User::updateImage");
    $routes->post("updateinfo", "User::updateUserInfo");


    // Test Debug
    $routes->post("usertest", "User::usertest");



    $routes->post("summary", "Prospect::getSummary");
    $routes->post("list", "Prospect::getList");
    $routes->post("favorite", "Prospect::getFavorite");
    $routes->post("detail", "Prospect::getDetail");
    $routes->post("leadbyid", "Prospect::getLeadById");
    $routes->post("setfavorite", "Prospect::setFavorite");
    $routes->post("setnote", "Prospect::setNote");

    $routes->post("phonelog", "Prospect::phoneLog");
    $routes->post("walog", "Prospect::waLog");

    $routes->post("setlost", "Prospect::setLost");
    $routes->post("reminder", "Prospect::setReminder");
    $routes->post("search", "Prospect::searchLeads");

    $routes->post("ads", "MobileAppController::getAds");


    $routes->get("fcm", "FcmController::sendPushNotification");


    /* add By bobby */

    $routes->get('/getinfo', 'Home::getinfo');
    $routes->get('/policy', 'Home::getPolicy');
    $routes->get('/privacy', 'Home::getPrivacy');


    $routes->get("crond", "CronController::index");
    $routes->get("testpush", "CronController::testPush");
    $routes->get("delivery", "DeliveryController::test");
    $routes->get("fcmwelcome", "WelcomePushController::sendWelcome");


    /* News */
    $routes->post("news", "NewsController::getNews");
    $routes->post("newsid", "NewsController::getDetail");

});
