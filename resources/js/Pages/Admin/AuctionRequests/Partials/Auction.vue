<script setup>
import ButtonWhite from '@/Components/ButtonWhite.vue';
import { ref } from 'vue';

const props = defineProps({
    request: {
    type: Object,
    required: true
  }
});

let showImageViewer = ref(false);
let currentImageIndex = ref(0);

const nextImage = () => {
  if (currentImageIndex.value < lot.images.length - 1) {
    currentImageIndex.value++;
  } else {
    currentImageIndex.value = 0;
  }
};

const previousImage = () => {
  if (currentImageIndex.value > 0) {
    currentImageIndex.value--;
  } else {
    currentImageIndex.value = lot.images.length - 1;
  }
};
</script>

<template>
    <div class="my-animation-in-up animation-lg">
        <div class="flex flex-col lg:flex-row">
            <div class="flex flex-col items-start">
                <img :src="request.lot.image" alt="Lot image" class="w-full h-56 md:h-72 lg:w-200 lg:h-112 object-cover rounded-2xl" />
                <div class="flex justify-between w-full mt-4">
                    <ButtonWhite text="❮" @click="previousImage" />
                    <ButtonWhite text="❯" @click="nextImage" />
                </div>
            </div>

            <div class="flex flex-col text-my-gray4 mt-5 lg:mt-0 lg:ml-10">
                <h2 class="text-2xl lg:text-4xl font-bold">{{ request.lot.title }}</h2>

                <p class="text-base font-light text-my-gray3 mt-3">{{ request.lot.address }}</p>
                <p class="text-base font-light text-my-gray3 mt-1">Starting: {{ request.lot.created_at }}</p>
                <p class="text-base font-light text-my-gray3 mt-1">Ending: {{ request.lot.end_date }}</p>

                <p class="text-base font-light text-my-gray3 mt-3">Starting price: <span class="text-my-violet font-normal">${{ request.lot.starting_price }}</span></p>
            </div>
        </div>

        <div class="flex flex-col">
            <p class="text-2xl font-bold text-my-gray3 mt-14">Description</p>
            <p class="text-base font-light text-my-gray3 mt-3">{{ request.lot.description }}</p>

            <!---
            <p class="text-2xl font-bold text-my-gray3 mt-14 mb-5">Сharacteristics</p>
            <div v-for="(characteristic, index) in lot.characteristics" :key="index" :class="{ 'bg-my-gray2 rounded-2xl': index % 2 === 0 }">
                <div class="grid grid-cols-2 py-2 px-6">
                    <p class="text-sm font-light text-my-gray3">{{ characteristic.name }}</p>
                    <p class="text-sm font-light text-my-gray3">{{ characteristic.value }}</p>
                </div>
            </div>-->

            <p class="text-2xl font-bold text-my-gray3 mt-14">Seller</p>
            <div class="flex flex-col space-y-4 mt-5 bg-my-gray2 rounded-2xl w-full lg:w-fit p-8 border-0.5 border-my-gray2 hover:border-my-gray-2 hover:bg-my-black transition duration-500 cursor-pointer lg:hover:-translate-y-1">
                <router-link to="/profile">
                    <div class="w-16 h-16 md:w-24 md:h-24 overflow-hidden rounded-2xl">
                        <img :src="request.user.avatar" alt="Avatar" class="object-cover min-w-full min-h-full">
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-8">
                            <p class="text-base font-semibold text-my-gray3">{{ request.user.name }}</p>
                            <div class="flex items-center space-x-2">
                                <font-awesome-icon :icon="['fas', 'star']" class="text-base text-my-orange my-gradient-text" />
                                <p class="text-base font-light text-my-gray3 mt-0.5">{{ request.user.rating }}</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500">{{ request.user.auctions_count }} auctions held</p>
                        <p class="text-base font-light text-my-gray3 lg:w-96">{{ request.user.description }}</p>
                    </div>
                </router-link>
            </div>
        </div>
    </div>
</template>