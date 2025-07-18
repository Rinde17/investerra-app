<script setup lang="ts">
import { FlashMessages } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';

// Lucide Icons
import { AlertTriangle, CheckCircle, X as Close, Info, XCircle } from 'lucide-vue-next';

const show = ref(false);
const message = ref('');
const type = ref<'success' | 'error' | 'info' | 'warning'>('success');

const page = usePage();

const hideToast = () => {
    show.value = false;
    message.value = '';
};

// Watch flash messages
watch(
    () => page.props.flash,
    (newFlash: FlashMessages) => {
        if (newFlash.success) {
            message.value = newFlash.success;
            type.value = 'success';
            show.value = true;
            setTimeout(hideToast, 5000);
        } else if (newFlash.error) {
            message.value = newFlash.error;
            type.value = 'error';
            show.value = true;
            setTimeout(hideToast, 7000);
        }
    },
    { deep: true },
);

// Check initial flash on mount
onMounted(() => {
    const initialFlash: FlashMessages = page.props.flash;

    if (initialFlash.success) {
        message.value = initialFlash.success;
        type.value = 'success';
        show.value = true;
        setTimeout(hideToast, 5000);
    } else if (initialFlash.error) {
        message.value = initialFlash.error;
        type.value = 'error';
        show.value = true;
        setTimeout(hideToast, 7000);
    }
});

const toastClasses = computed(() => {
    switch (type.value) {
        case 'success':
            return 'bg-[var(--toast-success-bg)] text-[var(--toast-success-text)]';
        case 'error':
            return 'bg-[var(--toast-error-bg)] text-[var(--toast-error-text)]';
        case 'info':
            return 'bg-[var(--toast-info-bg)] text-[var(--toast-info-text)]';
        case 'warning':
            return 'bg-[var(--toast-warning-bg)] text-[var(--toast-warning-text)]';
        default:
            return 'bg-gray-700 text-white';
    }
});

const iconClasses = computed(() => {
    switch (type.value) {
        case 'success':
            return 'text-[color:var(--toast-success-text)]/80';
        case 'error':
            return 'text-[color:var(--toast-error-text)]/80';
        case 'info':
            return 'text-[color:var(--toast-info-text)]/80';
        case 'warning':
            return 'text-[color:var(--toast-warning-text)]/80';
        default:
            return 'text-white/80';
    }
});
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="transform opacity-0 translate-y-full"
        enter-to-class="transform opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="transform opacity-100 translate-y-0"
        leave-to-class="transform opacity-0 translate-y-full"
    >
        <div v-if="show && message" :class="['fixed right-4 bottom-4 z-50 flex items-center space-x-3 rounded-lg p-4 shadow-lg', toastClasses]">
            <CheckCircle v-if="type === 'success'" class="h-6 w-6" :class="iconClasses" />
            <XCircle v-else-if="type === 'error'" class="h-6 w-6" :class="iconClasses" />
            <Info v-else-if="type === 'info'" class="h-6 w-6" :class="iconClasses" />
            <AlertTriangle v-else-if="type === 'warning'" class="h-6 w-6" :class="iconClasses" />

            <span class="font-semibold">{{ message }}</span>

            <button
                @click="hideToast"
                class="cursor-pointer hover:bg-opacity-40 focus:ring-opacity-50 ml-auto rounded-full p-1  focus:ring-2  focus:outline-none"
            >
                <Close class="h-5 w-5 text-white" />
            </button>
        </div>
    </Transition>
</template>
