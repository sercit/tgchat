<?php

namespace TGChat\Http\Controllers;


use Auth;
use Request;
use Telegram;
use Log;
use TGChat\Client;
use TGChat\Event;
use TGChat\Service;
use TGChat\TelegramUser;
use Telegram\Bot\Keyboard\Keyboard;
use TGChat\User;
use TGChat\Order;

class BotController extends Controller
{

//    private static $token = "847119911:AAGA-qJu9WfPqQYFb7e0WTwt8QAfA0av7mo";
    public function keyboardMonthWithControls($orderId, $month = null, $year = null)
    {
        if($month == null || $year == null){
            $month = date('m');
            $year = date('Y');
        }
        if($month !== date('m') || $year !== date('Y')){
            $year = date('Y', mktime(0, 0, 0, $month , 1, $year));
            $month = date('m', mktime(0, 0, 0, $month , 1, $year));
        }
        // Вычисляем число дней в текущем месяце
        $dayofmonth = date('t', mktime(0,0,0,$month, 1,$year));

        // Счётчик для дней месяца
        $day_count = 1;

        // 1. Первая неделя
        $num = 0;
        for ($i = 0; $i < 7; $i++) {
            // Вычисляем номер дня недели для числа
            $dayofweek = date('w', mktime(0, 0, 0,$month, $day_count, $year));
            // Приводим к числа к формату 1 - понедельник, ..., 6 - суббота
            $dayofweek = $dayofweek - 1;
            if ($dayofweek == -1) $dayofweek = 6;
            if ($dayofweek == $i) {
                // Если дни недели совпадают,
                // заполняем массив $week
                // числами месяца
                $weeks[$num][$i] = $day_count;
                $day_count++;
            } else {
                $weeks[$num][$i] = "";
            }
        }
        // 2. Последующие недели месяца
        while (true) {
            $num++;
            for ($i = 0; $i < 7; $i++) {
                $weeks[$num][$i] = $day_count;
                $day_count++;
                // Если достигли конца месяца - выходим
                // из цикла
                if ($day_count > $dayofmonth) break;
            }
            // Если достигли конца месяца - выходим
            // из цикла
            if ($day_count > $dayofmonth) break;
        }
        foreach($weeks as $week){
            $keyboardWeek = [];
            foreach ($week as $day){
                $text = $day != '' ? $day.'.'.$month : ' ';
                $keyboardWeek[] = Keyboard::inlineButton(['text' => $text, 'callback_data' => '{"request":"createMasterOrderDate'.$day.'/'.$month.'/'.$year.'","order":"'.$orderId.'"}']);
            }
            $keyboardWeeks[] = $keyboardWeek;
        }
        $nextMonthNumber = date('m', mktime(0,0,0,$month+1, 1,$year));
        $prevMonthNumber = date('m', mktime(0,0,0,$month-1, 1,$year));
        $nextYearNumber = date('Y', mktime(0,0,0,$month+1, 1,$year));
        $prevYearNumber = date('Y', mktime(0,0,0,$month-1, 1,$year));

        //$array = [2,3,4];
        //return ...$array //2,3,4

        $keyboardControls[] = Keyboard::inlineButton(['text' => '<<', 'callback_data' => '{"request":"createMasterOrderClient'.$orderId.'","date":"'.$prevMonthNumber.'/'.$prevYearNumber.'"}']);
        $keyboardControls[] = Keyboard::inlineButton(['text' => '>>', 'callback_data' => '{"request":"createMasterOrderClient'.$orderId.'","date":"'.$nextMonthNumber.'/'.$nextYearNumber.'"}']);
        switch (count($keyboardWeeks)){
            case 4:
                $keyboard = Keyboard::make()
                    ->inline()
                    ->row(
                        ...$keyboardWeeks[0]
                    )->row(
                        ...$keyboardWeeks[1]
                    )->row(
                        ...$keyboardWeeks[2]
                    )->row(
                        ...$keyboardWeeks[3]
                    )->row(
                        ...$keyboardControls
                    );
                break;
            case 5:
                $keyboard = Keyboard::make()
                    ->inline()
                    ->row(
                        ...$keyboardWeeks[0]
                    )->row(
                        ...$keyboardWeeks[1]
                    )->row(
                        ...$keyboardWeeks[2]
                    )->row(
                        ...$keyboardWeeks[3]
                    )->row(
                        ...$keyboardWeeks[4]
                    )->row(
                        ...$keyboardControls
                    );
                break;
            case 6:

                $keyboard = Keyboard::make()
                    ->inline()
                    ->row(
                        ...$keyboardWeeks[0]
                    )->row(
                        ...$keyboardWeeks[1]
                    )->row(
                        ...$keyboardWeeks[2]
                    )->row(
                        ...$keyboardWeeks[3]
                    )->row(
                        ...$keyboardWeeks[4]
                    )->row(
                        ...$keyboardWeeks[5]
                    )->row(
                        ...$keyboardControls
                    );
                break;
        }
        return $keyboard;
    }

    public function keyboardMonthWithControlsForClient($orderId, $month = null, $year = null)
    {
        if($month == null || $year == null){
            $month = date('m');
            $year = date('Y');
        }
        if($month !== date('m') || $year !== date('Y')){
            $year = date('Y', mktime(0, 0, 0, $month , 1, $year));
            $month = date('m', mktime(0, 0, 0, $month , 1, $year));
        }
        // Вычисляем число дней в текущем месяце
        $dayofmonth = date('t', mktime(0,0,0,$month, 1,$year));

        // Счётчик для дней месяца
        $day_count = 1;

        // 1. Первая неделя
        $num = 0;
        for ($i = 0; $i < 7; $i++) {
            // Вычисляем номер дня недели для числа
            $dayofweek = date('w', mktime(0, 0, 0,$month, $day_count, $year));
            // Приводим к числа к формату 1 - понедельник, ..., 6 - суббота
            $dayofweek = $dayofweek - 1;
            if ($dayofweek == -1) $dayofweek = 6;
            if ($dayofweek == $i) {
                // Если дни недели совпадают,
                // заполняем массив $week
                // числами месяца
                $weeks[$num][$i] = $day_count;
                $day_count++;
            } else {
                $weeks[$num][$i] = "";
            }
        }
        // 2. Последующие недели месяца
        while (true) {
            $num++;
            for ($i = 0; $i < 7; $i++) {
                $weeks[$num][$i] = $day_count;
                $day_count++;
                // Если достигли конца месяца - выходим
                // из цикла
                if ($day_count > $dayofmonth) break;
            }
            // Если достигли конца месяца - выходим
            // из цикла
            if ($day_count > $dayofmonth) break;
        }
        foreach($weeks as $week){
            $keyboardWeek = [];
            foreach ($week as $day){
                $text = $day != '' ? $day.'.'.$month : ' ';
                $keyboardWeek[] = Keyboard::inlineButton(['text' => $text, 'callback_data' => '{"request":"clientOrder","date"'.$day.'/'.$month.'/'.$year.'","order":"'.$orderId.'"}']);
            }
            $keyboardWeeks[] = $keyboardWeek;
        }
        $nextMonthNumber = date('m', mktime(0,0,0,$month+1, 1,$year));
        $prevMonthNumber = date('m', mktime(0,0,0,$month-1, 1,$year));
        $nextYearNumber = date('Y', mktime(0,0,0,$month+1, 1,$year));
        $prevYearNumber = date('Y', mktime(0,0,0,$month-1, 1,$year));

        $keyboardControls[] = Keyboard::inlineButton(['text' => '<<', 'callback_data' => '{"request":"clientOrder'.$orderId.'","date":"'.$prevMonthNumber.'/'.$prevYearNumber.'"}']);
        $keyboardControls[] = Keyboard::inlineButton(['text' => '>>', 'callback_data' => '{"request":"clientOrder'.$orderId.'","date":"'.$nextMonthNumber.'/'.$nextYearNumber.'"}']);
        switch (count($keyboardWeeks)){
            case 4:
                $keyboard = Keyboard::make()
                    ->inline()
                    ->row(
                        ...$keyboardWeeks[0]
                    )->row(
                        ...$keyboardWeeks[1]
                    )->row(
                        ...$keyboardWeeks[2]
                    )->row(
                        ...$keyboardWeeks[3]
                    )->row(
                        ...$keyboardControls
                    );
                break;
            case 5:
                $keyboard = Keyboard::make()
                    ->inline()
                    ->row(
                        ...$keyboardWeeks[0]
                    )->row(
                        ...$keyboardWeeks[1]
                    )->row(
                        ...$keyboardWeeks[2]
                    )->row(
                        ...$keyboardWeeks[3]
                    )->row(
                        ...$keyboardWeeks[4]
                    )->row(
                        ...$keyboardControls
                    );
                break;
            case 6:

                $keyboard = Keyboard::make()
                    ->inline()
                    ->row(
                        ...$keyboardWeeks[0]
                    )->row(
                        ...$keyboardWeeks[1]
                    )->row(
                        ...$keyboardWeeks[2]
                    )->row(
                        ...$keyboardWeeks[3]
                    )->row(
                        ...$keyboardWeeks[4]
                    )->row(
                        ...$keyboardWeeks[5]
                    )->row(
                        ...$keyboardControls
                    );
                break;
        }
        return $keyboard;
    }

