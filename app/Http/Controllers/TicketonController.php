<?php

namespace App\Http\Controllers;

use App\Models\TicketonCache;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

    public function getСonversations(Request $request)
    {
        $selectedDate = $request->get('selected_date', Carbon::now()->format('d.m.Y'));
        $defaultDateNow = Carbon::now();
        $events = $this->getCacheByKey('events');
        $shows = $this->getCacheByKey('shows');
        $result = collect($shows)->map(function ($show) use (&$events) {
            $showDateTime = Carbon::parse($show['dt']);
            $currentEvent = collect($events)->where('id', $show['event'])->first();
            return [
                'time' => $showDateTime->format('H:i'), // Формат времени 'H:i'
                'date' => $showDateTime->format('d.m.Y'),
                'formatContent' => $show['format'],
                'hallName' => $show['hall'],
                'hallId' => $show['hall_id'],
                'ticketonId' => $show['id'],
                'price' => $show['prices'][0]['sum'],
                'sessionId' => $show['session_id'],
                'filmName' => $currentEvent['name'],
                'age' => $currentEvent['fcsk'],
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
            $showDateTime = Carbon::parse($show['dt']);
            return [
                'time' => $showDateTime->format('H:i'), // Формат времени 'H:i'
                'date' => $showDateTime->format('d.m.Y'),
                'formatContent' => $show['format'],
                'hallName' => $show['hall'],
                'hallId' => $show['hall_id'],
                'ticketonId' => $show['id'],
                'price' => $show['prices'][0]['sum'],
                'sessionId' => $show['session_id']
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
            'filmId' => $resultEvent['id'],
            'filmName' => $resultEvent['name'],
            'age' => $resultEvent['fcsk'],
            'picture' => count($resultEvent['images']) > 0 ? $resultEvent['images'][0]['url'] : $resultEvent['cover'],
            'director' => $resultEvent['director'],
            'country' => $resultEvent['country'],
            'actors' => $resultEvent['actors'],
            'remark' => $resultEvent['description'],
            'duration' => $resultEvent['duration'],
            'times' => $eventShows, // Преобразуем коллекцию обратно в массив
        ];
        return response()->json([
            'status' => 0,
            'message' => 'success',
            'response' => $result
        ]);
    }

    public function getFilms(Request $request)
    {
        $selectedDate = $request->get('selected_date', Carbon::now()->format('d.m.Y'));
        $defaultDateNow = Carbon::now();
        $events = $this->getCacheByKey('events');
//        var_dump($events);
        $shows = $this->getCacheByKey('shows');
        $new = collect($events)->values()->map(function ($event) use ($shows, $selectedDate, $defaultDateNow) {
            $eventShows = collect($shows)->where('event', $event['id'])->map(function ($show) use ($defaultDateNow) {
                $showDateTime = Carbon::parse($show['dt']);
                return [
                    'time' => $showDateTime->format('H:i'), // Формат времени 'H:i'
                    'date' => $showDateTime->format('d.m.Y'),
                    'formatContent' => $show['format'],
                    'hallName' => $show['hall'],
                    'hallId' => $show['hall_id'],
                    'ticketonId' => $show['id'],
                    'price' => $show['prices'][0]['sum'],
                    'sessionId' => $show['session_id']
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
                'filmId' => $event['id'],
                'filmName' => $event['name'],
                'age' => $event['fcsk'],
                'picture' => count($event['images']) > 0 ? $event['images'][0]['url'] : $event['cover'],
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
        $ticketonCache =  TicketonCache::whereDate('created_at', now()->toDateString())->where('cache_key', $key)->first();
        if(isset($ticketonCache)) return $ticketonCache?->data;
        $response = $this->getShowsFromTicketon();
        $shows = $response->shows;
        $events = $response->events;
        TicketonCache::create([
           'cache_key' => 'shows',
           'data' => $shows
        ]);
        TicketonCache::create([
            'cache_key' => 'events',
            'data' => $events
        ]);
        $ticketonCache =  TicketonCache::whereDate('created_at', now()->toDateString())->where('cache_key', $key)->first();
        return $ticketonCache?->data;
    }
}
