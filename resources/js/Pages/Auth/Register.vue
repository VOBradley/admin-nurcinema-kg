<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, router, useForm} from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {TUser} from "@/types/user";
import {ref} from "vue";

defineProps<{
    users: TUser[]
}>()
const form = useForm({
    name: '',
    email: '',
    password: '',
    is_admin: false,
    password_confirmation: '',
});

const submit = () => {
    if (editedId.value) {
        form.post(route('user.update', {
            id: editedId.value
        }), {
            onFinish: () => {
                editedId.value = null
                form.reset('password', 'password_confirmation');
                form.name = ''
                form.email = ''
                form.is_admin = false
            },
        });
    } else {
        form.post(route('register'), {
            onFinish: () => {
                form.reset('password', 'password_confirmation');
            },
        });
    }
};
const editedId = ref<number | null>()
const edit = (user: TUser) => {
    form.name = user.name
    form.email = user.email
    form.is_admin = user.is_admin
    editedId.value = user.id
}

const create = () => {
    form.name = ''
    form.email = ''
    form.is_admin = false
    editedId.value = null
}

const remove = (id: TUser['id']) => {
    router.delete(route('profile.remove'), {
        data: {
            id: id
        },
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Аккаунты"/>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 light:text-gray-200 leading-tight">Пользователи</h2>
        </template>
        <v-card>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex flex-col">
                    <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                            <div class="overflow-hidden">
                                <table
                                    class="min-w-full text-left text-sm font-light light:text-black">
                                    <thead
                                        class="border-b border-neutral-200 font-medium light:border-black/10">
                                    <tr>
                                        <th scope="col" class="px-6 py-4">#</th>
                                        <th scope="col" class="px-6 py-4">Почта</th>
                                        <th scope="col" class="px-6 py-4">Админ</th>
                                        <th scope="col" class="px-6 py-4">Имя</th>
                                        <th scope="col" class="px-6 py-4">Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="border-b border-neutral-200 light:border-black/10" v-for="user in users">
                                        <td class="whitespace-nowrap px-6 py-4 font-medium">{{ user.id }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ user.email }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <v-switch disabled v-model="user.is_admin"></v-switch>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ user.name }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <v-btn class="mr-5" variant="tonal" icon="mdi-pencil" size="x-small"
                                                   color="green" @click="edit(user)"></v-btn>
                                            <v-btn @click="remove(user.id)" variant="tonal" size="x-small" color="red"
                                                   icon="mdi-delete"></v-btn>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </v-card>
        <v-card>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <form @submit.prevent="submit">
                        <div>
                            <InputLabel for="name" value="Имя"/>

                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full border"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                            />

                            <InputError class="mt-2" :message="form.errors.name"/>
                        </div>

                        <div class="mt-4">
                            <InputLabel for="email" value="Почта"/>

                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full border"
                                v-model="form.email"
                                required
                                autocomplete="username"
                            />

                            <InputError class="mt-2" :message="form.errors.email"/>
                        </div>
                        <div class="mt-4">
                            <InputLabel for="email" value="Админ"/>

                            <VSwitch
                                id="email"
                                type="email"
                                class="mt-1 block w-full border"
                                v-model="form.is_admin"
                                autocomplete="username"
                            />

                            <InputError class="mt-2" :message="form.errors.is_admin"/>
                        </div>

                        <div class="mt-4">
                            <InputLabel for="password" value="Пароль"/>

                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1 block w-full border"
                                v-model="form.password"
                                autocomplete="new-password"
                            />

                            <InputError class="mt-2" :message="form.errors.password"/>
                        </div>

                        <div class="mt-4">
                            <InputLabel for="password_confirmation" value="Подтверждение"/>

                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="mt-1 block w-full border"
                                v-model="form.password_confirmation"
                                autocomplete="new-password"
                            />

                            <InputError class="mt-2" :message="form.errors.password_confirmation"/>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <v-btn v-if="editedId" @click="create">Создать нового</v-btn>
                            <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }"
                                           :disabled="form.processing">
                                {{ editedId ? 'Изменить' : 'Создать' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </v-card>
    </AuthenticatedLayout>
</template>
