<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthBase title="Mot de passe oublié ?" description="Entrez votre adresse e-mail pour recevoir un lien de réinitialisation de mot de passe.">
        <Head title="Mot de passe oublié" />

        <div v-if="status" class="mb-6 text-center text-sm font-medium text-primary">
            {{ status }}
        </div>

        <div class="space-y-6">
            <form @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="email" class="text-foreground">Adresse e-mail</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="off"
                        v-model="form.email"
                        autofocus
                        placeholder="email@example.com"
                        class="bg-input border-border text-foreground
                               focus:border-primary focus:ring-primary
                               placeholder:text-muted-foreground"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button type="submit" class="w-full bg-primary hover:bg-primary/90 text-primary-foreground
                                          active:bg-primary/80"
                            :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                        Envoyer le lien de réinitialisation
                    </Button>
                </div>
            </form>

            <div class="space-x-1 text-center text-sm text-muted-foreground">
                <span>Ou, retourner à la</span>
                <TextLink :href="route('login')" class="text-primary hover:text-primary/90">connexion</TextLink>
            </div>
        </div>
    </AuthBase>
</template>
