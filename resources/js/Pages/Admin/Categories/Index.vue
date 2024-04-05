<script setup>
import ButtonWhite from '@/Components/ButtonWhite.vue';
import ButtonLila from '@/Components/ButtonLila.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue'
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps(['categories']);

const form = useForm({
    categories: props.categories,
});

/*
const categories2 = ref([
    { name: 'Real estate', subcategories: ['New buildings', 'Apartments', 'Rooms', 'Houses, dachas, cottages', 'Garages and parking lots', 'Sites', 'Commercial real estate'], showSubcategories: false },
    { name: 'Auto and spare parts', subcategories: ['Passenger cars', 'Spare parts', 'Trucks and buses', 'Motor vehicles', 'Agricultural machinery', 'Special equipment', 'Trailers', 'Water transport', 'Accessories', 'Tires, wheels', 'Tools, equipment'], showSubcategories: false },
]);

console.log(props.categories);
console.log(categories2.value);*/


const categories = ref(form.categories);

const updateCategoryName = (index, newName) => {
    categories.value[index].name = newName;
};

const updateSubcategoryName = (index, subIndex, newName) => {
    categories.value[index].subcategories[subIndex].name = newName;
};

const addCategory = () => {
    categories.value.push({ name: '', subcategories: [], showSubcategories: false });
};

const removeCategory = (index) => {
    categories.value.splice(index, 1);
};

const addSubcategory = (index) => {
    categories.value[index].subcategories.push({ name: '' });
};

const removeSubcategory = (index, subIndex) => {
    categories.value[index].subcategories.splice(subIndex, 1);
};

const toggleSubcategories = (index) => {
    categories.value[index].showSubcategories = !categories.value[index].showSubcategories;
};

let submitForm = () => {
    let formData = new FormData();
    form.categories.forEach((category, index) => {
        formData.append(`categories[${index}][id]`, category.id);
        formData.append(`categories[${index}][name]`, category.name);
        category.subcategories.forEach((subcategory, subIndex) => {
            formData.append(`categories[${index}][subcategories][${subIndex}][id]`, subcategory.id);
            formData.append(`categories[${index}][subcategories][${subIndex}][name]`, subcategory.name);
        });
    });

    axios.post(route('categories.store'), formData)
    .then((response) => {
        form.reset();
        form.clearErrors();
        window.location.href = '/admin/categories';
    }).catch(error => {
        console.error(error.response.data);
    });
};

</script>

<template>
    <AuthenticatedLayout>
        <div class="flex justify-center">
            <div class="border-2 border-transparent rounded-2xl my-gradient-bord p-4 lg:p-12 text-my-gray4 lg:my-12 w-full lg:w-260 my-animation-in-up animation-lg">
                <div class="text-my-gray4 text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-10">
                    Manage
                    <span class="my-gradient-text">Categories</span>
                </div>
                <form @submit.prevent="submitForm">
                    <div v-for="(category, index) in form.categories" :key="index">
                        <div class="flex flex-col md:flex-row justify-between items-center mt-6">
                            <div class="flex items-center space-x-3">
                                <TextInput
                                    type="text"
                                    @input="updateCategoryName(index, $event.target.value)"
                                    v-model="category.name"
                                    placeholder="Category" 
                                    class="block w-full md:w-84 font-bold"
                                    required
                                    :colorsInversed="true"
                                />
                                <span v-if="category.id" class="text-my-gray3">#{{ category.id }}</span>
                            </div>
                            <ButtonWhite type="button" @click="removeCategory(index)" class="mt-3 md:mt-0" text="Delete Category" />
                        </div>

                        <button @click="toggleSubcategories(index)" type="button" class="py-2 px-3 md:py-4 md:px-5 bg-my-black rounded-full text-my-gray3 text-lg md:text-xl mt-3 border border-my-black hover:border-my-lila transition duration-500" title="Show search form">
                            <font-awesome-icon :icon="['fas', category.showSubcategories ? 'chevron-up' : 'chevron-down']" />
                        </button>

                        <div v-if="category.showSubcategories" class="mt-3">
                            <div v-for="(subcategory, subIndex) in category.subcategories" :key="subIndex" class="flex flex-row items-center mt-1 space-x-3">
                                <TextInput
                                    type="text"
                                    @input="updateSubcategoryName(index, subIndex, $event.target.value)"
                                    v-model="subcategory.name"
                                    placeholder="Subcategory"
                                    class="block w-full md:w-84"
                                    required
                                    :colorsInversed="true"
                                />
                                <span v-if="subcategory.id" class="text-my-gray3">#{{ subcategory.id }}</span>
                                <button type="button" @click="removeSubcategory(index, subIndex)" class="text-2xl hover:text-my-lila tranition duration-500">×</button>
                            </div>
                        </div>
                        <ButtonWhite v-if="category.showSubcategories" type="button" @click="addSubcategory(index)" text="Add Subcategory" class="mt-3" />
                    </div>
                    <div class="mt-6 flex flex-col md:flex-row items-center md:space-x-3">
                        <ButtonWhite type="button" @click="addCategory" class="w-full" text="Add Category" />
                        <ButtonLila type="submit" class="mt-3 md:mt-0 w-full" text="Submit" />
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>