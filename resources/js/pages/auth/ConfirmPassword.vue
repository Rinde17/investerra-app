<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
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
    <AuthBase title="Confirmez votre mot de passe" description="Ceci est une zone sécurisée de l'application. Veuillez confirmer votre mot de passe avant de continuer.">
        <Head title="Confirmer le mot de passe" />

        <form @submit.prevent="submit">
            <div class="space-y-6">
                <div class="grid gap-2">
                    <Label htmlFor="password" class="text-foreground">Mot de passe</Label>
                    <Input
                        id="password"
                        type="password"
                        class="block w-full bg-input border-border text-foreground
                               focus:border-primary focus:ring-primary
                               placeholder:text-muted-foreground"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        autofocus
                        placeholder="Votre mot de passe"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center">
                    <Button type="submit" class="w-full bg-primary hover:bg-primary/90 text-primary-foreground
                                          active:bg-primary/80"
                            :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                        Confirmer le mot de passe
                    </Button>
                </div>
            </div>
        </form>
    </AuthBase>
</template>
