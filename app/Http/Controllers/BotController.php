<?php
namespace TGChat\Http\Controllers;


use http\Env\Request;
use Telegram;
use Log;
use TGChat\TelegramUser;

class BotController extends Controller
{
//    private static $token = "847119911:AAGA-qJu9WfPqQYFb7e0WTwt8QAfA0av7mo";
    public function index(){
        $update = Telegram::bot()->getWebhookUpdate();
        $message = $update->getMessage();
        $user = $message->getFrom();
        Telegram::bot()->sendChatAction([
            'chat_id' => $user->getId(),
            'action'=>'typing',
        ]);
        Log::debug(TelegramUser::find($user->getId()));
        if(!TelegramUser::find($user->getId())){
            Log::debug('123123');
            Telegram::bot()->sendMessage([
                'chat_id' => $user->getId(),
                'text' => 'Здравствуйте, '.$user->getFirstName().' '.$user->getLastName(),
            ]);
            $user = json_decode($user, true);
            $user['user_id'] = null; //оставляем id мастера пустым при регистрации.
            TelegramUser::create($user);
        }else{
            $master = TelegramUser::find($user->getId())->user;

            if($master == null){
                Telegram::bot()->sendMessage([
                    'chat_id' => $user->getId(),
                    'text' => 'Здравствуйте, '.$user->getFirstName().' '.$user->getLastName(),
                ]);
            }
            Telegram::bot()->sendMessage([
                'chat_id' => $user->getId(),
                'text' => 'Здравствуйте, '.$master->firstname.' '.$master->lastname,
            ]);
        }


    }
//    public function setWebhook(Request $request){
//        $result = $this->sendTelegramData('setwebhook',[
//            'query' => ['url'=> $request->url .'/'. \Telegram::getAccessToken()]
//        ]);
//        return redirect()->route('home')->with('status', $result);
//    }
//    public function getWebhookInfo(Request $request){
//        $result = $this->sendTelegramData('getWebhookInfo');
//        return redirect()->route('home')->with('status', $result);
//    }
//    public function sendTelegramData($route = '', $params= [], $method = 'POST'){
//        $client = new \GuzzleHttp\Client( ['base_uri' => 'https://api.telegram.org/bot'. \Telegram::getAccessToken().'/' ] );
//        $result = $client->request($method, $route, $params);
//        return (string) $result->getBody();
//    }
//
//    public static function sendMessage($service,$chatid)
//    {
//        $mess = "Услуга \"".$service."\"создана!"; //сообщение, которое мы удем оправлять
//
//        $curl = curl_init();
//
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => "https://api.telegram.org/bot".self::$token."/sendMessage?chat_id=".$chatid."&text=".urlencode($mess),
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => "",
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 30,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => "GET",
//            CURLOPT_POSTFIELDS => "",
//            CURLOPT_HTTPHEADER => array(
//                "cache-control: no-cache"
//            ),
//        ));
//
//        $response = curl_exec($curl);
//        $err = curl_error($curl);
//
//        curl_close($curl);
//
//        if ($err) {
//            return "cURL Error #:" . $err;
//        } else {
//            return $response;
//        }
//
//
//
//    }
}
