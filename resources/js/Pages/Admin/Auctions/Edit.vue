<script setup>
import ButtonArrow from '@/Components/ButtonArrow.vue';
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue'
import ManageForm from './Partials/ManageForm.vue';
import { onMounted, ref } from 'vue';
import { Chart, BarController, LinearScale, CategoryScale, BarElement } from 'chart.js';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    auction: {
    type: Object,
    required: true
  }
});

const bids = props.auction.bids;

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
        <div class="flex justify-center">
            <div class="space-y-12 my-animation-in-up animation-lg">
                <div class="border-2 border-transparent rounded-2xl my-gradient-bord p-4 lg:p-12 text-my-gray4 lg:my-12 w-full lg:w-260">
                    <div class="text-my-gray4 text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-10">
                        Manage
                        <span class="my-gradient-text">{{ auction.lot.title }}</span>
                    </div>

                    <Link :href="route('lots.show', auction.id)">
                        <ButtonArrow text="See the lot" :colorsInversed="true" />
                    </Link>

                    <ManageForm :auction="auction" />
                </div>

                <div class="border-2 border-transparent rounded-2xl my-gradient-bord p-4 lg:p-12 text-my-gray4 lg:my-12 w-full lg:w-260">
                    <div class="text-my-gray4 text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-10">
                        Statistics
                    </div>

                    <div class="space-y-6">
                        <p class="font-light text-my-gray3 mb-4">
                            Status: 
                            <span :class="{
                            'text-green-400': auction.status === 'Active', 
                            'text-orange-400': auction.status === 'Finished', 
                            'text-red-400': auction.status === 'Failed'
                            }">{{ auction.status }}</span>
                        </p>
                        <p class="font-light text-my-gray3">Bids count: {{ auction.bids_count }}</p>
                        <p class="font-light text-my-gray3">Max bid: ETH {{ auction.bids_max_bid_size }}</p>

                        <div class="w-72 md:w-160 lg:w-full">
                            <canvas id="myChart" ref="chartContainer"></canvas>
                        </div>

                        <Link :href="route('admin.auctions.bids', auction.id)">
                            <ButtonArrow text="See the bids" :colorsInversed="true" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>