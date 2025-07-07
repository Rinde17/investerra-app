<script setup lang="ts">
import { ref, watch, onMounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { FlashMessages } from '@/types';


const show = ref(false);
const message = ref('');
const type = ref('success'); // 'success', 'error', 'info', 'warning'

const page = usePage();

const hideToast = () => {
    show.value = false;
    message.value = '';
};

// Watch pour les messages flash
watch(() => page.props.flash, (newFlash: FlashMessages) => {
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
    // Ajoutez plus de conditions pour info, warning si vous les utilisez
}, { deep: true }); // 'deep: true' est important pour les objets imbriqués

// Affiche un toast immédiatement au chargement si un message flash est déjà présent
onMounted(() => {
    const initialFlash: FlashMessages = page.props.flash; // Typage correct ici

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

// Propriétés calculées pour les classes dynamiques des toasts
const toastClasses = computed(() => {
    switch (type.value) {
        case 'success':
            return 'bg-green-500 text-white';
        case 'error':
            return 'bg-red-500 text-white';
        case 'info':
            return 'bg-blue-500 text-white';
        case 'warning':
            return 'bg-yellow-500 text-white';
        default:
            return 'bg-gray-700 text-white';
    }
});

const iconClasses = computed(() => {
    switch (type.value) {
        case 'success':
            return 'text-green-200';
        case 'error':
            return 'text-red-200';
        case 'info':
            return 'text-blue-200';
        case 'warning':
            return 'text-yellow-200';
        default:
            return 'text-gray-200';
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
        <div v-if="show && message" :class="['fixed bottom-4 right-4 z-50 p-4 rounded-lg shadow-lg flex items-center space-x-3', toastClasses]">
            <svg v-if="type === 'success'" class="h-6 w-6" :class="iconClasses" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <svg v-else-if="type === 'error'" class="h-6 w-6" :class="iconClasses" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <svg v-else-if="type === 'info'" class="h-6 w-6" :class="iconClasses" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <svg v-else-if="type === 'warning'" class="h-6 w-6" :class="iconClasses" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>

            <span class="font-semibold">{{ message }}</span>

            <button @click="hideToast" class="ml-auto p-1 rounded-full hover:bg-white hover:bg-opacity-20 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50">
                <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </Transition>
</template>
