<?php

namespace App\Http\Controllers;

use App\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class TicketonController extends Controller
{
    public function getDates()
    {
        Carbon::setLocale('ru');
//        Carbon::setTimezone('UTC');
        $dateArray = [];
        $currentDate = Carbon::now();
        for ($i = 0; $i < 7; $i++) {
            $dateArray[] = [
                'date' => $currentDate->format('d.m.Y')
            ];
            $currentDate->addDay();
        }
        return response()->json([
            'status' => 0,
            'message' => 'success',
            'response' => $dateArray
        ]);
    }

    public function getVsDates()
    {
        Carbon::setLocale('ru');
        $dateArray = [];
        $currentDate = Carbon::now();

        for ($i = 0; $i < 7; $i++) {
            // Форматирование даты
            $formattedDate = $currentDate->format('d.m.Y');
            $title = '';

            // Определение заголовка
            if ($currentDate->isToday()) {
                $title = 'Сегодня';
            } elseif ($currentDate->isTomorrow()) {
                $title = 'Завтра';
            } else {
                // Форматируем дату
                $title = $currentDate->translatedFormat('l, j M');

                // Преобразуем месяц в трехбуквенную аббревиатуру и добавляем точку, если это не май
                $title = preg_replace_callback('/(\w{3})\b/', function ($matches) {
                    $month = $matches[1];
                    // Месяцы, для которых точка не ставится
                    $noDotMonths = ['май'];
                    // Добавляем точку, если месяц не май
                    return in_array($month, $noDotMonths) ? $month : $month . '.';
                }, $title);

                // Приводим первую букву заголовка к заглавной
                $title = strtolower($title);
            }

            $dateArray[] = [
                'date' => $formattedDate,
                'title' => $title
            ];

            // Переходим к следующему дню
            $currentDate->addDay();
        }
        return ResponseService::success($dateArray);
    }

    private function getShowsFromTicketon()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.ticketon.kz/shows?token=d14e041cc43b5db89a65fa5c7acdf90eefc0f8df&i18n=all&is_active=1&place=12158&with[]=future',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    /**
     * @throws ConnectionException
     */
    public function getPayments(Request $request)
    {
        $date_from = $request->post('date_from');
        $date_to = $request->post('date_to');
        $filters = [
            'date_from' => $date_from ? Carbon::createFromFormat('Y-m-d H:i:s', $date_from)->toDateTimeString() : Carbon::now()->subMonths(2)->toDateTimeString(),
            'date_to' => $date_to ? Carbon::createFromFormat('Y-m-d H:i:s', $date_to)->toDateTimeString() : Carbon::now()->toDateTimeString(),
            'limit' => 2000
        ];
        $queryParams = Arr::query($filters);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://ticketon.kz/api/create-report/reports/nur_detail_tickets?$queryParams",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: G2rT6m6YFaOq6qwR69GB3IrMLg5Wv9Ai'
            ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($httpcode == 200) { //check for true/false
            $result = json_decode($response)?->result;
            return $result;
        }
        return [
            'status' => $httpcode,
            'message' => 'error',
            'response' => $response
        ];
    }

    public function getPremiers()
    {
        $events = $this->getCacheByKey('events');
        $events = collect($events)->filter(function ($event) {
            return isset($event) && time() < $event->premiere_ts;
        })->map(function ($resultEvent) {
            return [
                'filmId' => $resultEvent->id,
                'filmName' => $resultEvent->name,
                'age' => $resultEvent->fcsk,
                'picture' => count($resultEvent->images) > 0 ? $resultEvent->images[0]->url : $resultEvent->cover,
                'director' => $resultEvent->director,
                'country' => $resultEvent->country,
                'actors' => $resultEvent->actors,
                'remark' => $resultEvent->description,
                'duration' => $resultEvent->duration,
                'premiere_date' => Carbon::createFromTimestamp($resultEvent->premiere_ts)->format('d-m-Y')
            ];
        })->values();
        return response()->json([
            'status' => 0,
            'message' => 'success',
            'response' => $events
        ]);
    }

    public function getVsPremiers()
    {
        $events = $this->getCacheByKey('events');
        $events = collect($events)->filter(function ($event) {
            return isset($event) && time() < $event->premiere_ts;
        })->map(function ($resultEvent) {
            return [
                'filmId' => $resultEvent->id,
                'filmName' => $resultEvent->name,
                'age' => $resultEvent->fcsk,
                'picture' => count($resultEvent->images) > 0 ? $resultEvent->images[0]->url : $resultEvent->cover,
                'premiere_date' => Carbon::createFromTimestamp($resultEvent->premiere_ts)->format('d-m-Y')
            ];
        })->values();
        return ResponseService::success($events);
    }

    public function getPremierByFilmId($filmId)
    {
        $events = $this->getCacheByKey('events');
        $events = collect($events)->filter(function ($event) {
            return isset($event) && time() < $event->premiere_ts;
        })->map(function ($resultEvent) {
            return [
                'filmId' => $resultEvent->id,
                'filmName' => $resultEvent->name,
                'age' => $resultEvent->fcsk,
                'picture' => count($resultEvent->images) > 0 ? $resultEvent->images[0]->url : $resultEvent->cover,
                'director' => $resultEvent->director,
                'country' => $resultEvent->country,
                'actors' => $resultEvent->actors,
                'remark' => $resultEvent->description,
                'duration' => $resultEvent->duration,
                'premiere_date' => Carbon::createFromTimestamp($resultEvent->premiere_ts)->format('d-m-Y')
            ];
        })->where('filmId', $filmId)->first();
        return response()->json([
            'status' => 0,
            'message' => 'success',
            'response' => $events
        ]);
    }

    public function getСonversations(Request $request)
    {
        $selectedDate = $request->get('selected_date', Carbon::now()->format('d.m.Y'));
        $defaultDateNow = Carbon::now();
        $events = $this->getCacheByKey('events');
        $shows = $this->getCacheByKey('shows');
        $result = collect($shows)->map(function ($show) use (&$events) {
            $showDateTime = Carbon::parse($show->dt);
            $currentEvent = collect($events)->where('id', $show->event)->first();
            return [
                'time' => $showDateTime->format('H:i'), // Формат времени 'H:i'
                'date' => $showDateTime->format('d.m.Y'),
                'formatContent' => $show->format,
                'hallName' => $show->hall,
                'hallId' => $show->hall_id,
                'ticketonId' => $show->id,
                'price' => $show->prices[0]->sum,
                'sessionId' => $show->session_id,
                'filmName' => $currentEvent->name,
                'age' => $currentEvent->fcsk,
            ];
        })->filter(function ($show) use ($selectedDate, $defaultDateNow) {
            $filmTime = Carbon::createFromFormat('d.m.Y H:i', $show['date'] . ' ' . $show['time']);
            if ($selectedDate === $defaultDateNow->format('d.m.Y')) {
                return ($show['date'] === $selectedDate) && ($defaultDateNow <= $filmTime);
            } else {
                return $show['date'] === $selectedDate;
            }
        })->values();

        return response()->json([
            'status' => 0,
            'message' => 'success',
            'response' => $result
        ]);

    }

    public function getFilmDetails(Request $request, $id)
    {
        $selectedDate = $request->get('selected_date', Carbon::now()->format('d.m.Y'));
        $defaultDateNow = Carbon::now();
        $events = $this->getCacheByKey('events');
        $shows = $this->getCacheByKey('shows');
        $resultEvent = collect($events)->where('id', $id)->first();
        $eventShows = collect($shows)->where('event', $id)->map(function ($show) {
            $showDateTime = Carbon::parse($show->dt);
            return [
                'time' => $showDateTime->format('H:i'), // Формат времени 'H:i'
                'date' => $showDateTime->format('d.m.Y'),
                'formatContent' => $show->format,
                'hallName' => $show->hall,
                'hallId' => $show->hall_id,
                'ticketonId' => $show->id,
                'price' => $show->prices[0]->sum,
                'sessionId' => $show->session_id
            ];
        })->filter(function ($show) use (&$selectedDate, $defaultDateNow) {
            $filmTime = Carbon::createFromFormat('d.m.Y H:i', $show['date'] . ' ' . $show['time']);
            if ($selectedDate === $defaultDateNow->format('d.m.Y')) {
                return ($show['date'] === $selectedDate) && ($defaultDateNow <= $filmTime);
            } else {
                return $show['date'] === $selectedDate;
            }
        })->values();
        $result = [
            'filmId' => $resultEvent->id,
            'filmName' => $resultEvent->name,
            'age' => $resultEvent->fcsk,
            'picture' => count($resultEvent->images) > 0 ? $resultEvent->images[0]->url : $resultEvent->cover,
            'director' => $resultEvent->director,
            'country' => $resultEvent->country,
            'actors' => $resultEvent->actors,
            'remark' => $resultEvent->description,
            'duration' => $resultEvent->duration,
            'times' => $eventShows, // Преобразуем коллекцию обратно в массив
        ];
        return response()->json([
            'status' => 0,
            'message' => 'success',
            'response' => $result
        ]);
    }

    public function getVsFilmDetails(Request $request, $id)
    {
        $selectedDate = $request->get('selected_date', Carbon::now()->format('d.m.Y'));
        $defaultDateNow = Carbon::now();
        $events = $this->getCacheByKey('events');
        $shows = $this->getCacheByKey('shows');
        $resultEvent = collect($events)->where('id', $id)->first();
        $eventShows = collect($shows)->where('event', $id)->map(function ($show) {
            $showDateTime = Carbon::parse($show->dt);
            return [
                'time' => $showDateTime->format('H:i'), // Формат времени 'H:i'
                'date' => $showDateTime->format('d.m.Y'),
                'formatContent' => $show->format,
                'hallName' => $show->hall,
                'hallId' => $show->hall_id,
                'ticketonId' => $show->id,
                'price' => $show->prices[0]->sum,
                'sessionId' => $show->session_id
            ];
        })->filter(function ($show) use (&$selectedDate, $defaultDateNow) {
            $filmTime = Carbon::createFromFormat('d.m.Y H:i', $show['date'] . ' ' . $show['time']);
            if ($selectedDate === $defaultDateNow->format('d.m.Y')) {
                return ($show['date'] === $selectedDate) && ($defaultDateNow <= $filmTime);
            } else {
                return $show['date'] === $selectedDate;
            }
        })->values();
        $result = [
            'filmId' => $resultEvent->id,
            'filmName' => $resultEvent->name,
            'age' => $resultEvent->fcsk,
            'picture' => count($resultEvent->images) > 0 ? $resultEvent->images[0]->url : $resultEvent->cover,
            'director' => $resultEvent->director,
            'country' => $resultEvent->country,
            'actors' => $resultEvent->actors,
            'remark' => $resultEvent->description,
            'duration' => $resultEvent->duration,
            'times' => $eventShows, // Преобразуем коллекцию обратно в массив
        ];
        return ResponseService::success($result);
    }


    public function getVsFilms(Request $request)
    {
        $selectedDate = $request->get('selected_date', Carbon::now()->format('d.m.Y'));
        $defaultDateNow = Carbon::now();
        $events = $this->getCacheByKey('events');
//        var_dump($events);
        $shows = $this->getCacheByKey('shows');
        $new = collect($events)->values()->map(function ($event) use ($shows, $selectedDate, $defaultDateNow) {
            $eventShows = collect($shows)->where('event', $event->id)->map(function ($show) use ($defaultDateNow) {
                $showDateTime = Carbon::parse($show->dt);
                return [
                    'time' => $showDateTime->format('H:i'), // Формат времени 'H:i'
                    'date' => $showDateTime->format('d.m.Y'),
                    'formatContent' => $show->format,
                    'hallName' => $show->hall,
                    'hallId' => $show->hall_id,
                    'ticketonId' => $show->id,
                    'price' => $show->prices[0]->sum,
                    'sessionId' => $show->session_id
                ];
            })->filter(function ($show) use ($selectedDate, $defaultDateNow) {
                $filmTime = Carbon::createFromFormat('d.m.Y H:i', $show['date'] . ' ' . $show['time']);
                if ($selectedDate === $defaultDateNow->format('d.m.Y')) {
                    return ($show['date'] === $selectedDate) && ($defaultDateNow <= $filmTime);
                } else {
                    return $show['date'] === $selectedDate;
                }
            })->values();

            return [
                'filmId' => $event->id,
                'filmName' => $event->name,
                'age' => $event->fcsk,
                'picture' => count($event->images) > 0 ? $event->images[0]->url : $event->cover,
                'times' => $eventShows->toArray(), // Преобразуем коллекцию обратно в массив
            ];
        })->values();

        return ResponseService::success($new);
    }

    public function getFilms(Request $request)
    {
        $selectedDate = $request->get('selected_date', Carbon::now()->format('d.m.Y'));
        $defaultDateNow = Carbon::now();
        $events = $this->getCacheByKey('events');
//        var_dump($events);
        $shows = $this->getCacheByKey('shows');
        $new = collect($events)->values()->map(function ($event) use ($shows, $selectedDate, $defaultDateNow) {
            $eventShows = collect($shows)->where('event', $event->id)->map(function ($show) use ($defaultDateNow) {
                $showDateTime = Carbon::parse($show->dt);
                return [
                    'time' => $showDateTime->format('H:i'), // Формат времени 'H:i'
                    'date' => $showDateTime->format('d.m.Y'),
                    'formatContent' => $show->format,
                    'hallName' => $show->hall,
                    'hallId' => $show->hall_id,
                    'ticketonId' => $show->id,
                    'price' => $show->prices[0]->sum,
                    'sessionId' => $show->session_id
                ];
            })->filter(function ($show) use ($selectedDate, $defaultDateNow) {
                $filmTime = Carbon::createFromFormat('d.m.Y H:i', $show['date'] . ' ' . $show['time']);
                if ($selectedDate === $defaultDateNow->format('d.m.Y')) {
                    return ($show['date'] === $selectedDate) && ($defaultDateNow <= $filmTime);
                } else {
                    return $show['date'] === $selectedDate;
                }
            })->values();

            return [
                'filmId' => $event->id,
                'filmName' => $event->name,
                'age' => $event->fcsk,
                'picture' => count($event->images) > 0 ? $event->images[0]->url : $event->cover,
                'times' => $eventShows->toArray(), // Преобразуем коллекцию обратно в массив
            ];
        })->values();

        return response()->json([
            'status' => 0,
            'message' => 'success',
            'response' => $new
        ]);
    }

    public function getCacheByKey($key)
    {
        $result = Cache::remember($key, 1440, function () use ($key) {
            $response = $this->getShowsFromTicketon();
            return $response;
        });
        return collect($result->$key)->toArray();
    }
}
