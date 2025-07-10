<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthBase title="Connectez-vous à votre compte" description="Entrez votre adresse e-mail et votre mot de passe ci-dessous pour vous connecter.">
        <Head title="Connexion" />

        <div v-if="status" class="mb-6 text-center text-sm font-medium text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-4">
                <div class="grid gap-2">
                    <Label for="email" class="text-gray-700 dark:text-gray-200">Adresse e-mail</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        autofocus
                        :tabindex="1"
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
                    <div class="flex items-center justify-between">
                        <Label for="password" class="text-gray-700 dark:text-gray-200">Mot de passe</Label>
                        <TextLink v-if="canResetPassword" :href="route('password.request')"
                                  class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300" :tabindex="5">
                            Mot de passe oublié ?
                        </TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        v-model="form.password"
                        placeholder="Mot de passe"
                        class="bg-gray-50 border-gray-300 text-gray-800
                               dark:bg-gray-800 dark:border-gray-700 dark:text-white
                               focus:border-indigo-500 focus:ring-indigo-500
                               placeholder:text-gray-400 dark:placeholder:text-gray-500"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3 text-gray-700 dark:text-gray-200">
                        <Checkbox id="remember" v-model="form.remember" :tabindex="3"
                                  class="data-[state=checked]:bg-indigo-600 data-[state=checked]:text-white
                                         border-gray-300 dark:border-gray-700
                                         focus-visible:ring-indigo-500 focus-visible:ring-offset-background" />
                        <span>Se souvenir de moi</span>
                    </Label>
                </div>

                <Button type="submit" class="mt-2 w-full bg-indigo-600 hover:bg-indigo-700 text-white
                                          dark:bg-indigo-700 dark:hover:bg-indigo-600
                                          active:bg-indigo-800 dark:active:bg-indigo-800"
                        :tabindex="4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                    Se connecter
                </Button>
            </div>

            <div class="text-center text-sm text-gray-600 dark:text-gray-400">
                Vous n'avez pas de compte ?
                <TextLink :href="route('register')"
                          class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300" :tabindex="5">Inscrivez-vous</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
