<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    bid: {
    type: Object,
    required: true
  }
});
</script>

<template>
    <Link :href="route('bids.show', bid.id)">
        <div class="p-4 text-my-gray4 rounded-2xl border-0.5 border-my-gray2 hover:border-my-lila transition duration-500 cursor-pointer lg:hover:-translate-y-1 flex items-center justify-between md:space-x-10">
            <div class="w-full">
                <p class="text-sm font-light text-my-gray3 mb-4">
                    Auction status: 
                    <span :class="{
                    'text-green-400': bid.auction.status === 'Active', 
                    'text-orange-400': bid.auction.status === 'Finished', 
                    'text-red-400': bid.auction.status === 'Failed'
                    }">{{ bid.auction.status }}</span>
                </p>
                <h2 class="text-base md:text-xl font-bold">{{ bid.auction.lot.title }}</h2>
                <p class="text-sm font-light text-my-gray3 mt-4">Bid size: <span class="text-my-violet font-normal">ETH {{ bid.bid_size }}</span></p>
            </div>
            <img v-if="bid.auction.lot.images[0]" :src="bid.auction.lot.images[0].image_path" alt="Lot image" class="w-32 h-32 md:min-w-48 md:min-h-48 object-cover ml-4 rounded-2xl">
            <img v-else src="/images/icon.svg" alt="Avatar" class="w-32 h-32 md:w-48 md:h-48 ml-4 rounded-2xl">
        </div>
    </Link>
</template>