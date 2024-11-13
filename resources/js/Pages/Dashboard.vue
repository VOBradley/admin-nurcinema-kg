<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';
const {configs} = defineProps<{
    configs: {
      slug: string
      value: boolean
    }[]
}>()

const configMatrix = {
    CAN_BUY_TICKET: "Разрешить покупку билетов",
}

const changeConfig = (slug, value) => {
    console.log(slug, value)
    router.post('/change-config', {
        slug: slug,
        value: value
    })
}
</script>

<template>
    <Head title="Панель"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 light:text-gray-200 leading-tight">Панель</h2>
        </template>
        <v-card>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white light:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 light:text-gray-100">Панель NUR-CINEMA.KG</div>
                    </div>
                    <div class="bg-white light:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <v-card class="pa-3 d-flex align-center" color="green" variant="outlined" v-for="item in configs">
                            {{configMatrix[item.slug] || 'Не известный конфиг'}}
                            <v-switch class="ml-5" color="green" v-model="item.value" hide-details @change="(value) => changeConfig(item.slug, item.value)"></v-switch>
                        </v-card>
                    </div>
                </div>
            </div>
        </v-card>
    </AuthenticatedLayout>
</template>
