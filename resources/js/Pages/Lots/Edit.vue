<script setup>
import ButtonArrow from '@/Components/ButtonArrow.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ButtonWhite from '@/Components/ButtonWhite.vue';
import ButtonLila from '@/Components/ButtonLila.vue';
import ButtonGradient from '@/Components/ButtonGradient.vue';
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import DangerButton from '@/Components/ButtonLila.vue';
import SecondaryButton from '@/Components/ButtonWhite.vue';
import Modal from '@/Components/Modal.vue';
import { onMounted, ref, watch } from 'vue';
import { Chart, BarController, LinearScale, CategoryScale, BarElement } from 'chart.js';
import { Link, useForm } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import axios from 'axios';

const props = defineProps(['auction', 'categories'])

const form = useForm({
    title: props.auction.lot.title,
    address: props.auction.lot.address,
    starting_price: props.auction.lot.starting_price,
    end_date: dayjs(props.auction.lot.end_date).format('YYYY-MM-DD'),
    description: props.auction.lot.description,
    images: props.auction.lot.images,
    characteristics: props.auction.lot.characteristics,
});

const form_contract = useForm({
    address: '',
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

const fileCount = ref(0);

const updateFileLabel = (event) => {
    form.images = event.target.files;
    fileCount.value = form.images.length;
};

let selectedCategory = ref('');
let selectedSubcategory = ref('');
if (props.auction && props.auction.lot) {
    selectedCategory.value = props.categories.find(category => category.subcategories.some(subcategory => subcategory.id === props.auction.lot.subcategory_id));
    selectedSubcategory.value = selectedCategory.value ? selectedCategory.value.subcategories.find(subcategory => subcategory.id === props.auction.lot.subcategory_id) : '';
}
watch(selectedCategory, (newVal) => {
    selectedSubcategory.value = newVal && newVal.subcategories[0] ? newVal.subcategories[0] : '';
});

const characteristics = ref(form.characteristics);
const addCharacteristic = () => {
    const id = Math.max(...characteristics.value.map(c => c.id)) + 1;
    characteristics.value.push({ id, name: '', value: '' });
};
const removeCharacteristic = (id) => {
    const index = characteristics.value.findIndex(c => c.id === id);
    if (index !== -1) {
        characteristics.value.splice(index, 1);
    }
};

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

let submitForm = () => {
    closeModalEdit();
    let formData = new FormData();
    formData.append('title', form.title);
    formData.append('address', form.address);
    formData.append('description', form.description);
    formData.append('end_date', form.end_date);
    formData.append('starting_price', form.starting_price);
    for (let i = 0; i < form.characteristics.length; i++) {
        formData.append(`characteristics[${i}][name]`, form.characteristics[i].name);
        formData.append(`characteristics[${i}][value]`, form.characteristics[i].value);
    }
    for (let i = 0; i < form.images.length; i++) {
        if (form.images[i].image_path) {
            formData.append('images[]', form.images[i].image_path);
        } else {
            formData.append('images[]', form.images[i]);
        }
    }
    if (selectedSubcategory.value) {
        formData.append('subcategory_id', selectedSubcategory.value.id);
    }
    formData.append('_method', 'PUT');

    axios.post(route('lots.update', props.auction.id), formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        },
    }).then((response) => {
        form.reset();
        form.clearErrors();
        window.location.href = '/auctions';
    }).catch(error => {
        if (error.response && error.response.data.errors) {
            for (let field in error.response.data.errors) {
                form.errors[field] = error.response.data.errors[field][0];
            }
        }
        console.error(error.response.data);
    });
};

let failError = ref('');
const submitFormFailure = () => {
    closeModalFailure();
    let formData = new FormData();
    formData.append('address', form_contract.address);

    axios.post(route('auctions.declare-failure', props.auction.id), formData)
    .then(() => {
        window.location.href = '/auctions';
    }).catch(error => {
        failError.value = error.response.data;
    });
}

let finishError = ref('');
const submitFormFinish = () => {
    closeModalFinish();
    let formData = new FormData();
    formData.append('address', form_contract.address);

    axios.post(route('auctions.declare-finish', props.auction.id), formData)
    .then(() => {
        window.location.href = '/auctions';
    }).catch(error => {
        finishError.value = error.response.data;
    });
}

