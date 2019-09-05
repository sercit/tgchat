<?php

namespace TGChat\Http\Controllers;


use Auth;
use Illuminate\Support\Facades\Date;
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
use function Psy\debug;

class BotController extends Controller
{

    public $nowDateTime;
//    private static $token = "847119911:AAGA-qJu9WfPqQYFb7e0WTwt8QAfA0av7mo";
    public function keyboardMonthWithControlsForBreak($eventId, $month = null, $year = null)
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
                $keyboardWeek[] = Keyboard::inlineButton(['text' => $text, 'callback_data' => '{"request":"masterBreakDate'.$day.'/'.$month.'/'.$year.'","event":"'.$eventId.'"}']);
            }
            $keyboardWeeks[] = $keyboardWeek;
        }
        $nextMonthNumber = date('m', mktime(0,0,0,$month+1, 1,$year));
        $prevMonthNumber = date('m', mktime(0,0,0,$month-1, 1,$year));
        $nextYearNumber = date('Y', mktime(0,0,0,$month+1, 1,$year));
        $prevYearNumber = date('Y', mktime(0,0,0,$month-1, 1,$year));

        //$array = [2,3,4];
        //return ...$array //2,3,4

        $keyboardControls[] = Keyboard::inlineButton(['text' => '<<', 'callback_data' => '{"request":"masterBreakDate'.$eventId.'","date":"'.$prevMonthNumber.'/'.$prevYearNumber.'"}']);
        $keyboardControls[] = Keyboard::inlineButton(['text' => '>>', 'callback_data' => '{"request":"masterBreakDate'.$eventId.'","date":"'.$nextMonthNumber.'/'.$nextYearNumber.'"}']);
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
                $keyboardWeek[] = Keyboard::inlineButton(['text' => $text, 'callback_data' => '{"request":"clientOrder","date":"'.$year.'/'.$month.'/'.$day.'","order":"'.$orderId.'"}']);
            }
            $keyboardWeeks[] = $keyboardWeek;
        }
        $nextMonthNumber = date('m', mktime(0,0,0,$month+1, 1,$year));
        $prevMonthNumber = date('m', mktime(0,0,0,$month-1, 1,$year));
        $nextYearNumber = date('Y', mktime(0,0,0,$month+1, 1,$year));
        $prevYearNumber = date('Y', mktime(0,0,0,$month-1, 1,$year));

        $keyboardControls[] = Keyboard::inlineButton(['text' => '<<', 'callback_data' => '{"request":"clientOrder'.$orderId.'month","date":"'.$prevMonthNumber.'/'.$prevYearNumber.'"}']);
        $keyboardControls[] = Keyboard::inlineButton(['text' => '>>', 'callback_data' => '{"request":"clientOrder'.$orderId.'month","date":"'.$nextMonthNumber.'/'.$nextYearNumber.'"}']);
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

    public function keyboardMonthWithControlsForMasterEvents($master, $month = null, $year = null)
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
                if(strlen($day)==1){
                    $day = '0'.$day;
                }
                $text = $day != '' ? $day.'.'.$month : ' ';
                $keyboardWeek[] = Keyboard::inlineButton(['text' => $text, 'callback_data' => '{"request":"showMasterEvents'.$master->id.'","day":"'.$year.'-'.$month.'-'.$day.'"}']);
            }
            $keyboardWeeks[] = $keyboardWeek;
        }
        $nextMonthNumber = date('m', mktime(0,0,0,$month+1, 1,$year));
        $prevMonthNumber = date('m', mktime(0,0,0,$month-1, 1,$year));
        $nextYearNumber = date('Y', mktime(0,0,0,$month+1, 1,$year));
        $prevYearNumber = date('Y', mktime(0,0,0,$month-1, 1,$year));

        //$array = [2,3,4];
        //return ...$array //2,3,4

        $keyboardControls[] = Keyboard::inlineButton(['text' => '<<', 'callback_data' => '{"request":"showMasterEvents'.$master->id.'","date":"'.$prevMonthNumber.'/'.$prevYearNumber.'"}']);
        $keyboardControls[] = Keyboard::inlineButton(['text' => '>>', 'callback_data' => '{"request":"showMasterEvents'.$master->id.'","date":"'.$nextMonthNumber.'/'.$nextYearNumber.'"}']);
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

        $eventsForChosenDay = Event::where('user_id', $master->id)->where('start_date','LIKE', '%'.$day.'%')->orderBy('start_date','asc')->get();
        $i=0;
        $count = count($eventsForChosenDay);
        foreach ($eventsForChosenDay as $event){
            $i++;
            if($event->service){
                $duration = $event->service->duration;
            }else{
                $duration = $event->duration;
            }
            $gapEndTimestamp = strtotime($event->start_date);
            if($i == 1){
                $gapStartTimestamp = $dayStartTimestamp;
            }else{
                $prevEventStartTimestamp = strtotime($eventsForChosenDay[$i-2]->start_date);
                if($eventsForChosenDay[$i-1]->service){
                    $prevEventDuration = $eventsForChosenDay[$i-1]->service->duration;
                }else{
                    $prevEventDuration = $eventsForChosenDay[$i-1]->duration;
                }
                $prevEventEndTimestamp = $prevEventStartTimestamp + $prevEventDuration*60;
                $gapStartTimestamp =  $prevEventEndTimestamp;
            }


            $gap = ($gapEndTimestamp - $gapStartTimestamp)/60;
            if($gap >= $duration){
                $availableTimes[] = [$gapStartTimestamp,$gapEndTimestamp];
            }
            if($i == $count){
                if($event->service){
                    $gapStartTimestamp = strtotime($event->start_date) + $event->service->duration*60;
                }else{
                    $gapStartTimestamp = strtotime($event->start_date) + $event->duration*60;
                }
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
    public function keyboardAvailableTimeForBreak($event, $master){
        $event_id = $event->id;
        $break = $event;
        $day = date('Y-m-d',strtotime($event->start_date));
        $dayOfWeek = date('l',strtotime($event->start_date));
        $schedule = json_decode($master->schedule, true);
        [$dayStart,$dayEnd] = explode('-',$schedule[$dayOfWeek]);
        $dayStartTimestamp = strtotime($day.' '.$dayStart);
        $dayEndTimestamp = strtotime($day.' '.$dayEnd);
        $availableTimes = [];
        $eventsForChosenDay = Event::where('user_id', $master->id)->where('start_date','LIKE', '%'.$day.'%')->where('start_date','not like','%'.$day.' 00:00:00')->where('id','<>',$event_id)->orderBy('start_date','asc')->get();
        $i=0;
        $count = count($eventsForChosenDay);
        foreach ($eventsForChosenDay as $event){

            $duration = $event->duration;
            $gapEndTimestamp = strtotime($event->start_date);
            if($i == 0){
                $gapStartTimestamp = $dayStartTimestamp;

            }else{
                $prevEventStartTimestamp = strtotime($eventsForChosenDay[$i-1]->start_date);
                if($eventsForChosenDay[$i]->service) {
                    $prevEventDuration = $eventsForChosenDay[$i]->service->duration;
                }else{
                    $prevEventDuration = $eventsForChosenDay[$i]->duration;
                }
                $prevEventEndTimestamp = $prevEventStartTimestamp + $prevEventDuration*60;
                $gapStartTimestamp =  $prevEventEndTimestamp;
            }


            $gap = ($gapEndTimestamp - $gapStartTimestamp)/60;
            if($gap >= $duration){
                $availableTimes[] = [$gapStartTimestamp,$gapEndTimestamp];
            }
            $i++;
            if($i == $count){
                $gapStartTimestamp = strtotime($event->start_date) + $event->duration*60;
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
                if($dayTimes >= $dayStartTimestamp[0] && $dayTimes <= ($dayEndTimestamp - $break->duration*60)){
                    $availableDayTimes[] = $dayTimes;
                    $dayTimes += 15*60;
                    continue;
                }
            }
            foreach ($availableTimes as $availableTime){
                if($dayTimes >= $availableTime[0] && $dayTimes <= ($availableTime[1] - $break->duration*60)){
                    $availableDayTimes[] = $dayTimes;
                }
            }
            $dayTimes += 15*60;
        }while($dayTimes < $dayEndTimestamp);
        $availableButtons = [];
        foreach ($availableDayTimes as $availableTime){
            $text = date('H:i',$availableTime);
            $availableButtons[] = Keyboard::inlineButton(['text' => $text, 'callback_data' => '{ "request":"masterBreakTime'.$availableTime.'","event":'.$event_id.'}']);
        }
        return $availableButtons;
    }
    public function sendClientMenu($telegramUser, $title = 'Меню')
    {
        $keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(['text' => 'Поиск мастера', 'callback_data' => '{ "request":"clientSearch"}'])
            )->row(
                Keyboard::inlineButton(['text' => 'Мои записи', 'callback_data' => '{ "request":"clientEvents"}']),
                Keyboard::inlineButton(['text' => 'Мои специалисты', 'callback_data' => '{ "request":"clientMasters"}'])
            )->row(
                Keyboard::inlineButton(['text' => 'Я мастер', 'callback_data' => '{ "request":"masterNew"}'])
            );
        Telegram::bot()->sendMessage([
            'chat_id' => $telegramUser->id,
            'text' => $title,
            'reply_markup' => $keyboard,
        ]);
    }
    public function sendClientEvents($telegramUser, $sendOrders= false){
        $events = Event::where('client_telegram_id', $telegramUser->id)->where('start_date','>',$this->nowDateTime)->orderBy('start_date')->get();
        if($events->count()) {
            foreach ($events as $event) {
            $keyboard = Keyboard::make()
                ->inline()
                ->row(
                    Keyboard::inlineButton(['text' => 'Отменить запись', 'callback_data' => '{ "request":"clientEventscancel'.$event->id.'"}'])
                );
                if($event->service){
                    $serviceName = $event->service->service_name;
                    $duration = $event->service->duration;
                }else{
                    $serviceName = 'Название услуги недоступно';
                    $duration = $event->duration;
                }
                Telegram::bot()->sendMessage([
                    'chat_id' => $telegramUser->id,
                    'text' => 'Запись на '.$serviceName.PHP_EOL.
                                'Мастер - '.$event->user->firstname.' '.$event->user->lastname.PHP_EOL.
                                'Телефон - '.$event->user->phone.PHP_EOL.
                                'Дата - '.$event->start_date.PHP_EOL.
                                'Услуга займет минут - '.$duration.PHP_EOL.
                                'Адрес - '.$event->user->address,
                    'reply_markup' => $keyboard,
                ]);
            }
        }else{
            Telegram::bot()->sendMessage([
                'chat_id' => $telegramUser->id,
                'text' => 'Подтвержденных записей не найдено',
            ]);
        }
    }

    public function sendClientMasters($telegramUser)
    {
        $clientRows = Client::where('telegram_id', $telegramUser->id)->get();
        $masterIds = [];
        foreach ($clientRows as $clientRow) {
            $masterIds[] = $clientRow->user_id;
        }
        $masterIds = array_unique($masterIds);
        $masters = User::whereIn('id', $masterIds)->get();
        if(count($masterIds)) {
            foreach ($masters as $master) {
                $keyboard = Keyboard::make()
                    ->inline()
                    ->row(
                        Keyboard::inlineButton(['text' => 'Записаться', 'callback_data' => '{ "request":"clientMastersBook'.$master->id.'"}'])
                    );
                Telegram::bot()->sendMessage([
                    'chat_id' => $telegramUser->id,
                    'text' => $master->firstname . ' ' . $master->lastname . PHP_EOL .
                        'Телефон: ' . $master->phone . PHP_EOL .
                        'Адрес: ' . $master->address,
                    'reply_markup' => $keyboard,
                ]);
            }
        }else{
            Telegram::bot()->sendMessage([
                'chat_id' => $telegramUser->id,
                'text' => 'Мастеров не найдено',
            ]);
        }
        $this->sendClientMenu($telegramUser);
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

    public function deleteMaster($telegramUser,$telegramId)
    {
        $user = User::where('id',$telegramUser->user_id)->get()->first();
        $user->clients()->delete();
        $user->events()->delete();
        $user->services()->delete();
        $telegramUser->user_id = null;
        $telegramUser->verified_master = false;
        $telegramUser->save();
        $user->delete();
        Telegram::bot()->sendMessage([
            'chat_id' => $telegramUser->id,
            'text' => 'Мастер удален',
        ]);
        $this->sendStartMessage($telegramId, true);
    }


    public function sendLastMessageAndSave($telegramUser, $lastMessage)
    {
        $telegramUser->last_message = $lastMessage;
        $telegramUser->save();
    }

    public function sendStartMessage($telegramUser, $afterDelete = false)
    {
        if(!$afterDelete) {
            $chatId = $telegramUser->getId();
            $text= 'Здравствуйте, ' . $telegramUser->getFirstName() . ' ' . $telegramUser->getLastName() . '.' . PHP_EOL;
        }else{
            $chatId = $telegramUser;
            $text = 'Здравствуйте!' . PHP_EOL;
        }
        $keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(['text' => 'Я мастер', 'callback_data' => '{ "request":"masterNew"}']),
                Keyboard::inlineButton(['text' => 'Я клиент', 'callback_data' => '{ "request":"clientNew"}'])
            );
        Telegram::bot()->sendMessage([
            'chat_id' => $chatId,
            'text' => $text.
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
        $clients = Client::where('user_id', $telegramUser->user_id)->orderBy('client_name')->get();
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
        $orders = Order::join('services', 'orders.service_id', '=', 'services.id')->select('services.*', 'orders.*')->where('services.user_id', $telegramUser->user_id)->where('orders.date','>',$this->nowDateTime)->orderBy('orders.date')->get();
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
                        'Дата - ' . $order->date . PHP_EOL.
                        'Комментарий - ' . $order->comment . PHP_EOL,
                    'reply_markup' => $keyboard,
                ]);
            }
        }else{
            Telegram::bot()->sendMessage([
                'chat_id' => $telegramUser->id,
                'parse_mode' => 'Markdown',
                'text' => 'Заказов не найдено',
            ]);
        }
        $keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(['text' => 'Добавить заказ', 'callback_data' => '{"request":"createMasterOrderStart"}'])
            );
        Telegram::bot()->sendMessage([
            'chat_id' => $telegramUser->id,
            'parse_mode' => 'Markdown',
            'text' => 'Вы можете добавить заказ',
            'reply_markup' => $keyboard
        ]);
    }

    public function sendMasterEvents($telegramUser,$events = null)
    {
        foreach ($events as $event) {

            if($event->service){
                $serviceName = $event->service->service_name;
                $duration = $event->service->duration;
                $amount = $event->service->amount;
            }else{
                $duration = $event->duration;
                if(!$event->amount){
                    $serviceName = 'Перерыв';
                    $amount = '0';
                }else{
                    $serviceName = 'Название услуги недоступно';
                    $amount = $event->amount;
                }
            }
            if($event->client){
                $clientName = $event->client->client_name;
                $phone = $event->client->phone;
            }else{
                $clientName = 'Имя клиента недоступно';
                $phone = 'Телефон клиента недоступен';
            }
            Log::debug($event->id);
            $keyboard = Keyboard::make()
                ->inline()
                ->row(
                    Keyboard::inlineButton(['text' => 'Удалить', 'callback_data' => '{"request":"removeEvent' . $event->id . '"}'])
                );
            Telegram::bot()->sendMessage([
                'chat_id' => $telegramUser->id,
                'parse_mode' => 'Markdown',
                'text' => $serviceName . PHP_EOL .
                    'Длительность - ' . $duration . PHP_EOL .
                    'Цена - ' . $amount . PHP_EOL .
                    'Имя клиента - ' . $clientName . PHP_EOL .
                    'Телефон - ' . $phone . PHP_EOL .
                    'Комментарий - ' . $event->comment . PHP_EOL .
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
                Keyboard::inlineButton(['text' => 'Назад', 'callback_data' => '{"request":"masterMenu"}']),
                Keyboard::inlineButton(['text' => 'Добавить перерыв', 'callback_data' => '{"request":"masterBreak"}'])
	    );
            //)->row(
            //    Keyboard::inlineButton(['text' => 'Удалить профиль', 'callback_data' => '{"request":"masterDelete"}'])
            //);
        Telegram::bot()->sendMessage([
            'chat_id' => $telegramUser->id,
            'text' => $text,
            'reply_markup' => $keyboard,
        ]);
        return null;
    }

    public function sendMasterSchedule($telegramUser){
        $schedule = json_decode(User::find($telegramUser->user_id)->schedule, true);
        $i = 0;
        $days = ['Понедельник','Вторник','Среда','Четверг','Пятница','Суббота','Воскресенье',];
        $daysEN = ['Monday', 'Tuesday','Wednesday','Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach($daysEN as $day){
            $i++;
            $keyboard = Keyboard::make()
                ->inline()
                ->row(
                    Keyboard::inlineButton(['text' => 'Изменить', 'callback_data' => '{"request":"changeSchedule' . $i . 'start"}'])
                );
            $text = $days[$i-1];
            $time = $schedule["$day"];
            if($time == '00:00-00:00'){
                $time='Выходной';
                $keyboard->row(
                    Keyboard::inlineButton(['text' => 'Сделать рабочим днем', 'callback_data' => '{"request":"turnonSchedule' . $i . '"}'])
                );
            }else{
                $keyboard->row(
                    Keyboard::inlineButton(['text' => 'Сделать выходным', 'callback_data' => '{"request":"turnoffSchedule' . $i . '"}'])
                );
            }
            Telegram::bot()->sendMessage([
                'chat_id' => $telegramUser->id,
                'text' => $text.":".PHP_EOL.
                $time,
                'reply_markup' => $keyboard,
            ]);
        }
        $this->sendMasterProfile($telegramUser);
    }

    public function sendMasterAddress($telegramUser){
        $address = User::find($telegramUser->user_id)->address;
        $keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(['text' => 'Изменить', 'callback_data' => '{ "request":"changeAddress"}'])
            );
        Telegram::bot()->sendMessage([
            'chat_id' => $telegramUser->id,
            'text' => "Текущий адрес:".PHP_EOL.
                $address,
            'reply_markup' => $keyboard,
        ]);
        $this->sendMasterProfile($telegramUser);
    }

    public function sendMasterEmail($telegramUser){
        $email = User::find($telegramUser->user_id)->email;
        $keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(['text' => 'Изменить', 'callback_data' => '{ "request":"changeEmail"}'])
            );
        Telegram::bot()->sendMessage([
            'chat_id' => $telegramUser->id,
            'text' => "Текущий адрес электронной почты:".PHP_EOL.
                $email,
            'reply_markup' => $keyboard,
        ]);
        $this->sendMasterProfile($telegramUser);
    }
    public function sendMasterPhone($telegramUser){
        $phone = User::find($telegramUser->user_id)->phone;
        $keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(['text' => 'Изменить', 'callback_data' => '{ "request":"changePhone"}'])
            );
        Telegram::bot()->sendMessage([
            'chat_id' => $telegramUser->id,
            'text' => "Текущий телефон:".PHP_EOL.
                $phone,
            'reply_markup' => $keyboard,
        ]);
        $this->sendMasterProfile($telegramUser);
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
                        Keyboard::inlineButton(['text' => 'Запись', 'callback_data' => '{ "request":"clientOrder' . $service->id . 'date"}'])
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

    public function sendClientServiceAvailableTime($orderId){
        $order = Order::where('id',$orderId)->get()->first();
        $master = $order->service->user;
        $day = date('Y-m-d',strtotime($order->date));
        $dayOfWeek = date('l',strtotime($order->date));
        $schedule = json_decode($master->schedule, true);
        [$dayStart,$dayEnd] = explode('-',$schedule[$dayOfWeek]);

        $dayStartTimestamp = strtotime($day.' '.$dayStart);
        $dayEndTimestamp = strtotime($day.' '.$dayEnd);
        $availableTimes = [];

        $eventsForChosenDay = Event::where('user_id',$master->id)->where('start_date','LIKE', '%'.$day.'%')->orderBy('start_date','asc')->get();
        $i=0;
        $count = count($eventsForChosenDay);
        foreach ($eventsForChosenDay as $event){
            $i++;
            if($event->service) {
                $duration = $event->service->duration;
            }else{
                $duration = $event->duration;
            }
            $gapEndTimestamp = strtotime($event->start_date);
            if($i == 1){
                $gapStartTimestamp = $dayStartTimestamp;
            }else{
                $prevEventStartTimestamp = strtotime($eventsForChosenDay[$i-2]->start_date);
                if($eventsForChosenDay[$i-1]->service){
                    $prevEventDuration = $eventsForChosenDay[$i-1]->service->duration;
                }else{
                    $prevEventDuration = $eventsForChosenDay[$i-1]->duration;
                }
                $prevEventEndTimestamp = $prevEventStartTimestamp + $prevEventDuration*60;
                $gapStartTimestamp =  $prevEventEndTimestamp;
            }


            $gap = ($gapEndTimestamp - $gapStartTimestamp)/60;
            if($gap >= $duration){
                $availableTimes[] = [$gapStartTimestamp,$gapEndTimestamp];
            }
            if($i == $count){
                if($event->service) {
                    $gapStartTimestamp = strtotime($event->start_date) + $event->service->duration * 60;
                }else{
                    $gapStartTimestamp = strtotime($event->start_date) + $event->duration * 60;
                }
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
            $availableButtons[] = Keyboard::inlineButton(['text' => $text, 'callback_data' => '{ "request":"clientOrder'.$order->id.'","time":'.$availableTime.'}']);
        }
        return $availableButtons;
    }

    public function index()
    {
        $update = Telegram::bot()->getWebhookUpdate();
        $callback_query = $update->getCallbackQuery();
        $message = $update->getMessage();
        $this->nowDateTime = date('Y-m-d H:i:s');

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
                        $email = strtolower($message->getText());
                        $user = User::where('email', $email)->get()->first();
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
                            $service = Service::where('id',$serviceId)->get()->first();
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
                            $service = Service::where('id',$serviceId)->get()->first();
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
                            $service = Service::where('id',$serviceId)->get()->first();
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
                            $client = Client::where('id',$clientId)->get()->first();
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
                            $client = Client::where('id',$clientId)->get()->first();
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
                            $client->telegram_id = null;
                            $client->save();
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => 'Имя нового клиента - ' . $client->client_name . PHP_EOL .
                                    'Введите телефон нового клиента',
                            ]);
                            $this->sendLastMessageAndSave($telegramUser, 'addClient' . $client->id . 'phone');
                        }else if (stripos($telegramUser->last_message, 'phone') !== false) {
                            $clientId = intval(substr($telegramUser->last_message, 9));
                            $client = Client::where('id',$clientId)->get()->first();
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
                    if (stripos($telegramUser->last_message, 'masterAddressChange') !==false){
                        $address = $message->getText();
                        $user = User::where('telegram_user_id', $telegramUser->id)->get()->first();
                        $user->address = $address;
                        $user->save();
                        Telegram::bot()->sendMessage([
                            'chat_id' => $telegramUser->id,
                            'text' => 'Адрес изменен',
                        ]);
                        $this->sendMasterMenu($telegramUser);
                    }
                    if (stripos($telegramUser->last_message, 'masterEmailChange') !==false){
                        $email = $message->getText();
                        $user = User::where('telegram_user_id', $telegramUser->id)->get()->first();
                        $user->email = $email;
                        $user->save();
                        Telegram::bot()->sendMessage([
                            'chat_id' => $telegramUser->id,
                            'text' => 'Электронная почта изменена',
                        ]);
                        $this->sendMasterMenu($telegramUser);
                    }
                    if (stripos($telegramUser->last_message, 'masterPhoneChange') !==false){
                        $phone = $message->getText();
                        if($phone[0] == '8'){
                            $phone[0] = '7';
                        }elseif($phone[0] == '+'){
                            $phone = substr($phone,1);
                        }
                        $user = User::where('telegram_user_id', $telegramUser->id)->get()->first();
                        $user->phone = $phone;
                        $user->save();
                        Telegram::bot()->sendMessage([
                            'chat_id' => $telegramUser->id,
                            'text' => 'Телефон изменен',
                        ]);
                        $this->sendMasterMenu($telegramUser);
                    }
                    if(strpos($telegramUser->last_message, 'masterBreakDuration') !== false){
                        $duration = intval($message->getText());
                        $event = new Event();
                        $event->service_id = null;
                        $event->client_id = null;
                        $event->duration = $duration;
                        $event->client_telegram_id = null;
                        $event->start_date = null;
                        $event->comment = null;
                        $event->telegram_user_id = null;
                        $event->save();
                        Telegram::bot()->sendMessage(
                            [
                                'chat_id' => $telegramUser->id,
                                'reply_markup' => $this->keyboardMonthWithControlsForBreak($event->id),
                                'text'    => 'Выберите дату для перерыва:',
                            ]
                        );
                    }

                }
                if ($telegramUser->last_message == 'clientSearch') {
                    $phone = $message->getText();
                    $phoneFirstSymbol = substr($phone,0,1);
                    if($phoneFirstSymbol == '+'){
                        $phone = substr($phone, 1);
                    }elseif ($phoneFirstSymbol == '8'){
                        $phone = '7'.substr($phone, 1);
                    }elseif($phoneFirstSymbol != '7' ){
                        $phone = '7'.$phone;
                    }

                    $master = User::where('phone', $phone)->first();
                    if ($master == null) {
                        Telegram::bot()->sendMessage([
                            'chat_id' => $telegramUser->id,
                            'text' => 'Мастера с таким номером не найдено, попробуйте еще раз!',
                        ]);
                    } else {
                        Telegram::bot()->sendMessage([
                            'chat_id' => $telegramUser->id,
                            'text' => 'Найден мастер ' . $master->firstname . ' ' . $master->lastname . '!'.PHP_EOL.
                            'Адрес мастера:'.$master->address,
                        ]);
                        $this->sendClientServices($telegramUser, $master->id);
                    }
                }
                if (stripos($telegramUser->last_message, 'clientOrder') !== false) {
                    if (strpos($telegramUser->last_message, 'phone') !== false) {
                        $phone = $message->getText();
                        $orderId = intval(substr($telegramUser->last_message, 16));
                        $order = Order::where('id',$orderId)->get()->first();
                        $client = Client::where('user_id',$order->service->user_id)->where('phone',$phone)->get()->first();
                        if($client == null){
                            $client = new Client();
                            $client->phone = $phone;
                            $client->client_name = $telegramUser->first_name.' '.$telegramUser->last_name;
                            $client->user_id = $order->service->user_id;
                            $client->telegram_id = $telegramUser->id;
                            $client->save();
                        }
                        $order->name = $client->client_name;
                        $order->phone = $client->phone;
                        $order->client_id = $client->id;
                        $order->save();
                        Telegram::bot()->sendMessage([
                            'chat_id' => $telegramUser->id,
                            'text' => 'Введите комментарий к заявке',
                        ]);
                        $this->sendLastMessageAndSave($telegramUser, 'clientOrdercomment'.$orderId);
                    }elseif (strpos($telegramUser->last_message, 'comment') !== false) {
                        $comment = $message->getText();
                        $orderId = intval(substr($telegramUser->last_message, 18));
                        $order = Order::where('id',$orderId)->get()->first();
                        $order->comment = $comment;
                        $order->save();
                        $masterTelegramId = $order->service->user->telegram_user_id;
                        if($masterTelegramId != null) {
                            Telegram::bot()->sendMessage(
                                [
                                    'chat_id' => $masterTelegramId,
                                    'text'    => 'У Вас новый заказ на '.$order->service->service_name,
                                ]
                            );
                        }
                        Telegram::bot()->sendMessage([
                            'chat_id' => $telegramUser->id,
                            'text' => 'Заявка сохранена и отправлена мастеру',
                        ]);
                        $this->sendClientMenu($telegramUser);
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
                    }
                    if ($callback_data['request'] == 'masterAddress') {
                        $this->sendMasterAddress($telegramUser);
                    }
                    if ($callback_data['request'] == 'masterEmail') {
                        $this->sendMasterEmail($telegramUser);
                    }
                    if ($callback_data['request'] == 'masterPhone') {
                        $this->sendMasterPhone($telegramUser);
                    }
                    if ($callback_data['request'] == 'masterMenu') {
                        $this->sendMasterMenu($telegramUser);
                    }
                    if ($callback_data['request'] == 'masterDelete') {
                        $this->deleteMaster($telegramUser, $telegramUser->id);
                    }

                    if ($callback_data['request'] == 'events') {
                        $master = User::where('id',$telegramUser->user_id)->get()->first();
                        Telegram::bot()->sendMessage(
                            [
                                'chat_id'      => $telegramUser->id,
                                'text'         => 'Выберите дату для просмотра записей',
                                'reply_markup' => $this->keyboardMonthWithControlsForMasterEvents($master)
                            ]
                        );

//                        $this->sendMasterEvents($telegramUser);
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
                        $service = Service::where('id',$serviceId)->get()->first();
                        Telegram::bot()->sendMessage([
                            'chat_id' => $telegramUser->id,
                            'text' => 'Текущее название услуги - ' . $service->service_name . PHP_EOL .
                                'Введите новое название услуги',
                        ]);
                        $this->sendLastMessageAndSave($telegramUser, $callback_data['request'] . 'name');
                    }
                    if (stripos($callback_data['request'], 'removeService') !== false) {
                        $serviceId = intval(substr($callback_data['request'], 13));
                        $service = Service::where('id',$serviceId)->get()->first();
                        $service->delete();
                        $this->sendMasterServices($telegramUser);
                        $this->sendMasterMenu($telegramUser);
                        $this->sendLastMessageAndSave($telegramUser, 'masterServices');
                    }
                    if (stripos($callback_data['request'], 'changeClient') !== false) {
                        $clientId = intval(substr($callback_data['request'], 12));
                        $client = Client::where('id',$clientId)->get()->first();
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
                        $client = Client::where('id',$clientId)->get()->first();
                        $client->events()->delete();
                        $client->delete();
                        $this->sendMasterClients($telegramUser, $user->id);
                        $this->sendMasterMenu($telegramUser);
                        $this->sendLastMessageAndSave($telegramUser, 'masterServices');

                    }
                    if (stripos($callback_data['request'], 'confirmOrder') !== false) {

                        $orderId = intval(substr($callback_data['request'], 12));
                        $order = Order::where('id',$orderId)->get()->first();
                        $orderDate = substr($order->date, 0,10);
                        $eventsForOrderDate = Event::where('user_id',$order->service->user->id)->where('start_date','LIKE', '%'.$orderDate.'%')->orderBy('start_date','asc')->get();
                        $orderDateBegin = strtotime($order->date);
                        $orderDateEnd = $orderDateBegin + $order->service->duration*60;
                        $intersection = false;
                        foreach($eventsForOrderDate as $event){

                            $beginEvent = strtotime($event->start_date);
                            $endEvent = $beginEvent + $event->duration*60;
                            if(($beginEvent <= $orderDateBegin && $endEvent > $orderDateBegin) || ($beginEvent < $orderDateEnd && $endEvent >= $orderDateEnd) || ($beginEvent >= $orderDateBegin && $endEvent <= $orderDateEnd) || ($beginEvent <= $orderDateBegin && $endEvent >= $orderDateEnd)){
                                $clientTelegramId = $order->client_telegram_id;
                                if($clientTelegramId != null  && $clientTelegramId != $telegramUser->id) {
                                    Telegram::bot()->sendMessage([
                                        'chat_id' => $clientTelegramId,
                                        'parse_mode' => 'Markdown',
                                        'text' => 'Ваш заказ на ' . $order->service->service_name . ' был отменен мастером.' . PHP_EOL,
                                    ]);
                                }
                                $order->delete();
                                $intersection = true;
                                break;
                            }
                        }
                        if(!$intersection) {
                            $event = new Event();
                            $event->service_id = $order->service_id;
                            $event->start_date = $order->date;
                            $event->client_telegram_id = $order->client_telegram_id;
                            $event->amount = $order->service->amount;
                            $event->duration = $order->service->duration;
                            $old_client = Client::where('client_name', $order->name)->where('phone', $order->phone)->where('user_id', $telegramUser->user_id)->get()->first();

                            if ($old_client === null) {  //if this master didn't have client with this data, it'll add it
                                $client = new Client();
                                $client->client_name = $order->name;
                                $client->phone = $order->phone;
                                $client->user_id = $user->id;
                                $client->telegram_id = null;
                                $client->save();
                            } else {
                                $client = $old_client;
                            }
                            $event->comment = $order->comment;
                            $event->client_id = $client->id;
                            $event->user_id = $user->id;
                            $event->save();
                            $order->delete();

                            $clientTelegramId = $order->client->telegram_id;
                            if ($clientTelegramId != $telegramUser->id && $clientTelegramId != null) {
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
                        }else{
                            $this->sendMasterOrders($telegramUser);
                        }
                    }
                    if (stripos($callback_data['request'], 'declineOrder') !== false) {
                        $orderId = intval(substr($callback_data['request'], 12));
                        $order = Order::where('id',$orderId)->first();
                        $clientTelegramId = $order->client_telegram_id;
                        if($clientTelegramId != null  && $clientTelegramId != $telegramUser->id) {
                            Telegram::bot()->sendMessage([
                                'chat_id' => $clientTelegramId,
                                'parse_mode' => 'Markdown',
                                'text' => 'Ваш заказ на ' . $order->service->service_name . ' был отменен мастером.' . PHP_EOL,
                            ]);
                        }
                        $order->delete();
                        Telegram::bot()->sendMessage([
                            'chat_id' => $telegramUser->id,
                            'text' => 'Заказ был отменен',
                        ]);
                        $this->sendMasterOrders($telegramUser);
                        $this->sendMasterMenu($telegramUser);
                        $this->sendLastMessageAndSave($telegramUser, 'masterOrders');
                    }
                    if (stripos($callback_data['request'], 'removeEvent') !== false) {
                        $eventId = intval(substr($callback_data['request'], 11));
                        $event = Event::where('id',$eventId)->get()->first();
                        $masterId = $event->user_id;
                        $day = substr($event->start_date,0,10);
                        $event->delete();
                        $events = Event::where('user_id',$masterId)->where('start_date','LIKE', '%'.$day.'%')->where('start_date','not like','%'.$day.' 00:00:00')->orderBy('start_date','asc')->get();
                        Log::debug($events);
                        $this->sendMasterEvents($telegramUser,$events);
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
                    if(stripos($callback_data['request'], 'masterBreakDate') !== false) {
                        if(array_key_exists('date',$callback_data)){
                            $date = explode('/', $callback_data['date']);
                            $eventId = intval(substr($callback_data['request'], 15));
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'reply_markup' => $this->keyboardMonthWithControlsForBreak($eventId,...$date),
                                'text' => 'Выберите дату для перерыва:',
                            ]);
                        }else {
                            $date = explode('/', substr($callback_data['request'], 15));
                            $event = Event::where('id', $callback_data['event'])->get()->first();
                            $event->user_id = $telegramUser->user_id;
                            $event->start_date = date('Y-m-d H:i:s', mktime(0, 0, 0, $date[1], $date[0], $date[2]));
                            $event->save();
                            $keyboard = Keyboard::make()->inline();
                            $keyboardData = $this->keyboardAvailableTimeForBreak($event, $telegramUser->user);
                            if ($keyboardData == []) {
                                Telegram::bot()->sendMessage([
                                    'chat_id' => $telegramUser->id,
                                    'text' => 'К сожалению, в этот день все занято'
                                ]);
                            } else {
                                $buttonsForKeyboard = array_chunk($this->keyboardAvailableTimeForBreak($event, $telegramUser->user), 4);
                                foreach ($buttonsForKeyboard as $buttons) {
                                    $keyboard->row(...$buttons);
                                }
                                Telegram::bot()->sendMessage([
                                    'chat_id' => $telegramUser->id,
                                    'reply_markup' => $keyboard,
                                    'text' => 'Выберите время для перерыва:' . PHP_EOL .
                                        $date[0] . '/' . $date[1] . '/' . $date[2],
                                ]);
                            }
                        }
                    }
                    if (stripos($callback_data['request'], 'masterBreakTime') !== false) {
                        $date = substr($callback_data['request'], 15);
                        Log::debug(date('Y-m-d H:i:s',$date));
                        $event = Event::where('id',$callback_data['event'])->get()->first();
                        $event->user_id = $telegramUser->user->id;
                        $event->start_date = date('Y-m-d H:i:s',$date);
                        $event->save();
                        Telegram::bot()->sendMessage([
                            'chat_id' => $telegramUser->id,
                            'text' => 'Перерыв сохранен',
                        ]);
                        $this->sendMasterMenu($telegramUser);
                    }
                    if(stripos($callback_data['request'], 'changeSchedule') !== false) {
                        $day = intval(substr($callback_data['request'], 14,1));
                        $schedule = User::where('id', $telegramUser->user_id)->select('schedule')->get()->first();
                        $dayNamesRu = ['Поенедельник','Вторник','Среда','Четверг','Пятница','Суббота','Воскресенье'];
                        $dayNames = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

                        $schedule['schedule'] = json_decode($schedule['schedule'],true);
                        $daySchedule = explode('-',$schedule['schedule'][''.$dayNames[$day-1]]);
                        if(stripos($callback_data['request'], 'start') !== false){
                            $keyboard = Keyboard::make()
                                ->inline()
                                ->row(
                                    Keyboard::inlineButton(['text' => '00', 'callback_data' => '{"request":"changeSchedule'.$day.'00beginHours"}']),
                                    Keyboard::inlineButton(['text' => '01', 'callback_data' => '{"request":"changeSchedule'.$day.'01beginHours"}']),
                                    Keyboard::inlineButton(['text' => '02', 'callback_data' => '{"request":"changeSchedule'.$day.'02beginHours"}']),
                                    Keyboard::inlineButton(['text' => '03', 'callback_data' => '{"request":"changeSchedule'.$day.'03beginHours"}'])
                                )->row(
                                    Keyboard::inlineButton(['text' => '04', 'callback_data' => '{"request":"changeSchedule'.$day.'04beginHours"}']),
                                    Keyboard::inlineButton(['text' => '05', 'callback_data' => '{"request":"changeSchedule'.$day.'05beginHours"}']),
                                    Keyboard::inlineButton(['text' => '06', 'callback_data' => '{"request":"changeSchedule'.$day.'06beginHours"}']),
                                    Keyboard::inlineButton(['text' => '07', 'callback_data' => '{"request":"changeSchedule'.$day.'07beginHours"}'])
                                )->row(
                                    Keyboard::inlineButton(['text' => '08', 'callback_data' => '{"request":"changeSchedule'.$day.'08beginHours"}']),
                                    Keyboard::inlineButton(['text' => '09', 'callback_data' => '{"request":"changeSchedule'.$day.'09beginHours"}']),
                                    Keyboard::inlineButton(['text' => '10', 'callback_data' => '{"request":"changeSchedule'.$day.'10beginHours"}']),
                                    Keyboard::inlineButton(['text' => '11', 'callback_data' => '{"request":"changeSchedule'.$day.'11beginHours"}'])
                                )->row(
                                    Keyboard::inlineButton(['text' => '12', 'callback_data' => '{"request":"changeSchedule'.$day.'12beginHours"}']),
                                    Keyboard::inlineButton(['text' => '13', 'callback_data' => '{"request":"changeSchedule'.$day.'13beginHours"}']),
                                    Keyboard::inlineButton(['text' => '14', 'callback_data' => '{"request":"changeSchedule'.$day.'14beginHours"}']),
                                    Keyboard::inlineButton(['text' => '15', 'callback_data' => '{"request":"changeSchedule'.$day.'15beginHours"}'])
                                )->row(
                                    Keyboard::inlineButton(['text' => '16', 'callback_data' => '{"request":"changeSchedule'.$day.'16beginHours"}']),
                                    Keyboard::inlineButton(['text' => '17', 'callback_data' => '{"request":"changeSchedule'.$day.'17beginHours"}']),
                                    Keyboard::inlineButton(['text' => '18', 'callback_data' => '{"request":"changeSchedule'.$day.'18beginHours"}']),
                                    Keyboard::inlineButton(['text' => '19', 'callback_data' => '{"request":"changeSchedule'.$day.'19beginHours"}'])
                                )->row(
                                    Keyboard::inlineButton(['text' => '20', 'callback_data' => '{"request":"changeSchedule'.$day.'20beginHours"}']),
                                    Keyboard::inlineButton(['text' => '21', 'callback_data' => '{"request":"changeSchedule'.$day.'21beginHours"}']),
                                    Keyboard::inlineButton(['text' => '22', 'callback_data' => '{"request":"changeSchedule'.$day.'22beginHours"}']),
                                    Keyboard::inlineButton(['text' => '23', 'callback_data' => '{"request":"changeSchedule'.$day.'23beginHours"}'])
                                );
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => "Текущее время начала работы - $daySchedule[0]".PHP_EOL.
                                    'Введите новое время начала:',
                                'reply_markup' => $keyboard,
                            ]);
                        }elseif(stripos($callback_data['request'], 'beginHours') !== false){
                            $chosenTime = substr($callback_data['request'], 15,2);
                            $keyboard = Keyboard::make()
                                ->inline()
                                ->row(
                                    Keyboard::inlineButton(['text' => ''.substr($chosenTime,0,2).':00', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.':00beginMinutes"}']),
                                    Keyboard::inlineButton(['text' => ''.substr($chosenTime,0,2).':15', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.':15beginMinutes"}']),
                                    Keyboard::inlineButton(['text' => ''.substr($chosenTime,0,2).':30', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.':30beginMinutes"}']),
                                    Keyboard::inlineButton(['text' => ''.substr($chosenTime,0,2).':45', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.':45beginMinutes"}'])
                                );
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => "Выбранное время - $chosenTime".PHP_EOL.
                                    'Введите новое время начала:',
                                'reply_markup' => $keyboard,
                            ]);
                        }elseif(stripos($callback_data['request'], 'beginMinutes') !== false){
                            $chosenTime = substr($callback_data['request'],15,5);
                            $keyboard = Keyboard::make()
                                ->inline()
                                ->row(
                                    Keyboard::inlineButton(['text' => '00', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-00endHours"}']),
                                    Keyboard::inlineButton(['text' => '01', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-01endHours"}']),
                                    Keyboard::inlineButton(['text' => '02', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-02endHours"}']),
                                    Keyboard::inlineButton(['text' => '03', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-03endHours"}'])
                                )->row(
                                    Keyboard::inlineButton(['text' => '04', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-04endHours"}']),
                                    Keyboard::inlineButton(['text' => '05', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-05endHours"}']),
                                    Keyboard::inlineButton(['text' => '06', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-06endHours"}']),
                                    Keyboard::inlineButton(['text' => '07', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-07endHours"}'])
                                )->row(
                                    Keyboard::inlineButton(['text' => '08', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-08endHours"}']),
                                    Keyboard::inlineButton(['text' => '09', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-09endHours"}']),
                                    Keyboard::inlineButton(['text' => '10', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-10endHours"}']),
                                    Keyboard::inlineButton(['text' => '11', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-11endHours"}'])
                                )->row(
                                    Keyboard::inlineButton(['text' => '12', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-12endHours"}']),
                                    Keyboard::inlineButton(['text' => '13', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-13endHours"}']),
                                    Keyboard::inlineButton(['text' => '14', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-14endHours"}']),
                                    Keyboard::inlineButton(['text' => '15', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-15endHours"}'])
                                )->row(
                                    Keyboard::inlineButton(['text' => '16', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-16endHours"}']),
                                    Keyboard::inlineButton(['text' => '17', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-17endHours"}']),
                                    Keyboard::inlineButton(['text' => '18', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-18endHours"}']),
                                    Keyboard::inlineButton(['text' => '19', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-19endHours"}'])
                                )->row(
                                    Keyboard::inlineButton(['text' => '20', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-20endHours"}']),
                                    Keyboard::inlineButton(['text' => '21', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-21endHours"}']),
                                    Keyboard::inlineButton(['text' => '22', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-22endHours"}']),
                                    Keyboard::inlineButton(['text' => '23', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.'-23endHours"}'])
                                );
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => "Выбранное время - $chosenTime".PHP_EOL.
                                    'Введите новое время окончания работ:',
                                'reply_markup' => $keyboard,
                            ]);
                        }else if(stripos($callback_data['request'], 'endHours') !== false){
                            $chosenTime = substr($callback_data['request'],15,8);
                            $keyboard = Keyboard::make()
                                ->inline()
                                ->row(
                                    Keyboard::inlineButton(['text' => ''.substr($chosenTime,6,2).':00', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.':00endMinutes"}']),
                                    Keyboard::inlineButton(['text' => ''.substr($chosenTime,6,2).':15', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.':15endMinutes"}']),
                                    Keyboard::inlineButton(['text' => ''.substr($chosenTime,6,2).':30', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.':30endMinutes"}']),
                                    Keyboard::inlineButton(['text' => ''.substr($chosenTime,6,2).':45', 'callback_data' => '{"request":"changeSchedule'.$day.$chosenTime.':45endMinutes"}'])
                                );
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => "Выбранное время - $chosenTime".PHP_EOL.
                                    'Введите новое время окончания работ:',
                                'reply_markup' => $keyboard,
                            ]);
                        }else if(stripos($callback_data['request'], 'endMinutes') !== false){
                            $day = substr($callback_data['request'],14,1);
                            $chosenTime = substr($callback_data['request'],15,11);
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => "Выбранное время для дня ".$dayNamesRu[$day-1]." - $chosenTime",
                            ]);
                            $newSchedule = json_decode($schedule, true)['schedule'];
                            $newSchedule[$dayNames[$day-1]] = $chosenTime;
                            $schedule = "{";
                            foreach($dayNames as $dayName){
                                $schedule .= '"'.$dayName.'":"'.$newSchedule[$dayName].'"';
                                if($dayName != 'Sunday'){
                                    $schedule .= ',';
                                }
                            }
                            $schedule .= "}";
                            $user = User::where('id',$telegramUser->user_id)->get()->first();
                            $user->schedule = $schedule;
                            $user->save();
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'text' => "Время изменено",
                            ]);
                            $this->sendMasterMenu($telegramUser);
                        }
                    }
                    if(stripos($callback_data['request'], 'turnoffSchedule') !== false) {
                        $day = intval(substr($callback_data['request'], 15,1));
                        $schedule = User::where('id', $telegramUser->user_id)->select('schedule')->get()->first();
                        $dayNamesRu = ['Поенедельник','Вторник','Среда','Четверг','Пятница','Суббота','Воскресенье'];
                        $dayNames = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                        Log::debug($dayNames[$day-1]);
                        $newSchedule = json_decode(json_decode($schedule, true)['schedule'], true);
                        Log::debug($newSchedule);
                        $newSchedule[$dayNames[$day-1]] = '00:00-00:00';
                        $schedule = "{";
                        foreach($dayNames as $dayName){
                            $schedule .= '"'.$dayName.'":"'.$newSchedule[$dayName].'"';

                            if($dayName != 'Sunday'){
                                $schedule .= ',';
                            }
                        }
                        $schedule .= "}";
                        $user = User::where('id',$telegramUser->user_id)->get()->first();
                        $user->schedule = $schedule;
                        $user->save();
                        $this->sendMasterProfile($telegramUser);
                    }
                    if(stripos($callback_data['request'], 'turnonSchedule') !== false) {
                        $day = intval(substr($callback_data['request'], 14,1));
                        $schedule = User::where('id', $telegramUser->user_id)->select('schedule')->get()->first();
                        $dayNamesRu = ['Поенедельник','Вторник','Среда','Четверг','Пятница','Суббота','Воскресенье'];
                        $dayNames = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                        Log::debug($dayNames[$day-1]);
                        $newSchedule = json_decode(json_decode($schedule, true)['schedule'],true);
                        $newSchedule[$dayNames[$day-1]] = '10:00-19:00';
                        $schedule = "{";
                        foreach($dayNames as $dayName){
                            $schedule .= '"'.$dayName.'":"'.$newSchedule[$dayName].'"';

                            if($dayName != 'Sunday'){
                                $schedule .= ',';
                            }
                        }
                        $schedule .= "}";
                        $user = User::where('id',$telegramUser->user_id)->get()->first();
                        $user->schedule = $schedule;
                        $user->save();
                        $this->sendMasterProfile($telegramUser);
                    }

                    if(stripos($callback_data['request'], 'changeAddress') !== false){
                        Telegram::bot()->sendMessage([
                            'chat_id' => $telegramUser->id,
                            'text' => 'Введите новый адрес',
                        ]);
                        $this->sendLastMessageAndSave($telegramUser, 'masterAddressChange');
                    }
                    if(stripos($callback_data['request'], 'changeEmail') !== false){
                        Telegram::bot()->sendMessage([
                            'chat_id' => $telegramUser->id,
                            'text' => 'Введите новый email:',
                        ]);
                        $this->sendLastMessageAndSave($telegramUser, 'masterEmailChange');
                    }
                    if(stripos($callback_data['request'], 'changePhone') !== false){
                        Telegram::bot()->sendMessage([
                            'chat_id' => $telegramUser->id,
                            'text' => 'Введите новый телефон',
                        ]);
                        $this->sendLastMessageAndSave($telegramUser, 'masterPhoneChange');
                    }
                    if(stripos($callback_data['request'], 'showMasterEvents') !== false){
                        if(array_key_exists('date',$callback_data)){
                            $date = explode('/', $callback_data['date']);
                            $masterId = intval(substr($callback_data['request'], 16));
                            $master = User::where('id',$masterId)->get()->first();
                            Telegram::bot()->sendMessage([
                                'chat_id' => $telegramUser->id,
                                'reply_markup' => $this->keyboardMonthWithControlsForMasterEvents($master,...$date),
                                'text' => 'Выберите дату для показа записей:',
                            ]);
                        }else {
                            $masterId = intval(substr($callback_data['request'], 16));
                            Log::debug($callback_data['day']);
                            $events = Event::where('user_id',$masterId)->where('start_date','LIKE', '%'.$callback_data['day'].'%')->where('start_date','not like', '%'.$callback_data['day'].' 00:00:00')->orderBy('start_date','asc')->get();
                            $this->sendMasterEvents($telegramUser,$events);
                            $this->sendMasterMenu($telegramUser);
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
                    'text' => 'Введите номер телефона мастера:',
                ]);
                $this->sendLastMessageAndSave($telegramUser, 'clientSearch');
            }
            if (stripos($callback_data['request'], 'clientMastersBook') !== false) {
                $masterId = intval(substr($callback_data['request'],17));
                $this->sendClientServices($telegramUser,$masterId);
            }

            if ($callback_data['request'] == 'clientEvents') {
                $this->sendClientEvents($telegramUser, true);
                $this->sendClientMenu($telegramUser);
                $this->sendLastMessageAndSave($telegramUser, 'clientEvents');
            }
            if ($callback_data['request'] == 'clientMasters') {
                $this->sendClientMasters($telegramUser);
            }
            if (stripos($callback_data['request'], 'clientEventscancel') !== false) {
                $eventId = intval(substr($callback_data['request'], 18));
                $event = Event::where('id',$eventId)->get()->first();

                Telegram::bot()->sendMessage([
                    'chat_id' => $event->user->telegram_user_id,
                    'text' => 'Запись на '.$event->service->service_name.' на дату '.$event->start_date.' была отменена клиентом.',
                ]);
                $event->delete();
                Telegram::bot()->sendMessage([
                    'chat_id' => $telegramUser->id,
                    'text' => 'Запись отменена',
                ]);
                $this->sendLastMessageAndSave($telegramUser, 'clientEvents');
                $this->sendClientMenu($telegramUser);
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
            if($callback_data['request'] == 'masterBreak'){
                Telegram::bot()->sendMessage([
                    'chat_id' => $telegramUser->id,
                    'text' => 'Введите длительность перерыва в минутах',
                ]);
                $this->sendLastMessageAndSave($telegramUser,'masterBreakDuration');
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
                    $order = Order::where('id',$orderId)->get()->first();
                    $order->client_id = $client->id;
                    $order->name = $client->client_name;
                    $order->client_telegram_id = $client->telegram_id;
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
                    $order = Order::where('id',$orderId)->get()->first();
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
                    Telegram::bot()->sendMessage([
                        'chat_id' => $telegramUser->id,
                        'text' => 'К сожалению, в этот день все занято'
                    ]);
                }else {
                    $buttonsForKeyboard = array_chunk($this->keyboardAvailableTimeForOrder($order), 4);
                    foreach ($buttonsForKeyboard as $buttons) {
                        $keyboard->row(...$buttons);
                    }
                    Telegram::bot()->sendMessage([
                        'chat_id' => $telegramUser->id,
                        'reply_markup' => $keyboard,
                        'text' => 'Выберите время для записи:' . PHP_EOL .
                            $date[0] . '/' . $date[1] . '/' . $date[2],
                    ]);
                }
            }
            if (stripos($callback_data['request'], 'createMasterOrderTime') !== false) {
                $date = substr($callback_data['request'], 21);
                $order = Order::where('id',$callback_data['order'])->first();
                $order->date = date('Y-m-d H:i:s',$date);
                $order->save();
                Telegram::bot()->sendMessage([
                    'chat_id' => $telegramUser->id,
                    'text' => 'Заказ сохранен. Для его подтверждения зайдите в список заказов',
                ]);
                if($order->client->telegram_id != null) {
                    Telegram::bot()->sendMessage([
                        'chat_id' => $order->client->telegram_id,
                        'text' => 'Вас записали на ' . $order->service->service_name . ' к мастеру ' . $order->service->user->first_name . ' ' . $order->service->user->last_name . PHP_EOL .
                        'Время - ' . $order->date . PHP_EOL .
                        'Длительность - ' . $order->service->duration . ' минут' . PHP_EOL .
                        'Стоимость - ' . $order->service->amount,
                    ]);
                }
                $this->sendMasterMenu($telegramUser);
            }
            if (stripos($callback_data['request'], 'clientOrder') !== false) {
                if(stripos($callback_data['request'], 'date')) {
                    $serviceId = intval(substr($callback_data['request'], 11));
                    $order = new Order();
                    $order->service_id = $serviceId;
                    $order->name = $telegramUser->first_name.' '.$telegramUser->last_name;
                    $order->client_telegram_id = $telegramUser->id;
                    $order->save();
                    $service = Service::where('id', $serviceId)->first();
                    Telegram::bot()->sendMessage(
                        [
                            'chat_id'      => $telegramUser->id,
                            'text'         => 'Выберите желаемую дату для записи на "'.$service->service_name.'"',
                            'reply_markup' => $this->keyboardMonthWithControlsForClient($order->id)
                        ]
                    );
                }elseif(stripos($callback_data['request'], 'month')){
                    $orderId = intval(substr($callback_data['request'], 11));
                    $order = Order::where('id',$orderId)->get()->first();
                    $date = explode('/', $callback_data['date']);
                    Telegram::bot()->sendMessage([
                        'chat_id' => $telegramUser->id,
                        'reply_markup' => $this->keyboardMonthWithControlsForClient($order->id,...$date),
                        'text' => 'Выберите желаемую дату для записи на "'.$order->service->service_name.'"'.PHP_EOL.
                            $date[0].'/'.$date[1],
                    ]);
                }elseif(array_key_exists('order',$callback_data)){
                    $orderId = intval($callback_data['order']);
                    $order = Order::where('id',$orderId)->get()->first();
                    $date = explode('/',$callback_data['date']);
                    $order->date = date('Y-m-d H:i:s',strtotime($callback_data['date']));
                    $order->save();
                    $service = Service::where('id',$order->service_id)->get()->first();

                    $keyboard =Keyboard::make()->inline();

                    $keyboardData = $this->sendClientServiceAvailableTime($orderId);
                    if($keyboardData == []){
                        $keyboard->row('К сожалению, в этот день все занято');
                    }
                    $buttonsForKeyboard = array_chunk($keyboardData, 4);
                    foreach ($buttonsForKeyboard as $buttons){
                        $keyboard->row(...$buttons);
                    }
                    Telegram::bot()->sendMessage([
                        'chat_id' => $telegramUser->id,
                        'text' => 'Выберите желаемое время для записи на "' . $service->service_name.'"'.PHP_EOL.
                            $date[0].'/'.$date[1],
                        'reply_markup' =>$keyboard
                    ]);
                }elseif(array_key_exists('time',$callback_data)){
                    $orderId = intval(substr($callback_data['request'], 11));
                    $order = Order::where('id',$orderId)->first();
                    $order->client_telegram_id = $telegramFrom->getId();
                    $date = date('Y-m-d H:i:s',$callback_data['time']);
                    $order->date =$date;
                    $order->save();
                    Telegram::bot()->sendMessage([
                        'chat_id' => $telegramUser->id,
                        'text' => 'Введите свой телефон для контакта с мастером:',
                    ]);
                    $this->sendLastMessageAndSave($telegramUser, 'clientOrderphone'.$orderId);
                }
            }
        }
    }
}
