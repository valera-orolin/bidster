<script setup>
import ButtonGradient from '@/Components/ButtonGradient.vue';
import ButtonArrow from '@/Components/ButtonArrow.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { onMounted, ref } from 'vue';
import { Chart, BarController, LinearScale, CategoryScale, BarElement } from 'chart.js';
import { Link } from '@inertiajs/vue3';
import dayjs from 'dayjs';

const props = defineProps(['bid', 'bids', 'max_bid_size']);

const bids = props.bids;

Chart.register(BarController, LinearScale, CategoryScale, BarElement);
const chartContainer = ref(null);
onMounted(() => {
    const ctx = chartContainer.value.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: bids.map(bid => bid.user.name),
            datasets: [{
                label: 'Bid size',
                data: bids.map(bid => bid.bid_size),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="my-animation-in-up animation-lg">
            <div class="flex flex-col lg:flex-row">
                <div class="flex flex-col items-start">
                    <img v-if="bid.auction.lot.images[0]" :src="bid.auction.lot.images[0].image_path" alt="Lot image" class="w-full h-56 md:h-72 lg:w-200 lg:h-112 object-cover rounded-2xl" />
                    <div v-else class="w-full h-56 md:h-72 lg:w-200 lg:h-112 object-cover rounded-2xl bg-my-gray2">
                        <img src="/images/icon.svg" alt="Lot image" class="h-24 md:h-32 m-8" />
                    </div>
                </div>

                <div class="flex flex-col text-my-gray4 mt-5 lg:mt-0 lg:ml-10">
                    <h2 class="text-2xl lg:text-4xl font-bold">{{ bid.auction.lot.title }}</h2>

                    <p class="text-base font-light text-my-gray3 mt-3">Starting: {{ dayjs(bid.auction.created_at).format('MMMM D, YYYY h:mm A') }}</p>
                    <p class="text-base font-light text-my-gray3 mt-1">Ending: {{ dayjs(bid.auction.lot.end_date).format('MMMM D, YYYY h:mm A') }}</p>
                    <p class="text-base font-light text-my-gray3 mt-1">Bid time: {{ dayjs(bid.created_at).format('MMMM D, YYYY h:mm:ss A') }}</p>

                    <p class="text-base font-light text-my-gray3 mt-3">
                        Auction status: 
                        <span :class="{
                        'text-green-400': bid.auction.status === 'Active', 
                        'text-orange-400': bid.auction.status === 'Finished', 
                        'text-red-400': bid.auction.status === 'Failed'
                        }">{{ bid.auction.status }}</span>
                    </p>

                    <p class="text-base font-light text-my-gray3 mt-3">Bid size: <span class="bg-my-violet py-1 px-2 rounded-xl font-normal">ETH {{ bid.bid_size }}</span></p>

                    <p v-if="bid.bid_size === max_bid_size" class="text-base font-light text-my-gray3 mt-3">This bid is <span class="text-green-400">leading</span></p>
                    <p v-else class="text-base font-light text-my-gray3 mt-3">This bid is <span class="text-red-400">not leading</span></p>

                    <div class="flex flex-col md:flex-row md:space-x-10 items-center w-fit">
                        <Link v-if="bid.auction.status == 'Active'" :href="route('bids.create', bid.auction.id)">
                            <ButtonGradient class="mt-8 w-56" text="Place a bid" />
                        </Link>

                        <Link :href="route('lots.show', bid.auction.id)">
                            <ButtonArrow class="mt-8 w-56" text="See the lot" />
                        </Link>
                    </div>
                </div>
            </div>

            <div class="flex flex-col">
                <p class="text-2xl font-bold text-my-gray3 mt-14">Bidder</p>
                <div class="flex flex-col space-y-4 mt-5 bg-my-gray2 rounded-2xl w-full lg:w-fit p-8 border-0.5 border-my-gray2 hover:border-my-gray-2 hover:bg-my-black transition duration-500 cursor-pointer lg:hover:-translate-y-1">
                    <Link :href="route('profile.show', bid.user.id)">
                        <div class="w-16 h-16 md:w-24 md:h-24 overflow-hidden rounded-2xl">
                            <img v-if="bid.user.avatar" :src="bid.user.avatar" alt="Avatar" class="object-cover min-w-full min-h-full">
                            <img v-else src="/images/icon.svg" alt="Avatar" class="min-w-full min-h-full">
                        </div>
                        <div class="space-y-3 mt-3">
                            <div class="flex items-center space-x-8">
                                <p class="text-base font-semibold text-my-gray3">{{ bid.user.name }}</p>
                            </div>
                            <p class="text-sm text-gray-500">{{ bid.user.auctions_count }} auctions held</p>
                            <p class="text-base font-light text-my-gray3 lg:w-96">{{ bid.user.description }}</p>
                        </div>
                    </Link>
                </div>

                <p class="text-2xl font-bold text-my-gray3 mt-14">Statistics</p>
                <div class="w-80 md:w-160 lg:w-full lg:max-h-160 mt-10">
                    <canvas id="myChart" ref="chartContainer"></canvas>
                </div>
            </div>

        </div>

    </AuthenticatedLayout>
</template>