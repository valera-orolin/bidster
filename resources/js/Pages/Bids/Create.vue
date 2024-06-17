<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import ButtonGradient from '@/Components/ButtonGradient.vue';
import ButtonArrow from '@/Components/ButtonArrow.vue';
import DangerButton from '@/Components/ButtonLila.vue';
import SecondaryButton from '@/Components/ButtonWhite.vue';
import Modal from '@/Components/Modal.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { Chart, BarController, LinearScale, CategoryScale, BarElement } from 'chart.js';

const props = defineProps(['auction', 'min_bid_size']);

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
    bid_size: props.min_bid_size,
    address: '',
});

const errorMessage = ref('');

let submitForm = () => {
    closeModal();

    let formData = new FormData();
    formData.append('bid_size', form.bid_size);
    formData.append('address', form.address);

    axios.post(route('bids.store', props.auction.id), formData)
    .then((response) => {
        form.reset();
        form.clearErrors();
        window.location.href = '/lots/show/' + props.auction.id;
        //console.log(response.data);
    }).catch(error => {
        errorMessage.value = error.response.data;
    });
};

const confirmingSubmission = ref(false);
const confirmSubmission = () => {
    confirmingSubmission.value = true;
};
const closeModal = () => {
    confirmingSubmission.value = false;
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
                    <p class="font-light text-my-gray3">Starting price: ETH {{ auction.lot.starting_price }}</p>
                    <p class="font-light text-my-gray3">Max bid: ETH {{ auction.bids_max_bid_size }}</p>
                    <div>
                        <Link :href="route('auctions.bids', auction.id)">
                            <p class="font-light text-my-gray3 tracking-widest underline hover:text-my-lila cursor-pointer duration-500 transition">See the bids</p>
                        </Link>
                    </div>

                    <div class="w-72 md:w-160 lg:w-full">
                        <canvas id="myChart" ref="chartContainer"></canvas>
                    </div>

                    <div>
                        <form @submit.prevent="confirmSubmission" class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-12 md:items-end">
                            <div>
                                <InputLabel for="bid-size" value="Bid size, ETH " />

                                <TextInput
                                    id="bid-size"
                                    type="number"
                                    class="mt-3 block w-64"
                                    v-model="form.bid_size"
                                    required
                                    :colorsInversed="true"
                                />

                                <InputError class="mt-2" :message="form.errors.bid_size" />
                            </div>

                            <ButtonGradient class="h-fit" text="Place a bid" />
                        </form>

                        <InputError class="mt-2" :message=errorMessage />
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="confirmingSubmission" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-my-gray3">
                    Are you sure you want to place a bid?
                </h2>

                <p class="mt-1 text-sm text-my-gray4">
                    No refund will be given if the auction is deemed finished and your bid wins. Gas payments will not be refunded.
                </p>

                <div class="mt-6">
                    <InputLabel for="account_address" value="Account address" class="sr-only" />

                    <TextInput
                        id="account_address"
                        ref="accountAddress"
                        v-model="form.address"
                        type="text"
                        class="mt-1 block w-3/4 text-my-gray4"
                        placeholder="Account address"
                        required
                        :colorsInversed="true"
                        @keyup.enter="submitForm"
                    />

                    <InputError :message="form.errors.address" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal" text="Cancel" />
                    <DangerButton class="ms-3" @click="submitForm" text="Submit" />
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>