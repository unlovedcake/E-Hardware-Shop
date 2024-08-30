<template>
    <Head title="Dashboard" />

    <Nav :isDisableShake="isDisableShake">
        <div class="max-w-3xl mx-auto p-4">
            <div
                v-if="carts.length === 0"
                class="flex items-center justify-center h-screen"
            >
                <p class="text-gray-600">Your cart is empty.</p>
            </div>
            <div class="mt-20" v-else>
                <div
                    v-for="item in carts"
                    :key="item.id"
                    class="flex items-center border-b border-gray-200 py-4"
                >
                    <div
                        class="mr-3 relative w-32 h-32 overflow-hidden border-2 border-black-600 fade-in"
                    >
                        <img
                            :src="item.image"
                            alt="Profile Image"
                            class="object-contain w-full h-full"
                        />
                    </div>

                    <div class="flex-1">
                        <h2 class="text-lg font-semibold">{{ item.name }}</h2>
                        <p class="text-gray-500">{{ item.description }}</p>
                        <p class="text-black">Price: ${{ item.price }}</p>
                        <p class="text-black">
                            Quantity: {{ item.available_quantity }}
                        </p>
                        <InputError class="mt-2" :message="item.message" />
                    </div>
                    <div class="flex items-center mt-2">
                        <button
                            @click="updateProductQuantity(item, 'decrement')"
                            :disabled="item.quantity <= 1"
                            class="px-3 py-1 bg-gray-200 rounded-l text-gray-700 hover:bg-gray-300 disabled:opacity-50"
                        >
                            -
                        </button>
                        <span class="mx-2 w-10 text-center">{{
                            item.quantity
                        }}</span>
                        <button
                            @click="updateProductQuantity(item, 'increment')"
                            class="px-3 py-1 bg-gray-200 rounded-r text-gray-700 hover:bg-gray-300"
                        >
                            +
                        </button>
                    </div>

                    <button
                        @click="removeFromCart(item.id)"
                        class="text-red-500 hover:text-red-700 ml-4"
                    >
                        Remove
                    </button>
                </div>

                <div class="mt-6 text-right">
                    <p class="text-lg font-semibold">Total: ${{ totalSum }}</p>
                </div>
            </div>
        </div>

        <div>
            <!-- Button that triggers the checkout function -->
            <button @click="checkout" class="btn btn-primary">Checkout</button>
        </div>

        <!-- <div>
            <h1>Your Shopping Cart</h1>
            <ul>
                <li v-for="item in state.cartItems" :key="item.id">
                    {{ item.name }} - {{ item.price }} - Quantity:
                    {{ item.quantity }}
                    <button
                        @click="removeFromCarts()"
                        class="text-red-500 hover:text-red-700 ml-4"
                    >
                        Remove
                    </button>
                </li>
            </ul>
        </div>
        <p v-if="state.cartItems.length === 0">Your cart is empty.</p> -->
    </Nav>
</template>

<script setup>
import { Head, useForm, router } from "@inertiajs/vue3";
import Nav from "@/Layouts/Nav.vue";
import { store, useCartStore, state } from "../../store.js";
import InputError from "@/Components/InputError.vue";
import axios from "axios";

import { onMounted, ref, reactive, computed, watch, watchEffect } from "vue";

const props = defineProps({
    carts: {
        type: [Array],
    },

    cartCountItems: {
        type: Number,
    },
});

const {
    cartCounts,
    decrementCartCount,
    removeItemFromCart,
    getCartItemsFromLocalStorage,
} = useCartStore();

const isDisableShake = ref(false);

const cartsIt = ref([]);
watchEffect(() => {
    console.log("STATE" + JSON.stringify(state.cartItems));
});
onMounted(() => {
    window.addEventListener("popstate", handleBackButton);
});

const countNumberCart = ref(0);

function handleBackButton(event) {
    // let url = new URL(route("home"));
    // router.visit(url, {
    //     preserveScroll: true,
    //     preserveState: true,
    //     replace: true,
    // });
    router.visit("/home");
    console.log("Back button clicked");
}
let totalSum = computed(() => {
    let total = [];

    Object.entries(props.carts).forEach(([key, val]) => {
        total.push(val.quantity * val.price);
    });

    return total.reduce(function (total, num) {
        return total + num;
    }, 0);
});

var form = useForm({
    name: "",
    description: "",
    price: 0.0,
    quantity: 0,
    category_name: "",
    category_id: 0,
});

function updateProductQuantity(product, action) {
    if (product.quantity <= 1 && action == "decrement") {
        return;
    }

    if (
        product.quantity == product.available_quantity &&
        action == "increment"
    ) {
        Object.keys(props.carts).forEach((key) => {
            if (props.carts[key].id == product.id) {
                props.carts[key].message =
                    "Sorry, we have only " +
                    product.available_quantity +
                    " available for this item " +
                    product.name;
                console.log("cartItemsLength");
            }
        });

        return;
    }
    form.id = product.id;
    // form.description = product.description;
    // form.price = product.price;
    // form.image = product.image;
    // form.category_name = product.category_name;
    // form.category_id = product.category_id;
    // console.log("ID:" + form.id);

    form.get(
        `/cart/add/${form.id}/?action=${action}`,

        {
            preserveScroll: true,
            //onBefore: () => confirm("Are you sure you want to delete this user?"),
            onError: () => {},
            onSuccess: () => {},
        }
    );
}
let cartItems = [];
async function checkout() {
    Object.keys(props.carts).forEach((key) => {
        cartItems.push({
            product_id: parseInt(props.carts[key].id),
            price: parseFloat(props.carts[key].price),
            quantity: parseInt(props.carts[key].quantity),
            name: props.carts[key].name,

            brand_name: props.carts[key].brand_name,
            category_name: props.carts[key].category_name,
            description: props.carts[key].description,

            total:
                parseFloat(props.carts[key].price) *
                parseInt(props.carts[key].quantity),
            image: props.carts[key].image,
        });
    });

    try {
        const response = await axios.post("/checkout", {
            cart_items: cartItems,
            // headers: {
            //     "Content-Type": "application/json",
            // },
        });
        console.log("Checkout successful:", response.data);
        // Handle success (e.g., redirect, show message)
    } catch (error) {
        console.error("Checkout failed:", error.response.data);
        // Handle error (e.g., show error message)
    }
}

const removeFromCart = (id) => {
    form.get(`/cart/remove/${id}`);
    isDisableShake.value = true;

    console.log("DELETED" + store.count);

    decrementCartCount();
    removeItemFromCart(id);

    console.log("DECREMENT" + state.cartCounts);
};
let cartCount = computed(() => {
    let cartItemsLength = [];
    let jsonData = JSON.stringify(props.carts);
    const items = JSON.parse(jsonData);
    Object.keys(items).forEach((key) => {
        cartItemsLength.push(items[key].name);
    });

    console.log("cartItemsLength" + cartItemsLength.length);
    return cartItemsLength.length;
});

store.count = cartCount;
</script>
