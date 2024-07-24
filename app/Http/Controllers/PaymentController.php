<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function view(Request $request)
    {
        $result = $this->getPayments($request);
        $result = $this->mapPayments($result);
        return Inertia::render('Payment', [
            'payments' => $result
        ]);
    }

    /**
     * @throws ConnectionException
     */
    public function getPayments(Request $request)
    {
        // ПОСТАВИТЬ TRUE на время тестов
        $isTest = false;
        $payments = new TicketonController();
        if ($isTest == true) {
            $result = $payments->getPayments($request);
            if ($result['status'] && $result['status'] !== 0) {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://admin.nurcinema.kg/api/get_payments',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => '{
    "date_from": "2024-05-03 10:00:00"
}',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);
                return json_decode($response);
            }
            return $result;
        } else {
            return $payments->getPayments($request);
        }
    }

    private function mapPayments($payments)
    {
        if ($payments) {
            $payments = collect(collect($payments)->toArray())->map(function ($item) {
                $item = (array)$item;
                return [
                    'UID' => $item['UID'],
                    'sale_uid' => $item['UID продажи'],
                    'reservation_code' => $item['Код резерва'],
                    'confirmation_date' => $item['Дата подтверждения'],
                    'venue' => $item['Место проведения'],
                    'event' => $item['Событие'],
                    'session' => $item['Сеанс'],
                    'hall' => $item['Зал'],
                    'sector' => $item['Сектор'],
                    'row' => $item['Ряд'],
                    'seat' => $item['Место'],
                    'order_type' => $item['Тип заказа'],
                    'order_status' => $item['Статус заказа'],
                    'ticket_type' => $item['Тип билета'],
                    'full_price' => $item['Полная цена'],
                    'price' => $item['Цена'],
                    'VK' => $item['ВК'],
                    'NK' => $item['НК'],
                    'promo_code' => $item['Промо-код'],
                    'client_email' => $item['Почта клиента'],
                    'client_phone' => $item['Телефон клиента'],
                    'referrer' => $item['Реферрер'],
                    'installment_commission' => $item['Комиссия по рассрочке'],
                ];
            })->filter(function ($item) {
                return $item['order_status'] === 'Подтверждено';
            })->values();
            return $payments;
        }
        return [];
    }
}
