<script setup>
import { ref, onMounted, onUnmounted, watch, watchEffect, computed } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import Nav from "@/Layouts/Nav.vue";
import { initFlowbite } from "flowbite";
import { Link, useForm, router, usePage } from "@inertiajs/vue3";
import { store, useCartStore, state } from "../../store.js";
import axios from "axios";
import { useIntersectionObserver } from "@vueuse/core";
import { debounce } from "lodash";

const showingNavigationDropdown = ref(false);

// onMounted(() => {
//     const windSterScript = document.createElement("link");
//     windSterScript.setAttribute(
//         "href",
//         "https://unpkg.com/tailwindcss@2.2.19/dist/tailwind.min.css"
//     );
//     windSterScript.setAttribute("async", "true");
//     windSterScript.setAttribute("defer", "true");
//     document.head.appendChild(windSterScript);
// });

const props = defineProps({
    nextPageUrl: {
        type: [String],
    },
    products: {
        type: [Array, Object],
    },
    hearts: {
        type: [Array],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    brands: {
        type: Array,
        default: () => [],
    },
    cartCountItems: {
        type: Number,
    },

    cartItems: {
        type: [Object, Array],
    },

    initialHearted: {
        type: Boolean,
        default: false,
    },
});

const isHearted = ref(props.hearts);

const searchValue = ref(usePage().props.searchTextFilter),
    pageNumber = ref(1);

const isDisableShake = ref(false);

const more_products = ref(props.products);

const prods = ref(props.products);

const page = ref(1);
const loading = ref(false);
const noMoreData = ref(false);

const productId = ref([]);

const isButtonFilterClick = ref(false);

const filterCategoryName = ref([]);
const filterBrandName = ref([]);

var form = useForm({
    id: null,
    name: "",
    description: "",
    price: 0.0,
    quantity: 0,
    image: [],
    category_name: null,
    brand_name: null,
});

onMounted(() => {
    initFlowbite();

    console.log("OnMounted");

    window.addEventListener("scroll", handleScroll);
});

onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll);
});

async function toggleHeart(productId) {
    try {
        const response = await axios.post(`/products/${productId}/heart`);

        if (isHearted.value.includes(productId)) {
            isHearted.value = isHearted.value.filter(
                (number) => number !== productId
            );
        } else {
            isHearted.value.push(productId);
        }
        console.log("Ishearted", isHearted.value);
    } catch (error) {
        console.error(error);
    }
}

