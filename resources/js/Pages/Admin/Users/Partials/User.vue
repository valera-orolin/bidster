<script setup>
import { Link } from '@inertiajs/vue3';
import dayjs from 'dayjs';

const props = defineProps(['user']);
</script>

<template>
    <Link :href="route('admin.users.edit', user.id)">
        <div class="p-4 text-my-gray4 rounded-2xl border-0.5 border-my-gray2 hover:border-my-lila transition duration-500 cursor-pointer lg:hover:-translate-y-1 flex items-center justify-between md:space-x-10">
            <div class="w-full">
                <h2 class="text-base md:text-xl font-bold">{{ user.name }}</h2>
                <p class="text-sm font-light text-my-gray3 mt-3">
                    Status: 
                    <span :class="{
                    'text-green-400': user.status === 'Active', 
                    'text-red-400': user.status === 'Banned'
                    }">{{ user.status }}</span>
                </p>
                <p class="text-sm font-light text-my-gray3 mt-3">
                    Role: 
                    <span :class="{
                    'text-sky-400': user.role === 'User', 
                    'text-lime-400': user.role === 'Director',
                    'text-yellow-400': user.role === 'Manager'
                    }">{{ user.role }}</span>
                </p>
                <p class="text-sm font-light text-my-gray3 mt-3">Registration date: {{ dayjs(user.created_at).format('MMMM D, YYYY h:mm A') }}</p>
                <p class="text-sm font-light text-my-gray3 mt-3">Auctions count: {{ user.auctions_count }}</p>
                <p class="text-sm font-light text-my-gray3 mt-3">Bids count: {{ user.bids_count }}</p>
            </div>
            <img v-if="user.avatar" :src="user.avatar" alt="Avatar" class="w-32 h-32 md:min-w-48 md:min-h-48 object-cover ml-4 rounded-2xl">
            <img v-else src="/images/icon.svg" alt="Avatar" class="w-32 h-32 md:w-48 md:h-48 ml-4 rounded-2xl">
        </div>
    </Link>
</template>