let publishError = ref('');
const submitPublishingContract = () => {
    closeModalPublishingContract();
    let formData = new FormData();
    formData.append('address', form_contract.address);

    axios.post(route('auctions.publish-contract', props.auction.id), formData)
    .then((response) => {
        form_contract.reset();
        form_contract.clearErrors();
        window.location.href = '/auctions';
    }).catch(error => {
        publishError.value = error.response.data;
    });
}

const confirmingSubmissionEdit = ref(false);
const confirmSubmissionEdit = () => {
    confirmingSubmissionEdit.value = true;
};
const closeModalEdit = () => {
    confirmingSubmissionEdit.value = false;
};

const confirmingSubmissionFailure = ref(false);
const confirmSubmissionFailure = () => {
    confirmingSubmissionFailure.value = true;
};
const closeModalFailure = () => {
    confirmingSubmissionFailure.value = false;
};

const confirmingSubmissionFinish = ref(false);
const confirmSubmissionFinish = () => {
    confirmingSubmissionFinish.value = true;
};
const closeModalFinish = () => {
    confirmingSubmissionFinish.value = false;
};

const confirmingPublishingContract = ref(false);
const confirmPublishingContract = () => {
    confirmingPublishingContract.value = true;
};
const closeModalPublishingContract = () => {
    confirmingPublishingContract.value = false;
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="flex justify-center">
            <div class="space-y-12 my-animation-in-up animation-lg">
                <div class="border-2 border-transparent rounded-2xl my-gradient-bord p-4 lg:p-12 text-my-gray4 lg:my-12 w-full lg:w-260">
                    <div class="text-my-gray4 text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-10">
                        Edit
                        <span class="my-gradient-text">{{ auction.lot.title }}</span>
                    </div>

                    <Link :href="route('lots.show', auction.id)">
                        <ButtonArrow text="See the lot" :colorsInversed="true" />
                    </Link>

                    <form @submit.prevent="confirmSubmissionEdit" class="text-my-gray3 text-base mt-10">
                        <div class="space-y-6">
                            <div>
                                <InputLabel for="title" value="Title" />

                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-3 block w-full"
                                    v-model="form.title"
                                    required
                                    autofocus
                                    :colorsInversed="true"
                                />

                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <div>
                                <InputLabel for="address" value="Address" />

                                <TextInput
                                    id="address"
                                    type="text"
                                    class="mt-3 block w-full"
                                    v-model="form.address"
                                    required
                                    :colorsInversed="true"
                                />

                                <InputError class="mt-2" :message="form.errors.address" />
                            </div>

                            <div>
                                <InputLabel for="starting-price" value="Starting price, ETH " />

                                <TextInput
                                    id="starting-price"
                                    type="number"
                                    class="mt-3 block w-64"
                                    v-model="form.starting_price"
                                    required
                                    :colorsInversed="true"
                                />

                                <InputError class="mt-2" :message="form.errors.starting_price" />
                            </div>

                            <div class="flex flex-col items-start w-full lg:w-160">
                                <img v-if="auction.lot.images[currentImageIndex]" :src="auction.lot.images[currentImageIndex].image_path" alt="Lot image" class="h-56 md:h-72 lg:h-112 object-cover rounded-2xl cursor-zoom-in" @click="openImage" />
                                <div v-else class="w-full h-56 md:h-72 lg:w-160 lg:h-112 object-cover rounded-2xl bg-my-black">
                                    <img src="/images/icon.svg" alt="Lot image" class="h-24 md:h-32 m-8" />
                                </div>
                                <div class="flex justify-between w-full mt-4">
                                    <ButtonWhite :disabled="auction.lot.images.length < 2" type="button" text="❮" @click="previousImage" />
                                    <ButtonWhite :disabled="auction.lot.images.length < 2" type="button" text="❯" @click="nextImage" />
                                </div>
                            </div>

                            <div>
                                <InputLabel for="images" value="Images" />

                                <div class="file-upload mt-3 flex flex-col md:flex-row md:items-center">
                                    <input type="file" id="images" class="file-input hidden" accept="image/*" multiple @change="updateFileLabel">
                                    <label for="images" class="file-label bg-my-black p-5 rounded-full cursor-pointer w-fit">Select images</label>
                                    <span class="file-count-label mt-2 md:mt-0 md:ml-6 text-my-gray3">Images selected: {{ fileCount }}</span>
                                </div>

                                <InputError class="mt-2" :message="form.errors.images" />
                            </div>

                            <div>
                                <InputLabel for="end-date" value="End date" />

                                <TextInput
                                    id="end-date"
                                    type="date"
                                    class="mt-3 block w-64"
                                    v-model="form.end_date"
                                    required
                                    :colorsInversed="true"
                                />

                                <InputError class="mt-2" :message="form.errors.end_date" />
                            </div>

                            <div>
                                <InputLabel for="description" value="Description"   />

                                <TextArea
                                    id="description"
                                    type="date"
                                    class="mt-3 block w-full"
                                    maxlength="1000"
                                    rows="5"
                                    v-model="form.description"
                                    required
                                    :colorsInversed="true"
                                    :defaultValue="form.description"
                                />

                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div class="flex flex-col space-y-2 lg:space-y-0 lg:flex-row lg:space-x-6">
                                <div>
                                    <InputLabel for="category" value="Category" />
                                    <select id="category" v-model="selectedCategory" class="w-64 p-5 rounded-full transition duration-500 bg-my-black border-my-black focus:border-my-black focus:outline-none focus:ring-0.5 focus:ring-my-black mt-3">
                                        <option v-for="category in categories" :value="category">{{ category.name }}</option>
                                    </select>
                                </div>

                                <div>
                                    <InputLabel for="subcategory" value="Subcategory" />
                                    <select id="subcategory" v-model="selectedSubcategory" class="w-64 p-5 rounded-full transition duration-500 bg-my-black border-my-black focus:border-my-black focus:outline-none focus:ring-0.5 focus:ring-my-black mt-3">
                                        <option v-for="subcategory in selectedCategory.subcategories" :value="subcategory">{{ subcategory.name }}</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <InputLabel for="characteristics" value="Characteristics" />
                                <div class="mt-3 flex flex-col space-y-2 lg:space-y-0 lg:flex-row lg:items-center lg:space-x-4 items-start" v-for="characteristic in form.characteristics" :key="characteristic.id">
                                    <TextInput
                                        type="text"
                                        class="w-64 md:w-96"
                                        v-model="characteristic.name" 
                                        placeholder="Name" 
                                        required
                                        :colorsInversed="true"
                                        :defaultValue="characteristic.name"
                                    />
                                    <TextInput
                                        type="text"
                                        class="w-64 md:w-96"
                                        v-model="characteristic.value" 
                                        placeholder="Value"
                                        required
                                        :colorsInversed="true"
                                        :defaultValue="characteristic.value"
                                    />
                                    <button type="button" @click="removeCharacteristic(characteristic.id)" class="text-4xl hover:text-my-lila transition duration-500">×</button>
                                </div>
                                <ButtonWhite type="button" @click="addCharacteristic" class="mt-3" text="Add characteristic"/>
                            </div>
                        </div>
                        <ButtonGradient class="mt-10" :text="'Edit auction'" />
                    </form>

                    <Modal :show="confirmingSubmissionEdit" @close="closeModalEdit">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-my-gray3">
                                Are you sure you want to edit this auction?
                            </h2>

                            <p class="mt-1 text-sm text-my-gray4">
                                A request will be created to edit the auction, which can be approved or rejected by the Bidster administration.
                            </p>

                            <div class="mt-6 flex justify-end">
                                <SecondaryButton @click="closeModalEdit" text="Cancel" />
                                <DangerButton class="ms-3" @click="submitForm" text="Submit" />
                            </div>
                        </div>
                    </Modal>
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

                        <Link :href="route('auctions.bids', auction.id)"> <!-- TODO -->
                            <ButtonArrow text="See the bids" :colorsInversed="true" />
                        </Link>
                    </div>
                </div>

                <div class="border-2 border-transparent rounded-2xl my-gradient-bord p-4 lg:p-12 text-my-gray4 lg:my-12 w-full lg:w-260 my-animation-in-up">
                    <div class="text-my-gray4 text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-10">
                        End the auction
                    </div>

                    <div class="space-x-6">

                        <div class="text-my-gray3 text-base md:space-x-6 flex flex-col md:flex-row">
                            <ButtonWhite class="mt-10" text="Declare finish" @click="confirmSubmissionFinish" />
                            <ButtonLila class="mt-10" text="Declare failure" @click="confirmSubmissionFailure"/>
                        </div>
                        <InputError class="mt-2" :message=finishError />
                        <InputError class="mt-2" :message=failError />
                    </div>
                </div>

                <div v-if="!auction.contract_id" class="border-2 border-transparent rounded-2xl my-gradient-bord p-4 lg:p-12 text-my-gray4 lg:my-12 w-full lg:w-260 my-animation-in-up">
                    <div class="text-my-gray4 text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-10">
                        Publish contract
                    </div>

                    <div class="space-x-6">

                        <div class="text-my-gray3 text-base md:space-x-6 flex flex-col md:flex-row">
                            <ButtonWhite class="mt-10" text="Publish contract" @click="confirmPublishingContract" />
                        </div>
                        <InputError class="mt-2" :message=publishError />
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showImageViewer" class="fixed inset-0 bg-my-black bg-opacity-50 flex items-center justify-center z-50">
            <img :src="auction.lot.images[currentImageIndex].image_path" class="max-h-screen max-w-screen" />
            <button class="absolute top-0 right-0 m-4 text-white text-5xl" @click="showImageViewer = false">×</button>
        </div>

        <Modal :show="confirmingPublishingContract" @close="closeModalPublishingContract">
            <div class="p-6">
                <h2 class="text-lg font-medium text-my-gray3">
                    Are you sure you want to publish contract for this auction?
                </h2>

                <p class="mt-1 text-sm text-my-gray4">
                    The auction will be able to receive bids then.
                </p>

                <div class="mt-6">
                    <InputLabel for="account_address" value="Account address" class="sr-only" />

                    <TextInput
                        id="account_address"
                        ref="accountAddress"
                        v-model="form_contract.address"
                        type="text"
                        class="mt-1 block w-3/4 text-my-gray4"
                        placeholder="Account address"
                        required
                        :colorsInversed="true"
                        @keyup.enter="submitPublishingContract"
                    />

                    <InputError :message="form_contract.errors.address" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModalPublishingContract" text="Cancel" />
                    <DangerButton class="ms-3" @click="submitPublishingContract" text="Submit" />
                </div>
            </div>
        </Modal>

        <Modal :show="confirmingSubmissionFinish" @close="closeModalFinish">
            <div class="p-6">
                <h2 class="text-lg font-medium text-my-gray3">
                    Are you sure you want to declrare finish of this auction?
                </h2>

                <p class="mt-1 text-sm text-my-gray4">
                    The auction will be given the status 'Finished'. The lot goes to the user with the highest bid.
                </p>

                <div class="mt-6">
                    <InputLabel for="account_address" value="Account address" class="sr-only" />

                    <TextInput
                        id="account_address"
                        ref="accountAddress"
                        v-model="form_contract.address"
                        type="text"
                        class="mt-1 block w-3/4 text-my-gray4"
                        placeholder="Account address"
                        :colorsInversed="true"
                        @keyup.enter="submitFormFailure"
                    />

                    <InputError :message="form_contract.errors.address" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModalFinish" text="Cancel" />
                    <DangerButton class="ms-3" @click="submitFormFinish" text="Submit" />
                </div>
            </div>
        </Modal>

        <Modal :show="confirmingSubmissionFailure" @close="closeModalFailure">
            <div class="p-6">
                <h2 class="text-lg font-medium text-my-gray3">
                    Are you sure you want to declrare failure of this auction?
                </h2>

                <p class="mt-1 text-sm text-my-gray4">
                    The auction will be given the status 'Failed'. All bids will be refunded.
                </p>

                <div class="mt-6">
                    <InputLabel for="account_address" value="Account address" class="sr-only" />

                    <TextInput
                        id="account_address"
                        ref="accountAddress"
                        v-model="form_contract.address"
                        type="text"
                        class="mt-1 block w-3/4 text-my-gray4"
                        placeholder="Account address"
                        :colorsInversed="true"
                        @keyup.enter="submitFormFailure"
                    />

                    <InputError :message="form_contract.errors.address" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModalFailure" text="Cancel" />
                    <DangerButton class="ms-3" @click="submitFormFailure" text="Submit" />
                </div>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>

<style>
.file-upload input[type="file"] {
  display: none;
}

input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
}
</style>