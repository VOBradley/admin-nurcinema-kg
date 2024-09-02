<script setup lang="ts">
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {Head, useForm} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
export interface Root {
    map: Map
    content: Content
    job: Job
    address: Address
    phones: Phones
}

export interface Map {
    id: number
    slug: string
    title: string
    content: string
    created_at: string
    updated_at: string
}

export interface Content {
    id: number
    slug: string
    title: string
    description: any
    created_at: string
    updated_at: string
}

export interface Job {
    id: number
    slug: string
    sort_order: number
    is_active: boolean
    title: string
    created_at: string
    updated_at: string
    fields: Field[]
}

export interface Field {
    id: number
    custom_group_id: number
    sort_order: number
    slug: string
    title: string
    is_active: boolean
    href: any
    created_at: string
    updated_at: string
}

export interface Address {
    id: number
    slug: string
    sort_order: number
    is_active: boolean
    title: string
    created_at: string
    updated_at: string
    fields: Field[]
}


export interface Phones {
    id: number
    slug: string
    sort_order: number
    is_active: boolean
    title: string
    created_at: string
    updated_at: string
    fields: Field[]
}


const {contact} = defineProps<{
    contact: Root
}>()

const form = useForm({
    content_title: contact?.content.title,
    map_link: contact?.map.content,
    job: {
        title: contact?.job.title,
        sort_order: contact?.job.sort_order,
        is_active: contact?.job.is_active,
        fields: contact?.job.fields
    },
    address: {
        title: contact?.address.title,
        sort_order: contact?.address.sort_order,
        is_active: contact?.address.is_active,
        fields: contact?.address.fields
    },
    phones: {
        title: contact?.phones.title,
        sort_order: contact?.phones.sort_order,
        is_active: contact?.phones.is_active,
        fields: contact?.phones.fields
    }
});
const tempField = {
    sort_order: 1,
    title: '',
    slug: '',
    href: '',
    is_active: true
}
const addField = (field) => {
    form[field].fields.push(JSON.parse(JSON.stringify(tempField)))
}
const submit = () => {
    form.post(route('custom-content.update-contact', form.data()), {
        onFinish: () => {
           window.location.reload()
        },
    });
}
</script>