let productUrl = computed(() => {
    let url = new URL(route("home"));

    url.searchParams.append("page", pageNumber.value);

    if (searchValue.value) {
        url.searchParams.append("searchValue", searchValue.value);
        console.log("not empty ");
    } else {
        page.value = 1;
        props.nextPageUrl = url;
        url = new URL(route("home"));

        console.log("empty search value");
    }

    return url;
});
watch(
    () => productUrl.value,
    (newValue) => {
        debounce(
            () => productUrl.value,

            2000
        );
        console.log(newValue);

        //Reset the checkbox
        filterCategoryName.value = [];
        filterBrandName.value = [];

        router.visit(newValue, {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    }
);

const handleScroll = () => {
    let bottomOfWindow =
        document.documentElement.scrollTop + window.innerHeight ===
        document.documentElement.offsetHeight;

    if (bottomOfWindow) {
        loadMoreProducts();
    }
};

const loadMoreProducts = async () => {
    loading.value = true;

    console.log("No More Data", props.nextPageUrl);

    try {
        if (props.nextPageUrl != null) {
            page.value += 1;
            const response = await axios.get(`/home?page=${page.value}`);
            console.log("Response Data", response.data);

            props.products.data.push(...response.data.data);
            loading.value = false;

            if (response.data.next_page_url == null) {
                console.log("No More Data");

                noMoreData.value = true;
                loading.value = false;
                return;
            } else {
                props.nextPageUrl = response.data.next_page_url;
                noMoreData.value = false;
            }
        }
    } catch (error) {
        console.error(error);
        loading.value = false;
    }
};
const cartIt = ref([state.cartItems]);
const itemCartCount = ref(1);

watchEffect(() => {
    console.log("JsonArray" + state.cartItems);
});

function isAddedToCart(productId) {
    // Object.entries(props.cartItems).forEach(([key, val]) => {
    //     cartIt.push(val.id);
    // });
    //cartIt.value.push(productId);

    return state.cartItems.includes(productId);
}

watch(
    () => isDisableShake.value,
    (newValue) => {
        console.log(newValue);
        isDisableShake.value = newValue;

        console.log("isDisableShakes" + isDisableShake.value);
    }
);

// watch(isDisableShake, (newValue, oldValue) => {
//     console.log(`isShasake changed from ${oldValue} to ${newValue}`);

// });

const {
    cartCounts,
    cartItems,
    incrementCartCount,
    setCartCount,
    setCartItems,
} = useCartStore();

let integerArray = [];
let tempIntegerArray = [];

async function addToCart(product) {
    form.id = product.id;
    form.description = product.description;
    form.price = product.price;
    form.image = product.image;
    form.quantity = product.quantity;
    form.category_name = product.category.name;
    form.brand_name = product.brand.name;

    await axios
        .get(`/cart/add/${form.id}`, form, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        })
        .then((response) => {
            setCartCount();
            setCartItems(form.id);
            isDisableShake.value = true;

            console.log(
                "Success:",
                response,
                "isDisableShake",
                isDisableShake.value
            );
        })
        .catch((error) => {
            // Handle error
            console.error("Error:", error);
        });

    // form.get(`/cart/add/${form.id}`, {
    //     preserveScroll: true,
    //     //onBefore: () => confirm("Are you sure you want to delete this user?"),
    //     onError: () => {},
    //     onSuccess: () => {
    //         console.log("Success");
    //     },
    // });
}

const filterCat = [];
const filterBran = [];
let url = new URL(route("home"));
function routeFilterCategory(param, value) {
    url.searchParams.append(param, value);

    router.visit(url, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
    if (filterCat.length === 0) {
        page.value = 1;
        noMoreData.value = false;
        loading.value = false;

        loadMoreProducts();
    }
}
function routeFilterBrand(param, value) {
    url.searchParams.append(param, value);

    router.visit(url, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });

    if (filterBran.length === 0) {
        console.log("dad");
        page.value = 1;
        noMoreData.value = false;
        loading.value = false;
        loadMoreProducts();
    } else {
        console.log("dads");
    }
}

function filterCategory(e) {
    let filterName = "";

    if (e.target.checked) {
        filterName = e.target.value;

        filterCat.push(filterName);
        console.log("filter" + filterCat);

        filterCategoryName.value = filterCat;

        routeFilterCategory("filterCategoryName", filterCategoryName.value);
    }
    if (e.target.checked == false) {
        filterName = e.target.value;

        const index = filterCat.indexOf(filterName);

        // If the category is already selected, remove it

        if (index > -1) {
            // If the category is already selected, remove it
            filterCat.splice(index, 1);
        }
        filterCategoryName.value = filterCat;

        routeFilterCategory("filterCategoryName", filterCategoryName.value);
    }
}

function filterBrand(e) {
    let filterName = "";

    if (e.target.checked) {
        filterName = e.target.value;

        filterBran.push(filterName);
        console.log("filter brand" + filterBran);

        routeFilterBrand("filterBrandName", filterBran);
    } else {
        filterName = e.target.value;

        const index = filterBran.indexOf(filterName);

        if (index > -1) {
            filterBran.splice(index, 1);
        }
        console.log("filter brand" + filterBran);

        routeFilterBrand("filterBrandName", filterBran);
    }
}
</script>

<style scoped></style>

<template>
    <body
        class="bg-white text-gray-600 work-sans leading-normal text-base tracking-normal"
    >
        <!--Nav-->

        <Nav :isDisableShake="isDisableShake">
            <div
                id="carouselExample"
                class="relative w-full px-12"
                data-carousel="slide"
            >
                <!-- Carousel wrapper -->
                <div
                    class="mt-24 relative h-56 overflow-hidden rounded-lg md:h-96"
                >
                    <!-- Item 1 -->
                    <div
                        class="hidden duration-700 ease-in-out"
                        data-carousel-item
                    >
                        <img
                            src="https://via.placeholder.com/800x400/FFB6C1"
                            class="absolute block w-full h-full object-cover"
                            alt="Slide 1"
                        />
                    </div>
                    <!-- Item 2 -->
                    <div
                        class="hidden duration-700 ease-in-out"
                        data-carousel-item
                    >
                        <img
                            src="https://via.placeholder.com/800x400/87CEFA"
                            class="absolute block w-full h-full object-cover"
                            alt="Slide 2"
                        />
                    </div>
                    <!-- Item 3 -->
                    <div
                        class="hidden duration-700 ease-in-out"
                        data-carousel-item
                    >
                        <img
                            src="https://via.placeholder.com/800x400/32CD32"
                            class="absolute block w-full h-full object-cover"
                            alt="Slide 3"
                        />
                    </div>
                </div>
                <!-- Slider controls -->
                <button
                    type="button"
                    class="absolute top-0 left-10 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                    data-carousel-prev
                >
                    <span
                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 group-hover:bg-white/50 group-focus:ring-4 group-focus:ring-white/70"
                    >
                        <svg
                            aria-hidden="true"
                            class="w-6 h-6 text-white"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19l-7-7 7-7"
                            ></path>
                        </svg>
                        <span class="sr-only">Previous</span>
                    </span>
                </button>
                <button
                    type="button"
                    class="absolute top-0 right-10 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                    data-carousel-next
                >
                    <span
                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 group-hover:bg-white/50 group-focus:ring-4 group-focus:ring-white/70"
                    >
                        <svg
                            aria-hidden="true"
                            class="w-6 h-6 text-white"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                            ></path>
                        </svg>
                        <span class="sr-only">Next</span>
                    </span>
                </button>
            </div>

            <section class="bg-white py-8">
                <div
                    class="container mx-auto flex items-center flex-wrap pr-12 pl-12 pt-4 pb-12"
                >
                    <nav id="store" class="w-full z-30 top-0 py-1">
                        <div
                            class="w-full container mx-auto flex flex-wrap items-center justify-between mt-0 px-2 py-3"
                        >
                            <a
                                class="uppercase tracking-wide no-underline hover:no-underline font-bold text-gray-800 text-xl"
                                href="#"
                            >
                                Store
                            </a>
                        </div>
                        <div
                            class="mb-4 flex items-center w-full md:w-auto justify-between"
                        >
                            <div class="w-full md:w-1/4">
                                <form class="flex items-center">
                                    <label for="simple-search" class="sr-only"
                                        >Search</label
                                    >
                                    <div class="relative w-full">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                        >
                                            <svg
                                                aria-hidden="true"
                                                class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                                fill="currentColor"
                                                viewbox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </div>

                                        <input
                                            type="text"
                                            id="simple-search"
                                            v-model="searchValue"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                            placeholder="Search"
                                            required=""
                                        />
                                    </div>
                                </form>
                            </div>

                            <!-- Filter button to show checkboxes -->

                            <button
                                @click="
                                    isButtonFilterClick = !isButtonFilterClick
                                "
                                id="filterDropdownButton"
                                data-dropdown-toggle="filterDropdown"
                                class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                type="button"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    aria-hidden="true"
                                    class="h-4 w-4 mr-2 text-gray-400"
                                    viewbox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                {{
                                    isButtonFilterClick
                                        ? "Submit Filter"
                                        : "Show Filter"
                                }}
                                <svg
                                    class="-mr-1 ml-1.5 w-5 h-5"
                                    fill="currentColor"
                                    viewbox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg"
                                    aria-hidden="true"
                                >
                                    <path
                                        clip-rule="evenodd"
                                        fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    />
                                </svg>
                            </button>
                            <div
                                id="filterDropdown"
                                class="overflow-y-auto h-40 z-10 hidden w-56 p-3 bg-white rounded-lg shadow dark:bg-gray-700"
                            >
                                <h6
                                    class="mb-3 text-sm font-bold text-gray-900 dark:text-white"
                                >
                                    Category
                                </h6>

                                <ul
                                    class="space-y-2 text-sm"
                                    v-for="category in categories"
                                    :key="category.id"
                                >
                                    <li class="flex items-center">
                                        <input
                                            type="checkbox"
                                            :value="category.name"
                                            v-model="filterCategoryName"
                                            @change="filterCategory($event)"
                                            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                        />
                                        <label
                                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                        >
                                            {{ category.name }}
                                        </label>
                                    </li>
                                </ul>
                                <h6
                                    class="mb-3 mt-3 text-sm font-bold text-gray-900 dark:text-white"
                                >
                                    Brand
                                </h6>

                                <ul
                                    class="space-y-2 text-sm"
                                    v-for="brand in brands"
                                    :key="brand.id"
                                >
                                    <li class="flex items-center">
                                        <input
                                            type="checkbox"
                                            :value="brand.name"
                                            v-model="filterBrandName"
                                            @change="filterBrand($event)"
                                            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                        />
                                        <label
                                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                        >
                                            {{ brand.name }}
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>

                    <div
                        @scroll="handleScroll"
                        class="duration-500 flex-grow grid grid-cols-1 w-400 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8"
                    >
                        <div
                            v-if="products.data.length == 0"
                            class="justify-center"
                        >
                            <h1>No Product Found...</h1>
                        </div>
                        <div
                            v-for="product in products.data"
                            :key="product.id"
                            class="relative bg-white shadow-md rounded-lg overflow-hidden transform transition duration-500 hover:scale-105"
                        >
                            <img
                                :src="product.image[0]"
                                alt="Product Image"
                                class="w-full h-48 object-contain"
                            />
                            <div class="p-4">
                                <h2 class="text-xl font-semibold">
                                    {{ product.name }}
                                </h2>
                                <p class="text-gray-600">
                                    {{ product.brand.name }}
                                </p>
                                <div class="p-2 absolute top-1 right-2">
                                    <button @click="toggleHeart(product.id)">
                                        <span
                                            v-if="
                                                isHearted.includes(product.id)
                                            "
                                            ><svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="w-6 h-6 text-red-500"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
                                                /></svg
                                        ></span>
                                        <span v-else
                                            ><svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="w-6 h-6 text-gray-500"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
                                                /></svg
                                        ></span>
                                    </button>
                                </div>
                                <div class="mt-4 mb-2">
                                    <span class="text-lg font-bold">{{
                                        product.price
                                    }}</span>

                                    <button
                                        v-if="product.quantity === 0"
                                        :disabled="product.quantity === 0"
                                        class="bg-blue text-white py-2 px-4 rounded-lg float-right"
                                        :class="{
                                            'bg-red-500':
                                                product.quantity === 0,
                                        }"
                                    >
                                        Out of Stack
                                    </button>

                                    <button
                                        v-else
                                        :disabled="
                                            state.cartItems.includes(product.id)
                                        "
                                        @click="addToCart(product)"
                                        :class="{
                                            'bg-gray-300':
                                                state.cartItems.includes(
                                                    product.id
                                                ),
                                        }"
                                        class="bg-blue-500 text-white py-2 px-4 rounded-lg float-right"
                                    >
                                        {{
                                            state.cartItems.includes(product.id)
                                                ? "Added to Cart"
                                                : "Add to Cart"
                                        }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="noMoreData" class="text-center">
                    No More Data....
                </div>
                <div v-else-if="nextPageUrl" class="text-center">
                    Load More Data....
                </div>
            </section>
            <!-- 
            <section class="bg-white py-8">
                <div class="container py-8 px-6 mx-auto">
                    <a
                        class="uppercase tracking-wide no-underline hover:no-underline font-bold text-gray-800 text-xl mb-8"
                        href="#"
                    >
                        About
                    </a>

                    <p class="mt-8 mb-8">
                        This template is inspired by the stunning nordic
                        minimalist design - in particular:
                        <br />
                        <a
                            class="text-gray-800 underline hover:text-gray-900"
                            href="http://savoy.nordicmade.com/"
                            target="_blank"
                            >Savoy Theme</a
                        >
                        created by
                        <a
                            class="text-gray-800 underline hover:text-gray-900"
                            href="https://nordicmade.com/"
                            >https://nordicmade.com/</a
                        >
                        and
                        <a
                            class="text-gray-800 underline hover:text-gray-900"
                            href="https://www.metricdesign.no/"
                            target="_blank"
                            >https://www.metricdesign.no/</a
                        >
                    </p>

                    <p class="mb-8">
                        Lorem ipsum dolor sit amet, consectetur
                        <a href="#">random link</a> adipiscing elit, sed do
                        eiusmod tempor incididunt ut labore et dolore magna
                        aliqua. Vel risus commodo viverra maecenas accumsan
                        lacus vel facilisis volutpat. Vitae aliquet nec
                        ullamcorper sit. Nullam eget felis eget nunc lobortis
                        mattis aliquam. In est ante in nibh mauris. Egestas
                        congue quisque egestas diam in. Facilisi nullam vehicula
                        ipsum a arcu. Nec nam aliquam sem et tortor consequat.
                        Eget mi proin sed libero enim sed faucibus turpis in.
                        Hac habitasse platea dictumst quisque. In aliquam sem
                        fringilla ut. Gravida rutrum quisque non tellus orci ac
                        auctor augue mauris. Accumsan lacus vel facilisis
                        volutpat est velit egestas dui id. At tempor commodo
                        ullamcorper a. Volutpat commodo sed egestas egestas
                        fringilla. Vitae congue eu consequat ac.
                    </p>
                </div>
            </section>

            <footer
                class="container mx-auto bg-white py-8 border-t border-gray-400"
            >
                <div class="container flex px-3 py-8">
                    <div class="w-full mx-auto flex flex-wrap">
                        <div class="flex w-full lg:w-1/2">
                            <div class="px-3 md:px-0">
                                <h3 class="font-bold text-gray-900">About</h3>
                                <p class="py-4">
                                    Lorem ipsum dolor sit amet, consectetur
                                    adipiscing elit. Maecenas vel mi ut felis
                                    tempus commodo nec id erat. Suspendisse
                                    consectetur dapibus velit ut lacinia.
                                </p>
                            </div>
                        </div>
                        <div
                            class="flex w-full lg:w-1/2 lg:justify-end lg:text-right mt-6 md:mt-0"
                        >
                            <div class="px-3 md:px-0">
                                <h3 class="text-left font-bold text-gray-900">
                                    Social
                                </h3>

                                <div class="w-full flex items-center py-4 mt-0">
                                    <a href="#" class="mx-2">
                                        <svg
                                            class="w-6 h-6 fill-current"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"
                                            ></path>
                                        </svg>
                                    </a>
                                    <a href="#" class="mx-2">
                                        <svg
                                            class="w-6 h-6 fill-current"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
                                            ></path>
                                        </svg>
                                    </a>
                                    <a href="#" class="mx-2">
                                        <svg
                                            class="w-6 h-6 fill-current"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"
                                            ></path>
                                        </svg>
                                    </a>
                                    <a href="#" class="mx-2">
                                        <svg
                                            class="w-6 h-6 fill-current"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z"
                                            ></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer> -->
        </Nav>
    </body>
</template>
