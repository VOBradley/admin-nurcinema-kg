<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';

export interface RootInterface {
    UID: number;
    sale_uid: number;
    reservation_code: string;
    confirmation_date: string;
    venue: string;
    event: string;
    session: string;
    hall: string;
    sector: string;
    row: string;
    seat: string;
    order_type: string;
    order_status: string;
    ticket_type: string;
    full_price: number;
    price: number;
    VK: number;
    NK: string;
    promo_code: number;
    client_email: string;
    client_phone: string;
    referrer: string;
    installment_commission: number;
}

const {payments} = defineProps<{
    payments: RootInterface[]
}>()

const headers = Object.freeze([
    { title: 'Событие', align: 'start', key: 'event' },
    { title: 'Сеанс', align: 'start', key: 'session' },
    { title: 'Зал', align: 'start', key: 'hall' },
    { title: 'Сектор', align: 'start', key: 'sector' },
    { title: 'Ряд', align: 'start', key: 'row' },
    { title: 'Место', align: 'start', key: 'seat' },
    { title: 'Тип заказа', align: 'start', key: 'order_type' },
    { title: 'Статус заказа', align: 'start', key: 'order_status' },
    { title: 'Тип билета', align: 'start', key: 'ticket_type' },
    { title: 'Полная цена', align: 'start', key: 'full_price' },
    { title: 'Цена', align: 'start', key: 'price' },
])

</script>

<template>
    <Head title="Платежи"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 light:text-gray-200 leading-tight">Платежи</h2>
        </template>
        <v-card>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white light:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <v-data-table :items="payments" :headers="headers">

                            </v-data-table>
                        </div>
                </div>
            </div>
        </v-card>
    </AuthenticatedLayout>
</template>