<template>
    <Head title="О компании"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 light:text-gray-200 leading-tight">Редактирование
                контактов</h2>
        </template>
        <v-card>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white light:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <InputLabel for="name" value="Заголовок"/>

                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full border"
                                    v-model="form.content_title"
                                    autofocus
                                    autocomplete="name"
                                />

                                <InputError class="mt-2" :message="form.errors.content_title"/>
                            </div>
                            <div class="mb-3">
                                <InputLabel for="name" value="Ссылка на карту"/>

                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full border"
                                    v-model="form.map_link"
                                    autofocus
                                    autocomplete="name"
                                />

                                <InputError class="mt-2" :message="form.errors.map_link"/>
                            </div>
                            <v-card class="pa-4 border mb-3">
                                <InputLabel for="name" value="Заголовок <Время работы>"/>

                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full border"
                                    v-model="form.job.title"
                                    autofocus
                                    autocomplete="name"
                                />

                                <v-switch v-model="form.job.is_active" label="Активность"></v-switch>
                                <v-text-field density="compact" variant="outlined" type="number" v-model="form.job.sort_order"
                                              label="Сортировка"></v-text-field>
                                <v-card class="pa-3 border ma-2">
                                    <template #title>Список времени работы</template>
                                    <template #text>
                                        <v-card class="pa-2 mb-3 border" v-for="(item, index) in form.job.fields">
                                            <div class="mb-2">Значение <b>#{{index+1}}</b></div>
                                            <v-text-field density="compact" variant="outlined" v-model="item.slug"
                                                          label="Ключ (любой)"></v-text-field>
                                            <v-switch v-model="item.is_active" label="Активность"></v-switch>
                                            <v-text-field density="compact" variant="outlined" type="number" v-model="item.sort_order"
                                                          label="Сортировка"></v-text-field>
                                            <v-text-field density="compact" variant="outlined" v-model="item.title"
                                                          label="Значение"></v-text-field>
                                            <v-text-field density="compact" variant="outlined" v-model="item.href"
                                                          label="Ссылка (по необходимости)"></v-text-field>
                                        </v-card>
                                        <v-btn color="orange" variant="tonal" @click="addField('job')">Добавить время
                                            работы
                                        </v-btn>
                                    </template>
                                </v-card>
                            </v-card>
                            <v-card class="pa-4 border mb-3">
                                <InputLabel for="name" value="Заголовок <Адрес>"/>

                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full border"
                                    v-model="form.address.title"
                                    autofocus
                                    autocomplete="name"
                                />

                                <v-switch v-model="form.address.is_active" label="Активность"></v-switch>
                                <v-text-field density="compact" variant="outlined" type="number" v-model="form.address.sort_order"
                                              label="Сортировка"></v-text-field>
                                <v-card class="pa-3 border ma-2">
                                    <template #title>Список адресов</template>
                                    <template #text>
                                        <v-card class="pa-2 mb-3 border" v-for="(item, index) in form.address.fields">
                                            <div class="mb-2">Значение <b>#{{index+1}}</b></div>
                                            <v-text-field density="compact" variant="outlined" v-model="item.slug"
                                                          label="Ключ (любой)"></v-text-field>
                                            <v-switch v-model="item.is_active" label="Активность"></v-switch>
                                            <v-text-field density="compact" variant="outlined" type="number" v-model="item.sort_order"
                                                          label="Сортировка"></v-text-field>
                                            <v-text-field density="compact" variant="outlined" v-model="item.title"
                                                          label="Значение"></v-text-field>
                                            <v-text-field density="compact" variant="outlined" v-model="item.href"
                                                          label="Ссылка (по необходимости)"></v-text-field>
                                        </v-card>
                                        <v-btn color="orange" variant="tonal" @click="addField('address')">Добавить адрес
                                        </v-btn>
                                    </template>
                                </v-card>
                            </v-card>
                            <v-card class="pa-4 border mb-3">
                                <InputLabel for="name" value="Заголовок <Телефоны>"/>
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full border"
                                    v-model="form.phones.title"
                                    autofocus
                                    autocomplete="name"
                                />

                                <v-switch v-model="form.phones.is_active" label="Активность"></v-switch>
                                <v-text-field density="compact" variant="outlined" type="number" v-model="form.phones.sort_order"
                                              label="Сортировка"></v-text-field>
                                <v-card class="pa-3 border ma-2">
                                    <template #title>Список номеров</template>
                                    <template #text>
                                        <v-card class="pa-2 mb-3 border" v-for="(item, index) in form.phones.fields">
                                            <div class="mb-2">Значение <b>#{{index+1}}</b></div>
                                            <v-text-field density="compact" variant="outlined" v-model="item.slug"
                                                          label="Ключ (любой)"></v-text-field>
                                            <v-switch v-model="item.is_active" label="Активность"></v-switch>
                                            <v-text-field density="compact" variant="outlined" type="number" v-model="item.sort_order"
                                                          label="Сортировка"></v-text-field>
                                            <v-text-field density="compact" variant="outlined" v-model="item.title"
                                                          label="Значение"></v-text-field>
                                            <v-text-field density="compact" variant="outlined" v-model="item.href"
                                                          label="Ссылка (по необходимости)"></v-text-field>
                                        </v-card>
                                        <v-btn color="orange" variant="tonal" @click="addField('phones')">Добавить номер
                                        </v-btn>
                                    </template>
                                </v-card>
                            </v-card>
                            <v-btn type="submit">Сохранить</v-btn>
                        </form>
                    </div>
                </div>
            </div>
        </v-card>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