    public function keyboardAvailableTimeForOrder($order){
        $master = $order->service->user;
        $day = date('Y-m-d',strtotime($order->date));
        $dayOfWeek = date('l',strtotime($order->date));
        $schedule = json_decode($master->schedule, true);
        [$dayStart,$dayEnd] = explode('-',$schedule[$dayOfWeek]);
        $dayStartTimestamp = strtotime($day.' '.$dayStart);
        $dayEndTimestamp = strtotime($day.' '.$dayEnd);
        $availableTimes = [];

        $eventsForChosenDay = Event::where('start_date','LIKE', '%'.$day.'%')->orderBy('start_date','asc')->get();
        $i=0;
        $count = count($eventsForChosenDay);
        foreach ($eventsForChosenDay as $event){
            $i++;
            $duration = $event->service->duration;
            $gapEndTimestamp = strtotime($event->start_date);
            if($i == 1){
                $gapStartTimestamp = $dayStartTimestamp;
            }else{
                $prevEventStartTimestamp = strtotime($eventsForChosenDay[$i-2]->start_date);
                $prevEventDuration = $eventsForChosenDay[$i-1]->service->duration;
                $prevEventEndTimestamp = $prevEventStartTimestamp + $prevEventDuration*60;
                $gapStartTimestamp =  $prevEventEndTimestamp;
            }


            $gap = ($gapEndTimestamp - $gapStartTimestamp)/60;
            if($gap >= $duration){
                $availableTimes[] = [$gapStartTimestamp,$gapEndTimestamp];
            }
            if($i == $count){
                $gapStartTimestamp = strtotime($event->start_date) + $event->service->duration*60;
                $gapEndTimestamp = $dayEndTimestamp;
                $gap = ($gapEndTimestamp - $gapStartTimestamp)/60;
                if($gap >= $duration){
                    $availableTimes[] = [$gapStartTimestamp,$gapEndTimestamp];
                }
            }
        }
        $dayTimes = $dayStartTimestamp;
        $availableDayTimes = [];
        do{
            if($count == 0){
                if($dayTimes >= $dayStartTimestamp[0] && $dayTimes <= ($dayEndTimestamp - $order->service->duration*60)){
                    $availableDayTimes[] = $dayTimes;
                    $dayTimes += 15*60;
                    continue;
                }
            }
            foreach ($availableTimes as $availableTime){
                if($dayTimes >= $availableTime[0] && $dayTimes <= ($availableTime[1] - $order->service->duration*60)){
                    $availableDayTimes[] = $dayTimes;
                    break;
                }
            }
            $dayTimes += 15*60;
        }while($dayTimes < $dayEndTimestamp);
        $availableButtons = [];
        foreach ($availableDayTimes as $availableTime){
            $text = date('H:i',$availableTime);
            $availableButtons[] = Keyboard::inlineButton(['text' => $text, 'callback_data' => '{ "request":"createMasterOrderTime'.$availableTime.'","order":'.$order->id.'}']);
        }
        return $availableButtons;
    }

