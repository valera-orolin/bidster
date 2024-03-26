<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    auction: {
    type: Object,
    required: true
  }
});

const truncate = (string, value) => {
    return string.length > value ? string.substring(0, value) + "..." : string;
}
</script>

<template>
    <Link :href="route('lots.show', auction.id)">
        <div class="p-4 text-my-gray4 rounded-2xl border-0.5 border-my-gray2 hover:border-my-lila transition duration-500 cursor-pointer lg:hover:-translate-y-1 flex items-center justify-between md:space-x-10">
            <div class="w-full">
                <p class="hidden md:flex text-sm font-light text-my-gray3 mb-4">• {{ auction.lot.category }}</p>
                <h2 class="text-base md:text-xl font-bold">{{ auction.lot.title }}</h2>
                <p class="hidden md:flex text-sm font-light text-my-gray3 mt-4">{{ truncate(auction.lot.description, 100) }}</p>
                <div class="flex justify-between items-center mt-4 space-x-2">
                    <p class="text-sm font-light text-my-gray3">{{ auction.lot.bids_count }} bids</p>
                    <p class="text-sm font-light text-my-gray3">Max bid: <span class="text-my-violet font-normal">${{ auction.lot.max_bid }}</span></p>
                </div>
            </div>
            <img :src="auction.lot.image" alt="Lot image" class="w-32 h-32 md:w-48 md:h-48 object-cover ml-4 rounded-2xl">
        </div>
    </Link>
</template>