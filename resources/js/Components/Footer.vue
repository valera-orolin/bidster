<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="bg-my-gray2 text-my-gray4 px-4 md:px-32 py-8">
        <div class="container mx-auto flex justify-between items-center">
            <div>
                <Link :href="route('lots.index')">
                    <img src="/images/logo.png" alt="Bidster" class="h-8"/>
                </Link>
                <p class="text-sm mt-4">Your trusted platform for online auctions.</p>
            </div>
            <div class="flex space-x-2 md:space-x-6">
                <a href="#" class="text-xl md:text-2xl p-4 rounded-full bg-my-lila w-9 h-9 md:w-12 md:h-12 flex items-center justify-center hover:bg-my-gray3 hover:text-my-lila transition duration-500">
                    <font-awesome-icon :icon="['fab', 'github']" />
                </a>
                <a href="#" class="text-xl md:text-2xl p-4 rounded-full bg-my-lila w-9 h-9 md:w-12 md:h-12 flex items-center justify-center hover:bg-my-gray3 hover:text-my-lila transition duration-500">
                    <font-awesome-icon :icon="['fab', 'linkedin-in']" />
                </a>
                <a href="#" class="text-xl md:text-2xl p-4 rounded-full bg-my-lila w-9 h-9 md:w-12 md:h-12 flex items-center justify-center hover:bg-my-gray3 hover:text-my-lila transition duration-500">
                    <font-awesome-icon :icon="['fas', 'envelope']" />
                </a>
            </div>
        </div>
        <div class="text-center mt-4 text-sm">
            © 2024 Bidster. Developed by Valeryia Matveyeva.
        </div>
    </div>
</template>