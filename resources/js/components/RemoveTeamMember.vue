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
    memberId: number;
    memberName: string;
    teamName: string;
}>();

const confirmInput = ref<HTMLInputElement | null>(null);
const dialogOpen = ref(false); // State to control dialog visibility

const form = useForm({
    confirmation: '', // Field to confirm by typing the member's name
});

const removeMember = () => {
    // Client-side validation: check if the typed confirmation matches the member's name
    if (form.confirmation !== props.memberName) {
        form.setError('confirmation', 'The member\'s name you entered does not match.');
        confirmInput.value?.focus();
        return;
    }

    form.delete(route('teams.members.destroy', { team: props.teamId, user: props.memberId }), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            // Optionally, add a global success notification here
        },
        onError: () => {
            // Focus on the confirmation input if there's a server-side error
            confirmInput.value?.focus();
        },
        onFinish: () => {
            form.reset(); // Always reset the form after the request
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
                type="button"
                variant="link"
                class="text-red-600 transition-colors duration-200 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-0 h-auto"
            >
                Remove
            </Button>
        </DialogTrigger>
        <DialogContent>
            <form class="space-y-6" @submit.prevent="removeMember">
                <DialogHeader class="space-y-3">
                    <DialogTitle>Are you sure you want to remove this member?</DialogTitle>
                    <DialogDescription>
                        This action will remove <span class="font-semibold text-gray-900 dark:text-white">"{{ memberName }}"</span>
                        from the team <span class="font-semibold text-gray-900 dark:text-white">"{{ teamName }}"</span>.
                        They will lose access to this team's resources.
                        <br /><br />
                        Please type the member's name "<span class="font-semibold text-gray-900 dark:text-white">{{ memberName }}</span>" to confirm.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-2">
                    <Label for="confirmation" class="sr-only">Confirm Member Name</Label>
                    <Input
                        id="confirmation"
                        type="text"
                        name="confirmation"
                        ref="confirmInput"
                        v-model="form.confirmation"
                        :placeholder="memberName"
                        :class="{ 'border-red-500': form.errors.confirmation }"
                        @keyup.enter="removeMember"
                    />
                    <InputError :message="form.errors.confirmation" />
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button type="button" variant="secondary" @click="closeModal"> Cancel </Button>
                    </DialogClose>

                    <Button type="submit" variant="destructive" :disabled="form.processing || form.confirmation !== memberName">
                        <span v-if="form.processing">Removing...</span>
                        <span v-else>Remove Member</span>
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
