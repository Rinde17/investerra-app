<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AuthLayout title="Confirmez votre mot de passe" description="Ceci est une zone sécurisée de l'application. Veuillez confirmer votre mot de passe avant de continuer.">
        <Head title="Confirmer le mot de passe" />

        <form @submit.prevent="submit">
            <div class="space-y-6">
                <div class="grid gap-2">
                    <Label htmlFor="password" class="text-gray-200">Mot de passe</Label>
                    <Input
                        id="password"
                        type="password"
                        class="block w-full bg-[#100c1c] text-white border border-[#3E3E3A] focus:border-indigo-500 placeholder:text-gray-500"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        autofocus
                        placeholder="Mot de passe"
                    />

                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center">
                    <Button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                        Confirmer le mot de passe
                    </Button>
                </div>
            </div>
        </form>
    </AuthLayout>
</template>
