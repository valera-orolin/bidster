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
                <p v-if="auction.lot.subcategory" class="hidden md:flex text-sm font-light text-my-gray3 mb-4">• {{ auction.lot.subcategory.category.name }}, {{ auction.lot.subcategory.name }}</p>
                <h2 class="text-base md:text-xl font-bold">{{ auction.lot.title }}</h2>
                <p class="hidden md:flex text-sm font-light text-my-gray3 mt-4">{{ truncate(auction.lot.description, 100) }}</p>
                <div class="flex justify-between items-center mt-4 space-x-2">
                    <p class="text-sm font-light text-my-gray3">{{ auction.bids_count }} bids</p>
                    <p class="text-sm font-light text-my-gray3">Max bid: <span class="text-my-violet font-normal">${{ auction.bids_max_bid_size }}</span></p>
                </div>
            </div>
            <img v-if="auction.lot.images[0]" :src="auction.lot.images[0].image_path" alt="Lot image" class="w-32 h-32 md:min-w-48 md:min-h-48 object-cover ml-4 rounded-2xl">
            <img v-else src="/images/icon.svg" alt="Lot image" class="w-32 h-32 md:w-48 md:h-48 ml-4 rounded-2xl">
        </div>
    </Link>
</template>