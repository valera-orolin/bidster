<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Auction from '@/Pages/Profile/Partials/Auction.vue';
import Bid from '@/Pages/Bids/Partials/Bid.vue';
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ButtonWhite from '@/Components/ButtonWhite.vue';

const props = defineProps(['user', 'auctions', 'bids']);

const selected = ref(localStorage.getItem('selected') || 'option1');

const changePage = (url, type) => {
    localStorage.setItem('selected', selected.value);
    window.history.replaceState({}, '', url);
    location.reload();
}

const currentUser = usePage().props.auth.user;

const isCurrentUser = ref(props.user.id == currentUser.id);
</script>

<template>
    <AuthenticatedLayout>
        <div class="my-animation-in-up animation-lg">
            <div class="flex justify-center">
                <div class="flex flex-col lg:flex-row space-y-6 space-x-0 lg:space-y-0 lg:space-x-10 lg:items-center my-gradient-bord-black border-2 border-transparent rounded-2xl p-4 lg:p-12 text-my-gray4"> 
                    <div class="w-36 h-36 md:w-60 md:h-60 lg:w-80 lg:h-80 overflow-hidden rounded-2xl">
                        <img v-if="user.avatar" :src="user.avatar" alt="Avatar" class="object-cover min-w-full min-h-full">
                        <img v-else src="/images/icon.svg" alt="Avatar" class="min-w-full min-h-full">
                    </div>
                    <div class="space-y-3 lg:w-140 tracking-widest overflow-auto break-words">
                        <p class="text-3xl md:text-5xl font-semibold text-my-gray3">{{ user.name }}</p>
                        <!---
                        <div class="flex items-center space-x-2">
                            <font-awesome-icon :icon="['fas', 'star']" class="text-base text-my-orange my-gradient-text" />
                            <p class="text-base font-light text-my-gray3 mt-0.5">{{ user.rating }}</p>
                        </div>-->
                        <p class="text-base font-light text-my-gray3">{{ user.description }}</p>
                        <div class="flex flex-row w-full justify-between">
                            <p class="text-sm text-gray-500">{{ user.auctions_count }} auctions held</p>
                            <Link v-if="isCurrentUser" :href="route('profile.edit')">
                                <font-awesome-icon class="cursor-pointer hover:text-my-lila duration-500 transition text-xl" :icon="['fas', 'gear']" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center space-x-8 mt-16">
                <button :class="selected === 'option1' ? 'bg-my-lila text-my-black' : 'bg-my-gray2 text-my-gray4 border border-my-gray3'" class="hover:bg-my-lila hover:text-my-gray2 py-4 px-12 rounded-2xl transition duration-500" @click="selected = 'option1'">Auctions</button>
                <button :class="selected === 'option2' ? 'bg-my-lila text-my-black' : 'bg-my-gray2 text-my-gray4 border border-my-gray3'" class="hover:bg-my-lila hover:text-my-gray2 py-4 px-12 rounded-2xl transition duration-500" @click="selected = 'option2'">Bids</button>
            </div>
        </div>

        <div v-if="selected === 'option1'" class="flex flex-col space-y-4 md:space-y-16">        
            <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-4 mt-16">
                <Auction v-for="auction in auctions.data" :key="auction.id" :auction="auction" class="my-animation-in-up" />
            </div>
            
            <div v-if="auctions.last_page > 1" class="flex items-center justify-center space-x-4">
                <ButtonWhite :disabled="!auctions.prev_page_url" @click="changePage(auctions.first_page_url, 'auctions')"><font-awesome-icon :icon="['fas', 'arrow-up']" /></ButtonWhite>

                <ButtonWhite :disabled="!auctions.prev_page_url" @click="changePage(auctions.prev_page_url, 'auctions')"><font-awesome-icon :icon="['fas', 'arrow-left']" /></ButtonWhite>

                <ButtonWhite :disabled="!auctions.next_page_url" @click="changePage(auctions.next_page_url, 'auctions')"><font-awesome-icon :icon="['fas', 'arrow-right']" /></ButtonWhite>
            </div>
        </div>

        <div v-else class="flex flex-col space-y-4 md:space-y-16">   
            <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-4 mt-16">
                <Bid v-for="bid in bids.data" :key="bid.id" :bid="bid" class="my-animation-in-up" />
            </div>
            
            <div v-if="bids.last_page > 1" class="flex items-center justify-center space-x-4">
                <ButtonWhite :disabled="!bids.prev_page_url" @click="changePage(bids.first_page_url, 'bids')"><font-awesome-icon :icon="['fas', 'arrow-up']" /></ButtonWhite>

                <ButtonWhite :disabled="!bids.prev_page_url" @click="changePage(bids.prev_page_url, 'bids')"><font-awesome-icon :icon="['fas', 'arrow-left']" /></ButtonWhite>

                <ButtonWhite :disabled="!bids.next_page_url" @click="changePage(bids.next_page_url, 'bids')"><font-awesome-icon :icon="['fas', 'arrow-right']" /></ButtonWhite>
            </div>
        </div>
    </AuthenticatedLayout>
</template>