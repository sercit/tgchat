<?php
namespace TGChat\Http\Controllers;


use http\Env\Request;
use Telegram;
use Log;
use TGChat\TelegramUser;
use Telegram\Bot\Keyboard\Keyboard;
use TGChat\User;
use TGChat\Order;

class BotController extends Controller
{
//    private static $token = "847119911:AAGA-qJu9WfPqQYFb7e0WTwt8QAfA0av7mo";
    public function index(){
        $update = Telegram::bot()->getWebhookUpdate();
        $callback_query = $update->getCallbackQuery();
        $message = $update->getMessage();


        if($message) {
            $user = $message->getFrom();
            Telegram::bot()->sendChatAction([
                'chat_id' => $user->getId(),
                'action'=>'typing',
            ]);
            if (!TelegramUser::find($user->getId())) {
                Telegram::bot()->sendMessage([
                    'chat_id' => $user->getId(),
                    'text' => 'Здравствуйте, ' . $user->getFirstName() . ' ' . $user->getLastName(),
                ]);
                $user = json_decode($user, true);
                $user['user_id'] = null; //оставляем id мастера пустым при регистрации.
                TelegramUser::create($user);

            } else {
                $master = TelegramUser::find($user->getId())->user;
                if ($master == null) {
                    Telegram::bot()->sendMessage([
                        'chat_id' => $user->getId(),
                        'text' => 'Здравствуйте, ' . $user->getFirstName() . ' ' . $user->getLastName(),
                    ]);

                } else {
                    $keyboard = Keyboard::make()
                        ->inline()
                        ->row(
                            Keyboard::inlineButton(['text' => 'Заказы', 'callback_data' => '{ "request":"orders", "master":"'.$master->id.'" }']),
                            Keyboard::inlineButton(['text' => 'Услуги', 'callback_data' => '{ "request":"services", "master":"'.$master->id.'" }'])
                        );
                    Telegram::bot()->sendMessage([
                        'chat_id' => $user->getId(),
                        'text' => 'Здарова, ' . $master->firstname . ' ' . $master->lastname,
                        'reply_markup' => $keyboard,
                    ]);

                }
            }
        }else if($callback_query){
            $master = TelegramUser::find($user->getId())->user;``
            $callback_data = $update->getCallbackQuery()->getData();
            $callback_data = json_decode($callback_data, true);
            if($callback_data['request'] == 'orders' && $callback_data['master']){
                $orders = User::find($master)->services()->orders()->get();
                foreach($orders as $order){
                    Telegram::bot()->sendMessage([
                        'chat_id' => $user->getId(),
                        'text' => $order->name,
                    ]);
                }
            }
        }


    }
}
