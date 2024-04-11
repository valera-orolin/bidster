<script setup>
import { Link } from '@inertiajs/vue3';

defineProps(['user']);
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-my-black">
        <div class="m-4 text-3xl md:text-6xl font-bold text-my-gray4">
            <div>
                You have been
                <span class="my-gradient-text">banned</span>
                from 
                <Link :href="'/'">Bidster,</Link>
                {{ user.name }} :)
            </div>
            <div class="flex justify-center mt-10 hover:text-my-lila transition duration-500">
                <Link :href="route('logout')" method="post" as="button">
                    <font-awesome-icon :icon="['fas', 'right-from-bracket']" />
                </Link>
            </div>
        </div>
    </div>
</template>
