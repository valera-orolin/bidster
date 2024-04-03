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
import { onMounted, ref, watch } from 'vue';
import { Chart, BarController, LinearScale, CategoryScale, BarElement } from 'chart.js';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    auction: {
    type: Object,
    required: true
  }
});
console.log(props.auction);

const form = useForm({
    title: props.auction.lot.title,
    address: props.auction.lot.address,
    starting_price: props.auction.lot.starting_price,
    end_date: props.auction.lot.end_date,
    description: props.auction.lot.description,
    images: props.auction.lot.images,
    characteristics: props.auction.lot.characteristics,
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

/*
const categories = ref([
    { name: 'Real estate', subcategories: ['New buildings', 'Apartments', 'Rooms', 'Houses, dachas, cottages', 'Garages and parking lots', 'Sites', 'Commercial real estate'] },
    { name: 'Auto and spare parts', subcategories: ['Passenger cars', 'Spare parts', 'Trucks and buses', 'Motor vehicles', 'Agricultural machinery', 'Special equipment', 'Trailers', 'Water transport', 'Accessories', 'Tires, wheels', 'Tools, equipment'] },
]);

const defaultCategory = categories.value.find(category => category.name === lot.category);
const selectedCategory = ref(defaultCategory || categories.value[0]);
const defaultSubcategory = selectedCategory.value.subcategories.find(subcategory => subcategory === lot.subcategory);
const selectedSubcategory = ref(defaultSubcategory || '');
watch(selectedCategory, (newVal) => {
    selectedSubcategory.value = newVal.subcategories[0];
});*/
/*
let id = 0;
const addCharacteristic = () => {
    form.characteristics.push({ id: id++, name: '', value: '' });
};
const removeCharacteristic = (id) => {
    const index = form.characteristics.findIndex(c => c.id === id);
    if (index !== -1) {
        form.characteristics.splice(index, 1);
    }
};*/

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

const bids = [
    { user_name: 'Bob', bid_size: 100, date_time: '' },
    { user_name: 'Charles', bid_size: 110, date_time: '' },
    { user_name: 'Anna', bid_size: 200, date_time: '' },
    { user_name: 'Bob', bid_size: 260, date_time: '' },
    { user_name: 'George', bid_size: 300, date_time: '' },
    { user_name: 'Valera', bid_size: 340, date_time: '' },
]

Chart.register(BarController, LinearScale, CategoryScale, BarElement);
const chartContainer = ref(null);
onMounted(() => {
    const ctx = chartContainer.value.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: bids.map(bid => bid.user_name),
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
        formData.append('images[]', form.images[i]);
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
        console.error(error);
    });
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

                    <form @submit.prevent="submitForm" class="text-my-gray3 text-base mt-10">
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
                                <InputLabel for="starting-price" value="Starting price, $" />

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
                                    <ButtonWhite type="button" text="❮" @click="previousImage" />
                                    <ButtonWhite type="button" text="❯" @click="nextImage" />
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

                            <!---
                            <div class="flex flex-col space-y-2 lg:space-y-0 lg:flex-row lg:space-x-6">
                                <div>
                                    <InputLabel for="category" value="Category" />
                                    <select id="category" v-model="selectedCategory" required class="w-64 p-5 rounded-full transition duration-500 bg-my-black mt-3 focus:outline-none">
                                        <option v-for="category in categories" :value="category">{{ category.name }}</option>
                                    </select>
                                </div>

                                <div>
                                    <InputLabel for="subcategory" value="Subcategory" />
                                    <select id="subcategory" v-model="selectedSubcategory" required class="w-64 p-5 rounded-full transition duration-500 bg-my-black mt-3 focus:outline-none">
                                        <option v-for="subcategory in selectedCategory.subcategories" :value="subcategory">{{ subcategory }}</option>
                                    </select>
                                </div>
                            </div>-->

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
                        <p class="font-light text-my-gray3">Max bid: ${{ auction.max_bid }}</p>

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
                            <form @submit.prevent=""><ButtonWhite class="mt-10 w-full" text="Declare finish" /></form>
                            <form @submit.prevent=""><ButtonLila class="mt-10 w-full" text="Declare failure" /></form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showImageViewer" class="fixed inset-0 bg-my-black bg-opacity-50 flex items-center justify-center z-50">
            <img :src="auction.lot.images[currentImageIndex].image_path" class="max-h-screen max-w-screen" />
            <button class="absolute top-0 right-0 m-4 text-white text-5xl" @click="showImageViewer = false">×</button>
        </div>

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