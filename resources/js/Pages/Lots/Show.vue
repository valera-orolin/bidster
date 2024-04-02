<script setup>
import ButtonGradient from '@/Components/ButtonGradient.vue';
import ButtonWhite from '@/Components/ButtonWhite.vue';
//import Chat from './Partials/Chat.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    auction: {
    type: Object,
    required: true
  }
});

let showImageViewer = ref(false);
let currentImageIndex = ref(0);

const openImage = () => {
  showImageViewer.value = true;
};

const nextImage = () => {
  if (currentImageIndex.value < props.auction.lot.images.length - 1) {
    currentImageIndex.value++;
  } else {
    currentImageIndex.value = 0;
  }
};

const previousImage = () => {
  if (currentImageIndex.value > 0) {
    currentImageIndex.value--;
  } else {
    currentImageIndex.value = props.auction.lot.images.length - 1;
  }
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="my-animation-in-up animation-lg">
            <div class="flex flex-col lg:flex-row my-animation">
                <div class="flex flex-col items-start">
                    <img v-if="auction.lot.images[currentImageIndex]" :src="auction.lot.images[currentImageIndex].image_path" alt="Lot image" class="w-full h-56 md:h-72 lg:w-200 lg:h-112 object-cover rounded-2xl cursor-zoom-in" @click="openImage" />
                    <div v-else class="w-full h-56 md:h-72 lg:w-200 lg:h-112 object-cover rounded-2xl bg-my-gray2">
                        <img src="/images/icon.svg" alt="Lot image" class="h-24 md:h-32 m-8" />
                    </div>
                    <div class="flex justify-between w-full mt-4">
                        <ButtonWhite text="❮" @click="previousImage" />
                        <ButtonWhite text="❯" @click="nextImage" />
                    </div>
                </div>

                <div class="flex flex-col text-my-gray4 mt-5 lg:mt-0 lg:ml-10">
                    <h2 class="text-2xl lg:text-4xl font-bold">{{ auction.lot.title }}</h2>

                    <p class="text-base font-light text-my-gray3 mt-3">{{ auction.lot.address }}</p>
                    <p class="text-base font-light text-my-gray3 mt-1">Starting: {{ auction.created_at }}</p>
                    <p class="text-base font-light text-my-gray3 mt-1">Ending: {{ auction.lot.end_date }}</p>

                    <p class="text-base font-light text-my-gray3 mt-3">Starting price: <span class="text-my-violet font-normal">${{ auction.lot.starting_price }}</span></p>
                    <p class="text-base font-light text-my-gray3 mt-1">{{ auction.lot.bids_count }} bids</p>
                    <p class="text-base font-light text-my-gray3 mt-1">Max bid: <span class="bg-my-violet py-1 px-2 rounded-xl font-normal">${{ auction.lot.max_bid }}</span></p>

                    <Link :href="route('bids.create', auction.id)">
                        <ButtonGradient class="mt-8 w-56" :text="'Place a bid'" />
                    </Link>

                    <p class="font-light text-my-gray3 mt-3">
                        Auction status: 
                        <span :class="{
                        'text-green-400': auction.status === 'Active', 
                        'text-orange-400': auction.status === 'Finished', 
                        'text-red-400': auction.status === 'Failed'
                        }">{{ auction.status }}</span>
                    </p>
                </div>
            </div>

            

            <div class="flex flex-col">
                <p class="text-2xl font-bold text-my-gray3 mt-14">Description</p>
                <p class="text-base font-light text-my-gray3 mt-3">{{ auction.lot.description }}</p>

                <!---
                <p class="text-2xl font-bold text-my-gray3 mt-14 mb-5">Сharacteristics</p>
                <div v-for="(characteristic, index) in lot.characteristics" :key="index" :class="{ 'bg-my-gray2 rounded-2xl': index % 2 === 0 }">
                    <div class="grid grid-cols-2 py-2 px-6">
                        <p class="text-sm font-light text-my-gray3">{{ characteristic.name }}</p>
                        <p class="text-sm font-light text-my-gray3">{{ characteristic.value }}</p>
                    </div>
                </div>
            -->

                <p class="text-2xl font-bold text-my-gray3 mt-14">Seller</p>
                <div class="flex flex-col space-y-4 mt-5 bg-my-gray2 rounded-2xl w-full lg:w-fit p-8 border-0.5 border-my-gray2 hover:border-my-gray-2 hover:bg-my-black transition duration-500 cursor-pointer lg:hover:-translate-y-1">
                    <router-link to="/profile">
                        <div class="w-16 h-16 md:w-24 md:h-24 overflow-hidden rounded-2xl">
                            <img v-if="auction.seller.avatar" :src="auction.seller.avatar" alt="Avatar" class="object-cover min-w-full min-h-full">
                            <img v-else src="/images/icon.svg" alt="Avatar" class="min-w-full min-h-full">
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-8">
                                <p class="text-base font-semibold text-my-gray3">{{ auction.seller.name }}</p>
                                <!---
                                <div class="flex items-center space-x-2">
                                    <font-awesome-icon :icon="['fas', 'star']" class="text-base text-my-orange my-gradient-text" />
                                    <p class="text-base font-light text-my-gray3 mt-0.5">{{ auction.seller.rating }}</p>
                                </div>-->
                            </div>
                            <p class="text-sm text-gray-500">{{ auction.seller.auctions_count }} auctions held</p>
                            <p class="text-base font-light text-my-gray3 lg:w-96">{{ auction.seller.description }}</p>
                        </div>
                    </router-link>
                </div>
                <p class="text-2xl font-bold text-my-gray3 mt-14 mb-5">Chat about <span class="my-gradient-text">{{ auction.lot.title }}</span></p>
                <!---
                <Chat />-->
            </div>
        </div>

        <div v-if="showImageViewer" class="fixed inset-0 bg-my-black bg-opacity-50 flex items-center justify-center z-50">
            <img :src="auction.lot.images[currentImageIndex].image_path" class="max-h-screen max-w-screen" />
            <button class="absolute top-0 right-0 m-4 text-white text-5xl" @click="showImageViewer = false">×</button>
        </div>

    </AuthenticatedLayout>
</template>