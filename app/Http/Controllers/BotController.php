<?php
namespace TGChat\Http\Controllers;
class BotController extends Controller
{
    private static $token = "847119911:AAGA-qJu9WfPqQYFb7e0WTwt8QAfA0av7mo";
    public static function sendMessage($service,$chatid)
    {
        $mess = "Услуга \"".$service."\"создана!"; //сообщение, которое мы удем оправлять

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.telegram.org/bot".self::$token."/sendMessage?chat_id=".$chatid."&text=".urlencode($mess),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }



    }
}