    public function sendClientMenu($telegramUser, $title = 'Меню')
    {
        $keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(['text' => 'Поиск мастера', 'callback_data' => '{ "request":"clientSearch"}']),
                Keyboard::inlineButton(['text' => 'Мои записи', 'callback_data' => '{ "request":"clientEvents"}'])
            )->row(
                Keyboard::inlineButton(['text' => 'Я мастер', 'callback_data' => '{ "request":"masterNew"}'])
            );
        Telegram::bot()->sendMessage([
            'chat_id' => $telegramUser->id,
            'text' => $title,
            'reply_markup' => $keyboard,
        ]);
    }

    public function sendMasterMenu($telegramUser, $text = 'Меню')
    {
        $keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(['text' => 'Заказы', 'callback_data' => '{"request":"orders"}']),
                Keyboard::inlineButton(['text' => 'Услуги', 'callback_data' => '{"request":"services"}'])
            )->row(
                Keyboard::inlineButton(['text' => 'Клиенты', 'callback_data' => '{"request":"clients"}']),
                Keyboard::inlineButton(['text' => 'Записи', 'callback_data' => '{"request":"events"}']))
            ->row(
                Keyboard::inlineButton(['text' => 'Профиль', 'callback_data' => '{"request":"profile"}']),
                Keyboard::inlineButton(['text' => 'Я клиент', 'callback_data' => '{"request":"clientNew"}']));
        Telegram::bot()->sendMessage([
            'chat_id' => $telegramUser->id,
            'text' => $text,
            'reply_markup' => $keyboard,
        ]);
        return null;
    }

    public function sendLastMessageAndSave($telegramUser, $lastMessage)
    {
        $telegramUser->last_message = $lastMessage;
        $telegramUser->save();
    }

    public function sendStartMessage($telegramUser)
    {
        $keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(['text' => 'Я мастер', 'callback_data' => '{ "request":"masterNew"}']),
                Keyboard::inlineButton(['text' => 'Я клиент', 'callback_data' => '{ "request":"clientNew"}'])
            );
        Telegram::bot()->sendMessage([
            'chat_id' => $telegramUser->getId(),
            'text' => 'Здравствуйте, ' . $telegramUser->getFirstName() . ' ' . $telegramUser->getLastName() . '.' . PHP_EOL .
                'Уточните, вы ищете мастера или сами мастер?',
            'reply_markup' => $keyboard,
        ]);
    }

    public function sendMasterServices($telegramUser)
    {
        $services = Service::where('user_id', $telegramUser->user_id)->get();

        foreach ($services as $service) {
            $keyboard = Keyboard::make()
                ->inline()
                ->row(
                    Keyboard::inlineButton(['text' => 'Изменить', 'callback_data' => '{"request":"changeService' . $service->id . '"}']),
                    Keyboard::inlineButton(['text' => 'Удалить', 'callback_data' => '{"request":"removeService' . $service->id . '"}'])
                );
            Telegram::bot()->sendMessage([
                'chat_id' => $telegramUser->id,
                'parse_mode' => 'Markdown',
                'reply_markup' => $keyboard,
                'text' => $service->service_name . PHP_EOL .
                    'Продолжительность - ' . $service->duration . PHP_EOL .
                    'Цена - ' . $service->amount,
            ]);
        }
        $keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(['text' => 'Добавить новую услугу', 'callback_data' => '{"request":"addService"}'])
            );
        Telegram::bot()->sendMessage([
            'chat_id' => $telegramUser->id,
            'parse_mode' => 'Markdown',
            'reply_markup' => $keyboard,
            'text' => 'Вы можете добавить услугу здесь:',
        ]);
    }

    public function sendMasterServicesForOrder($telegramUser)
    {
        $services = Service::where('user_id', $telegramUser->user_id)->get();
        if(count($services) > 0) {
            foreach ($services as $service) {
                $keyboard = Keyboard::make()
                    ->inline()
                    ->row(
                        Keyboard::inlineButton(['text' => 'Выбрать', 'callback_data' => '{"request":"createMasterOrderService' . $service->id . '"}'])
                    );
                Telegram::bot()->sendMessage([
                    'chat_id' => $telegramUser->id,
                    'parse_mode' => 'Markdown',
                    'reply_markup' => $keyboard,
                    'text' => $service->service_name . PHP_EOL .
                        'Продолжительность - ' . $service->duration . PHP_EOL .
                        'Цена - ' . $service->amount,
                ]);
            }
            $keyboard = Keyboard::make()
                ->inline()
                ->row(
                    Keyboard::inlineButton(['text' => 'Добавить новую услугу', 'callback_data' => '{"request":"addService"}'])
                );
            Telegram::bot()->sendMessage([
                'chat_id' => $telegramUser->id,
                'parse_mode' => 'Markdown',
                'reply_markup' => $keyboard,
                'text' => 'Также Вы можете добавить услугу',
            ]);
        }else{
            $keyboard = Keyboard::make()
                ->inline()
                ->row(
                    Keyboard::inlineButton(['text' => 'Добавить новую услугу', 'callback_data' => '{"request":"addService"}'])
                );
            Telegram::bot()->sendMessage([
                'chat_id' => $telegramUser->id,
                'parse_mode' => 'Markdown',
                'reply_markup' => $keyboard,
                'text' => 'Сначала нужно добавить услугу',
            ]);
        }
    }

    public function sendMasterClients($telegramUser)
    {
        $clients = Client::where('user_id', $telegramUser->user_id)->get();
        if(count($clients) > 0) {
            foreach ($clients as $client) {
                if($client->client_name != null && $client->phone != null) {
                    $keyboard = Keyboard::make()
                        ->inline()
                        ->row(
                            Keyboard::inlineButton(['text' => 'Изменить', 'callback_data' => '{"request":"changeClient' . $client->id . '"}']),
                            Keyboard::inlineButton(['text' => 'Удалить', 'callback_data' => '{"request":"removeClient' . $client->id . '"}'])
                        );
                    Telegram::bot()->sendMessage([
                        'chat_id' => $telegramUser->id,
                        'parse_mode' => 'Markdown',
                        'reply_markup' => $keyboard,
                        'text' => 'Имя - ' . $client->client_name . PHP_EOL .
                            'Телефон - ' . $client->phone . PHP_EOL,
                    ]);
                }
            }
        }
        $keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(['text' => 'Добавить нового клиента', 'callback_data' => '{"request":"addClient"}'])
            );
        Telegram::bot()->sendMessage([
            'chat_id' => $telegramUser->id,
            'parse_mode' => 'Markdown',
            'reply_markup' => $keyboard,
            'text' => 'Вы можете добавить нового клиента здесь:',
        ]);
    }

    public function sendMasterClientsForOrder($telegramUser, $orderId)
    {
        $clients = Client::where('user_id', $telegramUser->user_id)->get();
        if(count($clients) > 0) {
            foreach ($clients as $client) {
                if($client->client_name != null && $client->phone != null) {
                    $keyboard = Keyboard::make()
                        ->inline()
                        ->row(
                            Keyboard::inlineButton(['text' => 'Выбрать', 'callback_data' => '{"request":"createMasterOrderClient' . $client->id . '","order":"' . $orderId . '"}'])
                        );
                    Telegram::bot()->sendMessage([
                        'chat_id' => $telegramUser->id,
                        'parse_mode' => 'Markdown',
                        'reply_markup' => $keyboard,
                        'text' => 'Имя - ' . $client->client_name . PHP_EOL .
                            'Телефон - ' . $client->phone . PHP_EOL,
                    ]);
                }
            }
            $keyboard = Keyboard::make()
                ->inline()
                ->row(
                    Keyboard::inlineButton(['text' => 'Добавить нового клиента', 'callback_data' => '{"request":"addClient"}'])
                );
            Telegram::bot()->sendMessage([
                'chat_id' => $telegramUser->id,
                'parse_mode' => 'Markdown',
                'reply_markup' => $keyboard,
                'text' => 'Также Вы можете добавить нового клиента здесь',
            ]);
        }else{
            $keyboard = Keyboard::make()
                ->inline()
                ->row(
                    Keyboard::inlineButton(['text' => 'Добавить нового клиента', 'callback_data' => '{"request":"addClient"}'])
                );
            Telegram::bot()->sendMessage([
                'chat_id' => $telegramUser->id,
                'parse_mode' => 'Markdown',
                'reply_markup' => $keyboard,
                'text' => 'Сначала нужно добавить клиента',
            ]);
        }
    }

    public function sendMasterOrders($telegramUser)
    {
        $orders = Order::join('services', 'orders.service_id', '=', 'services.id')->select('services.*', 'orders.*')->where('services.user_id', $telegramUser->user_id)->get();
        if(count($orders) > 0) {
            foreach ($orders as $order) {
                if ($order->name == null || $order->phone == null || $order->date == null) {
                    continue;
                }
                $keyboard = Keyboard::make()
                    ->inline()
                    ->row(
                        Keyboard::inlineButton(['text' => 'Подтвердить', 'callback_data' => '{"request":"confirmOrder' . $order->id . '"}']),
                        Keyboard::inlineButton(['text' => 'Отменить', 'callback_data' => '{"request":"declineOrder' . $order->id . '"}'])
                    );
                Telegram::bot()->sendMessage([
                    'chat_id' => $telegramUser->id,
                    'parse_mode' => 'Markdown',
                    'text' => $order->service_name . PHP_EOL .
                        'Длительность - ' . $order->duration . PHP_EOL .
                        'Цена - ' . $order->amount . PHP_EOL .
                        'Имя клиента - ' . $order->name . PHP_EOL .
                        'Телефон - ' . $order->phone . PHP_EOL .
                        'Дата - ' . $order->date . PHP_EOL,
                    'reply_markup' => $keyboard,
                ]);
            }
        }else{
            Telegram::bot()->sendMessage([
                'chat_id' => $telegramUser->id,
                'parse_mode' => 'Markdown',
                'text' => 'Заказов не найдено',
            ]);
            $keyboard = Keyboard::make()
                ->inline()
                ->row(
                    Keyboard::inlineButton(['text' => 'Добавить заказ', 'callback_data' => '{"request":"createMasterOrderStart"}'])
                );
            Telegram::bot()->sendMessage([
                'chat_id' => $telegramUser->id,
                'parse_mode' => 'Markdown',
                'text' => 'Вы можете добавить заказ',
            ]);
        }
    }

    public function sendMasterEvents($telegramUser)
    {
        $events = Event::join('services', 'events.service_id', '=', 'services.id')->join('clients', 'events.client_id', '=', 'clients.id')->select('clients.*', 'services.*', 'events.*')->where('services.user_id', $telegramUser->user_id)->get();
        foreach ($events as $event) {
            $keyboard = Keyboard::make()
                ->inline()
                ->row(
                    Keyboard::inlineButton(['text' => 'Удалить', 'callback_data' => '{"request":"removeEvent' . $event->id . '"}'])
                );
            Telegram::bot()->sendMessage([
                'chat_id' => $telegramUser->id,
                'parse_mode' => 'Markdown',
                'text' => $event->service_name . PHP_EOL .
                    'Длительность - ' . $event->duration . PHP_EOL .
                    'Цена - ' . $event->amount . PHP_EOL .
                    'Имя клиента - ' . $event->client_name . PHP_EOL .
                    'Телефон - ' . $event->client_phone . PHP_EOL .
                    'Дата - ' . $event->start_date . PHP_EOL,
                'reply_markup' => $keyboard,
            ]);
        }
        if (!count($events)) {
            Telegram::bot()->sendMessage([
                'chat_id' => $telegramUser->id,
                'parse_mode' => 'Markdown',
                'text' => 'Записей не найдено',
            ]);
        }
        $keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(['text' => 'Добавить запись', 'callback_data' => '{"request":"createMasterOrderStart"}'])
            );
        Telegram::bot()->sendMessage([
            'chat_id' => $telegramUser->id,
            'parse_mode' => 'Markdown',
            'text' => 'Вы можете добавить новую запись:',
            'reply_markup' => $keyboard,
        ]);
    }

    public function sendMasterProfile($telegramUser,$text = 'Настройки профиля')
    {
        $keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(['text' => 'Время работы', 'callback_data' => '{"request":"masterSchedule"}']),
                Keyboard::inlineButton(['text' => 'Адрес', 'callback_data' => '{"request":"masterAddress"}'])
            )->row(
                Keyboard::inlineButton(['text' => 'E-mail', 'callback_data' => '{"request":"masterEmail"}']),
                Keyboard::inlineButton(['text' => 'Телефон', 'callback_data' => '{"request":"masterPhone"}']))
            ->row(
                Keyboard::inlineButton(['text' => 'Назад', 'callback_data' => '{"request":"profile"}']),
                Keyboard::inlineButton(['text' => 'Удалить профиль', 'callback_data' => '{"request":"clientNew"}'])
            );
        Telegram::bot()->sendMessage([
            'chat_id' => $telegramUser->id,
            'text' => $text,
            'reply_markup' => $keyboard,
        ]);
        return null;
    }

    public function sendMasterSchedule($telegramUser){
        $schedule = json_decode(User::find($telegramUser->user_id)->schedule);
        $i = 0;
        $days = ['Понедельник','Вторник','Среда','Четверг','Пятница','Суббота','Воскресенье',];
        foreach($schedule as $day => $time){
            $i++;
            $keyboard = Keyboard::make()
                ->inline()
                ->row(
                    Keyboard::inlineButton(['text' => 'Изменить', 'callback_data' => '{"request":"changeSchedule' . $i . 'beginHour"}'])
                );
            $text = $days[$i-1];
            Telegram::bot()->sendMessage([
                'chat_id' => $telegramUser->id,
                'text' => $text.":".PHP_EOL.
                $time,
                'reply_markup' => $keyboard,
            ]);
        }
    }

    public function createTelegramUser($telegramUser)
    {
        $telegramUser = json_decode($telegramUser, true);
        $telegramUser['user_id'] = null; //оставляем id мастера пустым при регистрации.
        $telegramUser['last_message'] = 'start';
        $telegramUser['verified_master'] = false;
        TelegramUser::create($telegramUser);
    }

    public function sendClientServices($telegramUser, $masterId)
    {
        $services = Service::where('user_id', $masterId)->get();
        if(count($services) > 0 ) {
            foreach ($services as $service) {
                $keyboard = Keyboard::make()
                    ->inline()
                    ->row(
                        Keyboard::inlineButton(['text' => 'Запись', 'callback_data' => '{ "request":"clientOrder' . $service->id . 'start"}'])
                    );
                Telegram::bot()->sendMessage([
                    'chat_id' => $telegramUser->id,
                    'text' => $service->service_name . PHP_EOL .
                        'Длительность - ' . $service->duration . PHP_EOL .
                        'Цена - ' . $service->amount,
                    'reply_markup' => $keyboard,
                ]);
            }
        }else{
            Telegram::bot()->sendMessage([
                'chat_id' => $telegramUser->id,
                'text' => 'К сожалению, у найденного мастера нет услуг.',
            ]);
        }

    }

    public function sendClientServiceAvailableTime($telegramUser, $service, $date)
    {
        $orderDate = date('Y-m-d', strtotime($date));
        $events = Event::where('events.user_id', $service->user_id)->join('services', 'events.service_id', '=', 'services.id')->select('services.*', 'events.*')->where('events.start_date', 'LIKE', '%' . $orderDate . '%')->orderBy('start_date')->get();
        $dayTimestamp = strtotime($orderDate);
        $workFromHour = 10;
        $workFromMinute = 30;
        $workToHour = 19;
        $workToMinute = 45;
        $availableTime = [];
        $dayStart = $dayTimestamp + ($workFromHour * 60 * 60) + ($workFromMinute * 60);
        $dayEnd = $dayEnd = $dayTimestamp + ($workToHour * 60 * 60) + ($workToMinute * 60);
        $elements = ($dayEnd - $dayStart) / (15 * 60);
        for ($i = 1; $i <= $elements; $i++) {
            $timeButtons[] = $dayStart + (($i - 1) * 15 * 60);
        }

        for ($i = 0; $i < count($events); $i++) {
            $eventStart = strtotime($events[$i]->start_date);
            if ($i == 0) {
                $previousEventEnd = $dayTimestamp + ($workFromHour * 60 * 60) + ($workFromMinute * 60);
            } else {
                $previousEventEnd = $events[$i - 1]->start_date + $events[$i - 1]->duration * 60;
            }
            if ($i + 1 == count($events)) {
                $lastEventEnd = strtotime($events[$i]->start_date) + $events[$i]->duration * 60;
                if ($dayEnd - $lastEventEnd > $service->duration * 60) {
                    $availableTime[] = [$lastEventEnd, $dayEnd - $service->duration * 60];
                }
            }
            if ($eventStart - $previousEventEnd > $events[$i]->duration * 60) {
                $availableTime[] = [$previousEventEnd, $eventStart - $service->duration * 60];
            }
        }
        if (count($events) == 0) {
            $availableTime[] = [$dayTimestamp + ($workFromHour * 60 * 60) + ($workFromMinute * 60), $dayTimestamp + ($workToHour * 60 * 60) + ($workToMinute * 60) - $service->duration * 60];
        }
        $availableButtons = [];
        foreach ($timeButtons as $timeButton) {
            foreach ($availableTime as $interval) {
                if ($timeButton >= $interval[0] && $timeButton <= $interval[1]) {
                    $availableButtons[] = $timeButton;
                    continue 2;
                }
            }
        }

        foreach ($availableButtons as $availableButton) {
            $keyboardElements[] = Keyboard::inlineButton(['text' => date('H:i', $availableButton), 'callback_data' => '{ "request":"clientOrder' . $service->id . 'time' . $availableButton . '"}']);
            Telegram::bot()->sendMessage([
                'chat_id' => $telegramUser->id,
                'text' => date('H:i', $availableButton),
            ]);

        }
    }

    public function index()
    {
        $update = Telegram::bot()->getWebhookUpdate();
        $callback_query = $update->getCallbackQuery();
        $message = $update->getMessage();


        if ($message) {        //набрано сообщение
            $telegramUser = $message->getFrom();

            Telegram::bot()->sendChatAction([
                'chat_id' => $telegramUser->getId(),
                'action' => 'typing',
            ]);
            if (!TelegramUser::find($telegramUser->getId())) { //Если новый пользователь telegram
                $this->sendStartMessage($telegramUser);
                $this->createTelegramUser($telegramUser);
            } else {
                $telegramUser = TelegramUser::find($telegramUser->getId());
                $master = $telegramUser->user_id;
                if ($master == null) {
                    if ($telegramUser->last_message == 'masterStart') {
                        $email = $message->getText();
                        $user = User::where('email', $email)->first();
                        if (!is_object($user)) {
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => 'Нет такого E-mail!',
                            ]);
                        } else {
                            if ($user->email == $email) {
                                Telegram::bot()->sendMessage([
                                    'chat_id' => $telegramUser->id,
                                    'text' => 'Успешно, введите код из профиля',
                                ]);
                                $telegramUser->user_id = $user->id;
                                $this->sendLastMessageAndSave($telegramUser, 'masterVerification');
                            }
                        }
                    }
                    if (stripos($telegramUser->last_message, 'clientOrder') !== false) {
                        if (strpos($telegramUser->last_message, 'date') !== false) {
                            $serviceId = intval(substr($telegramUser->last_message, 11));
                            $service = Service::find($serviceId)->first();
                            $date = $message->getText();
                            $this->sendClientServiceAvailableTime($telegramUser, $service, $date);
                            $this->sendLastMessageAndSave($telegramUser, 'clientOrder' . $serviceId . 'time');
                        }
                    }
                } else {
                    if ($telegramUser->last_message == 'masterVerification' && $telegramUser->verified_master == false) {
                        $code = $message->getText();
                        $user = User::where('id',$master)->first();
                        if ($user->telegram_user_token !== $code) {
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => 'Неправильный код, попробуйте еще раз!',
                            ]);
                        } else {
                            if ($user->telegram_user_token == $code) {
                                Telegram::bot()->sendMessage([
                                    'chat_id' => $telegramUser->id,
                                    'text' => 'Успешно!',
                                ]);
                                $user->telegram_user_id = $telegramUser->id;
                                $user->save();
                                $this->sendMasterMenu($telegramUser, 'Добрый день, мастер ' . $user->firstname . ' ' . $user->lastname . '!');
                                $telegramUser->user_id = $user->id;
                                $telegramUser->verified_master = true;
                                $this->sendLastMessageAndSave($telegramUser, 'masterStart');
                            }
                        }
                    }
                    if (stripos($telegramUser->last_message, 'changeService') !== false) {
                        $serviceId = intval(substr($telegramUser->last_message, 13));
                        $last_message = explode($serviceId, $telegramUser->last_message);
                        if ($last_message[1] === 'name') {
                            $service = Service::find($serviceId)->first();
                            $service->service_name = $message->getText();
                            $service->save();
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => 'Новое название услуги - ' . $service->service_name . PHP_EOL .
                                    'Продолжительность услуги - ' . $service->duration . PHP_EOL .
                                    'Введите новую продолжительность',
                            ]);
                            $this->sendLastMessageAndSave($telegramUser, $last_message[0] . $serviceId . 'duration');
                        }
                        if ($last_message[1] === 'duration') {
                            $service = Service::find($serviceId)->first();
                            $service->duration = intval($message->getText());
                            $service->save();
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => 'Новое название услуги - ' . $service->service_name . PHP_EOL .
                                    'Новая продолжительность услуги - ' . $service->duration . PHP_EOL .
                                    'Стоимость услуги - ' . $service->amount . PHP_EOL .
                                    'Введите новую стоимость',
                            ]);
                            $this->sendLastMessageAndSave($telegramUser, $last_message[0] . $serviceId . 'amount');
                        }
                        if ($last_message[1] === 'amount') {
                            $service = Service::find($serviceId)->first();
                            $service->amount = $message->getText();
                            $service->save();
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => 'Новое название услуги - ' . $service->service_name . PHP_EOL .
                                    'Новая продолжительность услуги - ' . $service->duration . PHP_EOL .
                                    'Новая стоимость услуги - ' . $service->amount,
                            ]);
                            $this->sendLastMessageAndSave($telegramUser, 'services');
                            $services = Service::where('user_id', $master)->get();
                            $this->sendMasterServices($telegramUser, $master);
                            $this->sendMasterMenu($telegramUser);
                            $this->sendLastMessageAndSave($telegramUser, 'masterServices');
                        }
                        $service->save();
                    }
                    if (stripos($telegramUser->last_message, 'masterServicesAdd') !== false) {

                        $action = substr($telegramUser->last_message, 17);
                        if ($action === 'Name') {
                            $service = new Service();
                            $service->user_id = $telegramUser->user_id;
                            $service->service_name = $message->getText();
                            $service->duration = 0;
                            $service->amount = 0;
                            $service->save();
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => 'Название новой услуги - ' . $service->service_name . PHP_EOL .
                                    'Введите продолжительность новой услуги(в минутах):',
                            ]);
                            $this->sendLastMessageAndSave($telegramUser, 'masterServicesAddDuration'.$service->id);
                        }
                        if (stripos($action, 'Duration') !== false) {
                            $serviceId = intval(substr($action,8));
                            $service = Service::where('id',$serviceId)->first();
                            $service->duration = intval($message->getText());
                            $service->save();
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => 'Название новой услуги - ' . $service->service_name . PHP_EOL .
                                    'Продолжительность новой услуги(в минутах) - ' . $service->duration . PHP_EOL .
                                    'Введите стоимость новой услуги:',
                            ]);
                            $this->sendLastMessageAndSave($telegramUser, 'masterServicesAddAmount'.$service->id);
                        }
                        if (stripos($action, 'Amount') !== false) {
                            $serviceId = intval(substr($action,6));
                            $service = Service::where('id',$serviceId)->first();
                            $service->amount = intval($message->getText());
                            $service->save();
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => 'Название новой услуги - ' . $service->service_name . PHP_EOL .
                                    'Продолжительность новой услуги(в минутах) - ' . $service->duration . PHP_EOL .
                                    'Стоимость новой услуги - ' . $service->amount.PHP_EOL.
                                'Услуга сохранена',
                            ]);
                            $this->sendLastMessageAndSave($telegramUser, 'masterServicesAdd'.$service->id);
                            $this->sendMasterMenu($telegramUser);
                        }
                    }
                    if (stripos($telegramUser->last_message, 'changeClient') !== false) {
                        $clientId = intval(substr($telegramUser->last_message, 12));
                        $last_message = explode($clientId, $telegramUser->last_message);
                        if ($last_message[1] === 'name') {
                            $client = Client::find($clientId)->first();
                            $client->client_name = $message->getText();
                            $client->save();
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => 'Новое имя клиента - ' . $client->client_name . PHP_EOL .
                                    'Телефон - ' . $client->phone . PHP_EOL .
                                    'Введите новый телефон',
                            ]);
                            $this->sendLastMessageAndSave($telegramUser, $last_message[0] . $clientId . 'phone');
                        }
                        if ($last_message[1] === 'phone') {
                            $client = Client::find($clientId)->first();
                            $client->phone = $message->getText();
                            $client->save();
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => 'Клиент изменен',
                            ]);

                            $this->sendLastMessageAndSave($telegramUser, 'clients');
                            $this->sendMasterClients($telegramUser);
                            $this->sendMasterMenu($telegramUser);
                            $this->sendLastMessageAndSave($telegramUser, 'masterServices');
                        }
                    }
                    if (stripos($telegramUser->last_message, 'addClient') !== false) {
                        $action = substr($telegramUser->last_message, 9);
                        if ($action === 'name') {
                            $client = new Client();
                            $client->client_name = $message->getText();
                            $client->user_id = $telegramUser->user_id;
                            $client->save();
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => 'Имя нового клиента - ' . $client->client_name . PHP_EOL .
                                    'Введите телефон нового клиента',
                            ]);
                            $this->sendLastMessageAndSave($telegramUser, 'addClient' . $client->id . 'phone');
                        }else if (stripos($telegramUser->last_message, 'phone') !== false) {
                            $clientId = intval(substr($telegramUser->last_message, 9));
                            $client = Client::where('id',$clientId)->first();
                            $client->phone = $message->getText();
                            $client->save();
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => 'Клиент сохранен',
                            ]);

                            $this->sendLastMessageAndSave($telegramUser, 'clients');
                            $this->sendMasterClients($telegramUser);
                            $this->sendMasterMenu($telegramUser);
                            $this->sendLastMessageAndSave($telegramUser, 'masterServices');
                        }
                    }
                }
                if ($telegramUser->last_message == 'clientSearch') {
                    $master = User::where('phone', $message->getText())->first();
                    if ($master == null) {
                        Telegram::bot()->sendMessage([
                            'chat_id' => $telegramUser->id,
                            'text' => 'Мастера с таким номером не найдено, попробуйте еще раз!',
                        ]);
                    } else {
                        Telegram::bot()->sendMessage([
                            'chat_id' => $telegramUser->id,
                            'text' => 'Найден мастер ' . $master->firstname . ' ' . $master->lastname . '!',
                        ]);
                        $this->sendClientServices($telegramUser, $master->id);
                    }
                }
            }
        } else if ($callback_query) {
            $callback_data = json_decode($callback_query->getData(), true);
            $telegramFrom = $callback_query->getFrom();
            Telegram::bot()->sendChatAction([
                'chat_id' => $telegramFrom->getId(),
                'action' => 'typing',
            ]);
            if (!TelegramUser::find($telegramFrom->getId())) { //Если новый пользователь telegram
                $this->createTelegramUser($telegramFrom);
            }
            $telegramUser = TelegramUser::where('id',$telegramFrom->getId())->first();

            if ($telegramUser->verified_master) {
                $user = User::where('id',$telegramUser->user_id)->first();
                if (is_object($user)) {
                    if ($callback_data['request'] == 'services') {
                        $this->sendMasterServices($telegramUser);
                        $this->sendMasterMenu($telegramUser);
                        $this->sendLastMessageAndSave($telegramUser, 'masterServices');
                    }
                    if ($callback_data['request'] == 'orders') {
                        $this->sendMasterOrders($telegramUser);
                        $this->sendMasterMenu($telegramUser);
                        $this->sendLastMessageAndSave($telegramUser, 'masterOrders');
                    }
                    if ($callback_data['request'] == 'masterSchedule') {
                        $this->sendMasterSchedule($telegramUser);
                        $this->sendMasterMenu($telegramUser);
                        $this->sendLastMessageAndSave($telegramUser, 'masterOrders');
                    }
                    if ($callback_data['request'] == 'events') {

                        $this->sendMasterEvents($telegramUser);
                        $this->sendMasterMenu($telegramUser);
                        $this->sendLastMessageAndSave($telegramUser, 'masterEvents');

                    }
                    if ($callback_data['request'] == 'clients') {
                        $this->sendMasterClients($telegramUser);
                        $this->sendMasterMenu($telegramUser);
                        $this->sendLastMessageAndSave($telegramUser, 'masterClients');
                    }
                    if ($callback_data['request'] == 'profile') {
                        $this->sendMasterProfile($telegramUser);
                        $this->sendMasterMenu($telegramUser);
                        $this->sendLastMessageAndSave($telegramUser, 'masterProfile');
                    }
                    if ($callback_data['request'] == 'addClient') {
                        Telegram::bot()->sendMessage([
                            'chat_id' => $telegramUser->id,
                            'text' => 'Введите имя нового клиента:',
                        ]);
                        $this->sendLastMessageAndSave($telegramUser, 'addClientname');
                    }
                    if (stripos($callback_data['request'], 'changeService') !== false) {
                        $serviceId = intval(substr($callback_data['request'], 13));
                        $service = Service::find($serviceId)->first();
                        Telegram::bot()->sendMessage([
                            'chat_id' => $telegramUser->id,
                            'text' => 'Текущее название услуги - ' . $service->service_name . PHP_EOL .
                                'Введите новое название услуги',
                        ]);
                        $this->sendLastMessageAndSave($telegramUser, $callback_data['request'] . 'name');
                    }
                    if (stripos($callback_data['request'], 'removeService') !== false) {
                        $serviceId = intval(substr($callback_data['request'], 13));
                        $service = Service::find($serviceId);
                        $service->delete();
                        $this->sendMasterServices($telegramUser);
                        $this->sendMasterMenu($telegramUser);
                        $this->sendLastMessageAndSave($telegramUser, 'masterServices');
                    }
                    if (stripos($callback_data['request'], 'changeClient') !== false) {
                        $clientId = intval(substr($callback_data['request'], 12));
                        $client = Client::find($clientId)->first();
                        Telegram::bot()->sendMessage([
                            'chat_id' => $telegramUser->id,
                            'text' => 'Текущее имя клиента - ' . $client->client_name . PHP_EOL .
                                'Введите новое',
                        ]);
                        $telegramUser->last_message = $callback_data['request'] . 'name';
                        $telegramUser->save();
                    }
                    if (stripos($callback_data['request'], 'removeClient') !== false) {
                        $clientId = intval(substr($callback_data['request'], 12));
                        $client = Client::find($clientId);
                        $client->events()->delete();
                        $client->delete();
                        $this->sendMasterClients($telegramUser, $user->id);
                        $this->sendMasterMenu($telegramUser);
                        $this->sendLastMessageAndSave($telegramUser, 'masterServices');

                    }
                    if (stripos($callback_data['request'], 'confirmOrder') !== false) {

                        $orderId = intval(substr($callback_data['request'], 12));
                        $order = Order::where('id',$orderId)->first();

                        $event = new Event();
                        $event->service_id = $order->service_id;
                        $event->start_date = $order->date;
                        $old_client = Client::where('client_name', $order->name)->where('phone', $order->phone)->where('user_id', $telegramUser->user_id)->get()->first();

                        if ($old_client === null) {  //if this master didn't have client with this data, it'll add it
                            $client = new Client();
                            $client->client_name = $order->name;
                            $client->phone = $order->phone;
                            $client->user_id = $user->id;
                            $client->save();
                        } else {
                            $client = $old_client;
                        }
                        $event->client_id = $client->id;
                        $event->user_id = $user->id;
                        $event->save();
                        $order->delete();

                        $clientTelegramId = $order->telegram_user_id;
                        if($clientTelegramId != $telegramUser->id) {
                            Telegram::bot()->sendMessage([
                                'chat_id' => $clientTelegramId,
                                'parse_mode' => 'Markdown',
                                'text' => 'Ваш заказ на ' . $order->service->service_name . ' был принят мастером.' . PHP_EOL,
                            ]);
                        }

                        Telegram::bot()->sendMessage([
                            'chat_id' => $telegramUser->id,
                            'text' => 'Заказ подтвержден',
                        ]);
                        $this->sendMasterOrders($telegramUser);
                        $this->sendLastMessageAndSave($telegramUser, 'masterOrder');
                        $this->sendMasterMenu($telegramUser);
                    }
                    if (stripos($callback_data['request'], 'declineOrder') !== false) {
                        $orderId = intval(substr($callback_data['request'], 12));
                        $order = Order::where('id',$orderId)->first();
                        $clientTelegramId = $order->telegram_user_id;
                        if($clientTelegramId != $telegramUser->id) {
                            Telegram::bot()->sendMessage([
                                'chat_id' => $clientTelegramId,
                                'parse_mode' => 'Markdown',
                                'text' => 'Ваш заказ на ' . $order->service->service_name . ' был отменен мастером.' . PHP_EOL,
                            ]);
                        }
                        $order->delete();
                        $this->sendMasterOrders($telegramUser);
                        $this->sendMasterMenu($telegramUser);
                        $this->sendLastMessageAndSave($telegramUser, 'masterOrders');
                    }
                    if (stripos($callback_data['request'], 'removeEvent') !== false) {
                        $eventId = intval(substr($callback_data['request'], 11));
                        $event = Event::find($eventId)->first();
                        $event->delete();
                        $this->sendMasterEvents($telegramUser);
                        $this->sendMasterMenu($telegramUser);
                        $this->sendLastMessageAndSave($telegramUser, 'masterEvents');
                    }
                    if (stripos($callback_data['request'], 'createEvent') !== false) {
                        $action = substr($callback_data['request'], 11);
                        if ($action == 'Service') {
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => 'Выберите услугу для записи',
                            ]);
                            $this->sendClientServices($telegramUser, $user->id);
                        } elseif (stripos($action, 'Client') == 0) {
                            $serviceId = substr($action, 7);
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => 'Выберите клиента для записи',
                            ]);
                            $this->sendMasterClientsForEvent($telegramUser);
                        } elseif (stripos($action, 'Date') == 0) {
//                                $eventInfo = explode()
                        }
                        $eventId = intval(substr($callback_data['request'], 11));
                    }
                    if (stripos($callback_data['request'], 'addService') !== false) {
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => 'Введите название новой услуги',
                            ]);
                           $this->sendLastMessageAndSave($telegramUser, 'masterServicesAddName');
                    }
                    if (stripos($callback_data['request'], 'changeSchedule') !== false) {
                        $day = intval(substr($callback_data['request'], 14));
                        $schedule = User::where('id', $telegramUser->user_id)->select('schedule')->get()->first();
                        $dayNames = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                        Log::debug($schedule[0]['schedule']);
                        Log::debug($dayNames[$day-1]);
                        $daySchedule = explode('-',$schedule[0]['schedule'][''.$dayNames[$day-1]]);
                        if(stripos($callback_data['request'], 'beginHour') !== false){
                            $keyboard = Keyboard::make()
                                ->inline()
                                ->row(
                                    Keyboard::inlineButton(['text' => '00', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes00"}']),
                                    Keyboard::inlineButton(['text' => '01', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes01"}']),
                                    Keyboard::inlineButton(['text' => '02', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes02"}']),
                                    Keyboard::inlineButton(['text' => '03', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes03"}'])
                                )->row(
                                    Keyboard::inlineButton(['text' => '04', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes04"}']),
                                    Keyboard::inlineButton(['text' => '05', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes05"}']),
                                    Keyboard::inlineButton(['text' => '06', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes06"}']),
                                    Keyboard::inlineButton(['text' => '07', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes07"}'])
                                )->row(
                                    Keyboard::inlineButton(['text' => '08', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes08"}']),
                                    Keyboard::inlineButton(['text' => '09', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes09"}']),
                                    Keyboard::inlineButton(['text' => '10', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes10"}']),
                                    Keyboard::inlineButton(['text' => '11', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes11"}'])
                                )->row(
                                    Keyboard::inlineButton(['text' => '12', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes12"}']),
                                    Keyboard::inlineButton(['text' => '13', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes13"}']),
                                    Keyboard::inlineButton(['text' => '14', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes14"}']),
                                    Keyboard::inlineButton(['text' => '15', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes15"}'])
                                )->row(
                                    Keyboard::inlineButton(['text' => '16', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes16"}']),
                                    Keyboard::inlineButton(['text' => '17', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes17"}']),
                                    Keyboard::inlineButton(['text' => '18', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes18"}']),
                                    Keyboard::inlineButton(['text' => '19', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes19"}'])
                                )->row(
                                    Keyboard::inlineButton(['text' => '20', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes20}']),
                                    Keyboard::inlineButton(['text' => '21', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes21}']),
                                    Keyboard::inlineButton(['text' => '22', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes22}']),
                                    Keyboard::inlineButton(['text' => '23', 'callback_data' => '{"request":"changeSchedule'.$day.'beginMinutes23}'])
                                );
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => "Текущее время начала работы - $daySchedule[0]".PHP_EOL.
                                'Введите новое время начала:',
                                'reply_markup' => $keyboard,
                            ]);
                        }else if(stripos($callback_data['request'], 'beginMinutes') !== false){
                            $beginHour = intval(substr($callback_data['request'], 27));
                            $keyboard = Keyboard::make()
                                ->inline()
                                ->row(
                                    Keyboard::inlineButton(['text' => ''.$beginHour.':00', 'callback_data' => '{"request":"changeSchedule'.$beginHour.'endHours00"}']),
                                    Keyboard::inlineButton(['text' => ''.$beginHour.':15', 'callback_data' => '{"request":"changeSchedule'.$beginHour.'beginMinutes15"}']),
                                    Keyboard::inlineButton(['text' => ''.$beginHour.':30', 'callback_data' => '{"request":"changeSchedule'.$beginHour.'beginMinutes30"}']),
                                    Keyboard::inlineButton(['text' => ''.$beginHour.':45', 'callback_data' => '{"request":"changeSchedule'.$beginHour.'beginMinutes45"}'])
                                );
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => "Выбран час - $beginHour".PHP_EOL.
                                    'Введите новое время начала:',
                                'reply_markup' => $keyboard,
                            ]);
                        }
                    }
                }
            }
            if ($callback_data['request'] == 'clientNew') {
                $this->sendClientMenu($telegramUser, 'Здравствуйте, ' . $telegramUser->first_name . ' ' . $telegramUser->last_name . '!');
                $this->sendLastMessageAndSave($telegramUser, 'clientStart');
            }
            if ($callback_data['request'] == 'clientSearch') {
                Telegram::bot()->sendMessage([
                    'chat_id' => $telegramUser->id,
                    'text' => 'Введите номер мастера, через 7:',
                ]);
                $this->sendLastMessageAndSave($telegramUser, 'clientSearch');
            }
            if ($callback_data['request'] == 'masterNew') {
                if ($telegramUser->user_id == null) {
                    Telegram::bot()->sendMessage([
                        'chat_id' => $telegramUser->id,
                        'text' => 'Введите email:',
                    ]);
                    $this->sendLastMessageAndSave($telegramUser, 'masterStart');
                } else {
                    $this->sendMasterMenu($telegramUser);
                }

            }
            if ($callback_data['request'] == 'createMasterOrderStart') {
                Telegram::bot()->sendMessage([
                    'chat_id' => $telegramUser->id,
                    'text' => 'Выберите услугу для записи:',
                ]);
                $this->sendMasterServicesForOrder($telegramUser);
            }
            if (stripos($callback_data['request'], 'createMasterOrderService') !== false) {
                $serviceId = intval(substr($callback_data['request'], 24));
                $order = new Order();
                $order->service_id = $serviceId;
                $order->save();
                Telegram::bot()->sendMessage([
                    'chat_id' => $telegramUser->id,
                    'text' => 'Выберите клиента для записи:',
                ]);
                $this->sendMasterClientsForOrder($telegramUser, $order->id);
            }
            if (stripos($callback_data['request'], 'createMasterOrderClient') !== false) {
                if(!array_key_exists('date',$callback_data)) {
                    $clientId = intval(substr($callback_data['request'], 23));
                    $client = Client::where('id',$clientId)->first();
                    $orderId = $callback_data['order'];
                    $order = Order::where('id',$orderId)->first();
                    $order->client_id = $client->id;
                    $order->name = $client->client_name;
                    Log::debug($telegramFrom->getId());
                    $order->telegram_user_id = $telegramFrom->getId();
                    $order->phone = $client->phone;
                    $order->save();
                    Telegram::bot()->sendMessage([
                        'chat_id' => $telegramUser->id,
                        'reply_markup' => $this->keyboardMonthWithControls($order->id),
                        //                    'reply_markup' => $keyboard,

                        'text' => 'Выберите дату для записи:',
                    ]);
                }else{
                    $orderId = intval(substr($callback_data['request'], 23));
                    $order = Order::where('id',$orderId)->first();
                    $date = explode('/', $callback_data['date']);
                    Telegram::bot()->sendMessage([
                        'chat_id' => $telegramUser->id,
                        'reply_markup' => $this->keyboardMonthWithControls($order->id,...$date),
                        'text' => 'Выберите дату для записи:'.PHP_EOL.
                        $date[0].'/'.$date[1],
                    ]);
                }
            }
            if (stripos($callback_data['request'], 'createMasterOrderDate') !== false) {
                $date = explode('/',substr($callback_data['request'], 21));
                $order = Order::where('id',$callback_data['order'])->first();
                $order->date = date('Y-m-d H:i:s',mktime(0,0,0,$date[1],$date[0],$date[2]));
                $order->save();
                $keyboard =Keyboard::make()->inline();
                $keyboardData = $this->keyboardAvailableTimeForOrder($order);
                if($keyboardData == []){
                    $keyboard->row('К сожалению, в этот день все занято');
                }
                $buttonsForKeyboard = array_chunk($this->keyboardAvailableTimeForOrder($order), 4);
                foreach ($buttonsForKeyboard as $buttons){
                    $keyboard->row(...$buttons);
                }


                Telegram::bot()->sendMessage([
                    'chat_id' => $telegramUser->id,
                    'reply_markup' => $keyboard,
                    'text' => 'Выберите время для записи:'.PHP_EOL.
                        $date[0].'/'.$date[1].'/'.$date[2],
                ]);
            }
            if (stripos($callback_data['request'], 'createMasterOrderTime') !== false) {
                $date = substr($callback_data['request'], 21);
                $order = Order::where('id',$callback_data['order'])->first();
                $order->date = date('Y-m-d H:i:s',$date);
                $order->save();
                Log::debug($order);
                Telegram::bot()->sendMessage([
                    'chat_id' => $telegramUser->id,
                    'text' => 'Заказ сохранен. Для его подтверждения зайдите в список заказов',
                ]);
                $this->sendMasterMenu($telegramUser);
            }
            if (stripos($callback_data['request'], 'clientOrder') !== false) {
                if(!array_key_exists('date',$callback_data)) {
                    $serviceId = intval(substr($callback_data['request'], 11));
                    $order = new Order();
                    $order->service_id = $serviceId;
                    $order->name = $telegramUser->first_name.' '.$telegramUser->last_name;
                    $order->save();
                    $service = Service::where('id',$serviceId)->first();
                    Telegram::bot()->sendMessage([
                        'chat_id' => $telegramUser->id,
                        'text' => 'Выберите желаемую дату для записи на "' . $service->service_name.'"',
                        'reply_markup' =>$this->keyboardMonthWithControls($order->id)
                    ]);
                }elseif(!array_key_exists('order',$callback_data)){
                    $orderId = intval(substr($callback_data['request'], 11));
                    $date = explode('/',$callback_data['date']);
                    $service = Service::find($serviceId)->first();
                    Telegram::bot()->sendMessage([
                        'chat_id' => $telegramUser->id,
                        'text' => 'Выберите желаемую дату для записи на "' . $service->service_name.'"'.PHP_EOL.
                            $date[0].'/'.$date[1],
                        'reply_markup' =>$this->keyboardMonthWithControls($orderId, $date[0], $date[1])
                    ]);
                }else{
                    $date = explode('/',$callback_data['date']);
                    $orderId = intval(substr($callback_data['request'], 11));
                    $order = Order::where('id',$orderId)->first();
                    $order->telegram_user_id = $telegramFrom->getId();
                    $order->date = date('Y-m-d H:i:s',mktime(0,0,0,$date[1],$date[0],$date[2]));
                    $order->save();
                }
            }
        }
    }
}
