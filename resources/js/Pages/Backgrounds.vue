<script setup lang="ts">
import {Head, useForm} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref} from "vue";

const {bgs} = defineProps<{
    bgs: {
        active: boolean,
        image: string
    }[]
}>()

const form = useForm({
    id: null,
    active: true,
    image: null
});
const tempField = {
    active: true,
    image: null
}

const submit = () => {
    if (form?.id) {
        form.post(route('backgrounds.update', form.data()), {
            onFinish: () => {
                window.location.reload()
            },
        });
    } else {
        form.post(route('backgrounds.create', form.data()), {
            onFinish: () => {
                window.location.reload()
            },
        });
    }
}

const headers = [
    {title: 'ID', key: 'id'},
    {title: 'Изображение', key: 'image'},
    {title: 'Активность', key: 'active'},
    {title: 'Действие', key: 'actions'},
]

const activeImage = ref()

const editBg = (item) => {
    // form.reset(item)
    form.id = item.id
    form.active = item.active
    activeImage.value = item.image
}

const createBg = () => {
    form.id = null
    form.active = null
    activeImage.value = null
}

const deleteBg = (id: number) => {
    form.delete(route('backgrounds.delete', {
        id: id
    }))
}
</script>

<template>
    <Head title="Задние фоны"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 light:text-gray-200 leading-tight">Редактирование
                задних фонов</h2>
        </template>
        <v-card>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white light:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <v-data-table :items="bgs" :headers="headers">
                            <template #item.image="{item}">
                                <v-img height="100" :src="`/storage/page-images/${item?.image}`"></v-img>
                            </template>
                            <template #item.active="{item}">
                                <v-switch hide-details color="green" v-model="item.active"></v-switch>
                            </template>
                            <template #item.actions="{item}">
                                <v-btn @click="editBg(item)" color="blue" size="x-small" icon>
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn @click="deleteBg(item.id)" color="red" size="x-small" icon>
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                            </template>
                        </v-data-table>
                        <form @submit.prevent="submit">
                            <v-switch color="green" v-model="form.active" label="Активность"></v-switch>
                            <div v-if="form?.image" class="preview">
                                <img :src="`https://admin.nurcinema.kg/storage/page-images/${form?.image}`" alt=""/>
                            </div>
                            <div v-if="activeImage">
                                <img :src="`/storage/page-images/${activeImage}`" alt=""/>
                            </div>
                            <v-file-input label="Картинка" v-model="form.image"/>
                            <v-btn color="yellow" @click="createBg" v-if="form?.id">Создать</v-btn>
                            <v-btn color="orange" type="submit">{{ form.id ? 'Изменить' : 'Создать' }}</v-btn>
                        </form>
                    </div>
                </div>
            </div>
        </v-card>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
