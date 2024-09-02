<script setup lang="ts">
import {Head, router, useForm} from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
const {content, image} = defineProps<{
    content: TCustomContent,
    image: TCustomImage
}>()
export interface TCustomContent {
    id: number
    slug: string
    title: string
    description: string
    created_at: string
    updated_at: string
}

export interface TCustomImage {
    id: number
    slug: string
    image: string
    created_at: string
    updated_at: string
}

const form = useForm({
    title: content?.title,
    description: content?.description,
    image: null
});
const submit = () => {
    form.post(route('custom-content.update', {
        pageSlug: content.slug
    }), {
        onFinish: () => {

        },
    });
};
</script>

<template>
    <Head title="О компании"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 light:text-gray-200 leading-tight">Редактирование {{
                    content?.slug
                }}</h2>
        </template>
        <v-card>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white light:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="name" value="Заголовок"/>

                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full border"
                                    v-model="form.title"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />

                                <InputError class="mt-2" :message="form.errors.title"/>
                            </div>
                            <div class="my-5">
                                <InputLabel for="desc" value="Описание"/>

                                <QuillEditor v-model:content="form.description" contentType="html" theme="snow" placeholder="Описание"/>


                            </div>
                            <div v-if="image?.image" class="preview">
                                <img :src="`/storage/page-images/${image.image}`" alt="">
                            </div>
                            <v-file-input label="Картинка" v-model="form.image">

                            </v-file-input>
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
