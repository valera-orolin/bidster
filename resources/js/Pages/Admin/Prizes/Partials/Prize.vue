<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    prize: {
    type: Object,
    required: true
  }
});
</script>

<template>
    <div class="p-4 text-my-gray4 rounded-2xl border-0.5 border-my-gray2 hover:border-my-lila transition duration-500 cursor-pointer lg:hover:-translate-y-1 flex items-center justify-between md:space-x-10">
        <div class="w-full">
            <h2 class="text-base md:text-xl font-bold">{{ prize.auction.lot.title }}</h2>
            <div class="flex justify-between items-center mt-4 space-x-2">
                <p class="text-sm font-light text-my-gray3">Seller: {{ prize.auction.seller.name }}</p>
            </div>
            <div class="flex justify-between items-center mt-4 space-x-2">
                <p class="text-sm font-light text-my-gray3">Purchaser: {{ prize.bid.user.name }}</p>
            </div>
            <div class="flex justify-between items-center mt-4 space-x-2">
                <p class="text-sm font-light text-my-gray3 mb-4">
                    Is received: 
                    <span v-if="prize.is_received" class="text-green-400">yes</span>
                    <span v-else class="text-red-400">no</span>
                </p>
            </div>
        </div>
        <img v-if="prize.auction.lot.images[0]" :src="prize.auction.lot.images[0].image_path" alt="Lot image" class="w-32 h-32 md:min-w-48 md:min-h-48 object-cover ml-4 rounded-2xl">
        <img v-else src="/images/icon.svg" alt="Lot image" class="w-32 h-32 md:w-48 md:h-48 ml-4 rounded-2xl">
    </div>
</template>