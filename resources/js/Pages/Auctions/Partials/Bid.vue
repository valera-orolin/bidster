<script setup>
import dayjs from 'dayjs';
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    bid: {
    type: Object,
    required: true
  }
});
</script>

<template>
    <Link :href="route('profile.show', bid.user.id)">
        <div class="p-4 text-my-gray4 rounded-2xl border-0.5 border-my-gray2 hover:border-my-lila transition duration-500 cursor-pointer lg:hover:-translate-y-1 flex items-center justify-between md:space-x-10">
            <div class="w-full">
                <h2 class="text-base md:text-xl font-bold">{{ bid.user.name }}</h2>
                <p class="text-sm font-light text-my-gray3 mt-4">Bid time: {{ dayjs(bid.created_at).format('MMMM D, YYYY h:mm:ss A') }}</p>
                <p class="text-sm font-light text-my-gray3 mt-4">Bid size: <span class="text-my-violet font-normal">ETH {{ bid.bid_size }}</span></p>
            </div>
            <img v-if="bid.user.avatar" :src="bid.user.avatar" alt="Avatar" class="w-32 h-32 md:min-w-48 md:min-h-48 object-cover ml-4 rounded-2xl">
            <img v-else src="/images/icon.svg" alt="Avatar" class="w-32 h-32 md:w-48 md:h-48 ml-4 rounded-2xl">
        </div>
    </Link>
</template>