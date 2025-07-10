<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';

const props = defineProps<{
    teamId: number;
    teamName: string;
}>();

const confirmInput = ref<HTMLInputElement | null>(null);
const dialogOpen = ref(false); // State to control dialog visibility

const form = useForm({
    confirmation: '', // Field to confirm by typing the team name
});

const deleteTeam = () => {
    // Basic client-side validation: check if the typed confirmation matches the team name
    if (form.confirmation !== props.teamName) {
        form.setError('confirmation', 'The team name you entered does not match.');
        confirmInput.value?.focus();
        return;
    }

    form.delete(route('teams.destroy', { team: props.teamId }), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            // Optionally, add a global success notification here if your app has one
        },
        onError: () => {
            // Focus on the confirmation input if there's a server-side error
            confirmInput.value?.focus();
        },
        onFinish: () => {
            // Always reset the form after the request
            form.reset();
        },
    });
};

const closeModal = () => {
    dialogOpen.value = false; // Close the dialog
    form.clearErrors(); // Clear any validation errors
    form.reset(); // Reset the form fields
};
</script>

<template>
    <Dialog :open="dialogOpen" @update:open="dialogOpen = $event">
        <DialogTrigger as-child>
            <Button
                variant="destructive"
                class="bg-red-600 text-white hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600"
            >
                Delete Team
            </Button>
        </DialogTrigger>
        <DialogContent>
            <form class="space-y-6" @submit.prevent="deleteTeam">
                <DialogHeader class="space-y-3">
                    <DialogTitle>Are you sure you want to delete this team?</DialogTitle>
                    <DialogDescription>
                        This action cannot be undone. This will permanently delete the team
                        <span class="font-semibold text-gray-900 dark:text-white">"{{ teamName }}"</span>
                        and all its associated data.
                        <br /><br />
                        Please type the team's name "<span class="font-semibold text-gray-900 dark:text-white">{{ teamName }}</span>" to confirm.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-2">
                    <Label for="confirmation" class="sr-only">Confirm Team Name</Label>
                    <Input
                        id="confirmation"
                        type="text"
                        name="confirmation"
                        ref="confirmInput"
                        v-model="form.confirmation"
                        :placeholder="teamName"
                        :class="{ 'border-red-500': form.errors.confirmation }"
                        @keyup.enter="deleteTeam"
                    />
                    <InputError :message="form.errors.confirmation" />
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button type="button" variant="secondary" @click="closeModal"> Cancel </Button>
                    </DialogClose>

                    <Button type="submit" variant="destructive" :disabled="form.processing || form.confirmation !== teamName">
                        <span v-if="form.processing">Deleting...</span>
                        <span v-else>Delete Team</span>
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
