<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import ButtonGradient from '@/Components/ButtonGradient.vue';
import ButtonArrow from '@/Components/ButtonArrow.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { Chart, BarController, LinearScale, CategoryScale, BarElement } from 'chart.js';

const props = defineProps({
    auction: {
    type: Object,
    required: true
  }
});

/*
const auction = { id: 1, 
    title: 'MacBook Pro 14', 
    description: 'The laptop is new. Got it from a reseller from Poland.', 
    category: 'Electronics, Laptops', 
    bids_count: '18', 
    max_bid: '340', 
    image: 'https://image.cnbcfm.com/api/v1/image/106452529-1584646955287macbook-air-2020-10.png?v=1584647237&w=929&h=523&vtcrop=y', 
    status: 'Active', 
    starting_price: 100 
}
*/
/*
const bids = [
    { user_name: 'Bob', bid_size: 100, date_time: '' },
    { user_name: 'Charles', bid_size: 110, date_time: '' },
    { user_name: 'Anna', bid_size: 200, date_time: '' },
    { user_name: 'Bob', bid_size: 260, date_time: '' },
    { user_name: 'George', bid_size: 300, date_time: '' },
    { user_name: 'Valera', bid_size: 340, date_time: '' },
]*/

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

const form = useForm({
    bid_size: '',
});

let submitForm = () => {
    let formData = new FormData();
    formData.append('bid_size', form.bid_size);

    axios.post(route('bids.store', props.auction.id), formData)
    .then((response) => {
        form.reset();
        form.clearErrors();
        window.location.href = '/lots/show/' + props.auction.id;
        //console.log(response.data);
    }).catch(error => {
        if (error.response) {
            console.error(error.response.data);
            console.error('2 ' + error.response.status);
            console.error('3 ' + error.response.headers);
        } else if (error.request) {
            console.error(error.request);
        } else {
            console.error('Error', error.message);
        }
    });
};

</script>

<template>
    <AuthenticatedLayout>
        <div class="flex justify-center">
            <div class="border-2 border-transparent rounded-2xl my-gradient-bord p-4 lg:p-12 text-my-gray4 lg:my-12 w-full lg:w-260 my-animation-in-up animation-md">
                <div class="text-my-gray4 text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-10">
                    Place a bid on 
                    <span class="my-gradient-text">{{ auction.lot.title }}</span>
                </div>

                <Link :href="route('lots.show', auction.id)">
                    <ButtonArrow text="See the lot" :colorsInversed="true" />
                </Link>

                <div class="space-y-6 mt-10">
                    <p class="font-light text-my-gray3 mb-4">
                        Auction status: 
                        <span :class="{
                        'text-green-400': auction.status === 'Active', 
                        'text-orange-400': auction.status === 'Finished', 
                        'text-red-400': auction.status === 'Failed'
                        }">{{ auction.status }}</span>
                    </p>
                    <p class="font-light text-my-gray3">Bids count: {{ auction.bids_count }}</p>
                    <p class="font-light text-my-gray3">Starting price: ${{ auction.lot.starting_price }}</p>
                    <p class="font-light text-my-gray3">Max bid: ${{ auction.bids_max_bid_size }}</p>
                    <div>
                        <Link :href="route('auctions.bids', auction.id)">
                            <p class="font-light text-my-gray3 tracking-widest underline hover:text-my-lila cursor-pointer duration-500 transition">See the bids</p>
                        </Link>
                    </div>

                    <div class="w-72 md:w-160 lg:w-full">
                        <canvas id="myChart" ref="chartContainer"></canvas>
                    </div>

                    <form @submit.prevent="submitForm" class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-12 md:items-end">
                        <div>
                            <InputLabel for="bid-size" value="Bid size, $" />

                            <TextInput
                                id="bid-size"
                                type="number"
                                class="mt-3 block w-64"
                                v-model="form.bid_size"
                                required
                                :colorsInversed="true"
                                :defaultValue="auction.lot.max_bid"
                            />

                            <InputError class="mt-2" :message="form.errors.bid_size" />
                        </div>

                        <ButtonGradient class="h-fit" text="Place a bid" />
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>