<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthBase title="Créez votre compte" description="Entrez vos informations ci-dessous pour créer votre compte.">
        <Head title="Inscription" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-4">
                <div class="grid gap-2">
                    <Label for="name" class="text-gray-700 dark:text-gray-200">Nom complet</Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        v-model="form.name"
                        placeholder="Votre nom complet"
                        class="bg-gray-50 border-gray-300 text-gray-800
                               dark:bg-gray-800 dark:border-gray-700 dark:text-white
                               focus:border-indigo-500 focus:ring-indigo-500
                               placeholder:text-gray-400 dark:placeholder:text-gray-500"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email" class="text-gray-700 dark:text-gray-200">Adresse e-mail</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="email@example.com"
                        class="bg-gray-50 border-gray-300 text-gray-800
                               dark:bg-gray-800 dark:border-gray-700 dark:text-white
                               focus:border-indigo-500 focus:ring-indigo-500
                               placeholder:text-gray-400 dark:placeholder:text-gray-500"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password" class="text-gray-700 dark:text-gray-200">Mot de passe</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="Créer un mot de passe"
                        class="bg-gray-50 border-gray-300 text-gray-800
                               dark:bg-gray-800 dark:border-gray-700 dark:text-white
                               focus:border-indigo-500 focus:ring-indigo-500
                               placeholder:text-gray-400 dark:placeholder:text-gray-500"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation" class="text-gray-700 dark:text-gray-200">Confirmer le mot de passe</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="Confirmer votre mot de passe"
                        class="bg-gray-50 border-gray-300 text-gray-800
                               dark:bg-gray-800 dark:border-gray-700 dark:text-white
                               focus:border-indigo-500 focus:ring-indigo-500
                               placeholder:text-gray-400 dark:placeholder:text-gray-500"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button type="submit" class="mt-2 w-full bg-indigo-600 hover:bg-indigo-700 text-white
                                          dark:bg-indigo-700 dark:hover:bg-indigo-600
                                          active:bg-indigo-800 dark:active:bg-indigo-800"
                        tabindex="5" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                    Créer un compte
                </Button>
            </div>

            <div class="text-center text-sm text-gray-600 dark:text-gray-400">
                Vous avez déjà un compte ?
                <TextLink :href="route('login')"
                          class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300" :tabindex="6">Connectez-vous</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
