<script setup>
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import { ref, onMounted } from "vue";
import { state } from "../store.js";

const showingNavigationDropdown = ref(false);

const props = defineProps({
    cartCount: {
        type: Number,
    },

    isDisableShake: {
        type: Boolean,
    },
});
</script>

<style scoped>
/* @keyframes shake {
    0% {
        transform: translateX(0);
    }
    25% {
        transform: translateX(-5px);
    }
    50% {
        transform: translateX(0);
    }
    75% {
        transform: translateX(5px);
    }
    100% {
        transform: translateX(0);
    }
}

.animate-shake {
    animation: shake 0.5s ease-in-out;
} */
.animate-shake {
    animation: shake 0.82s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
    transform: translate3d(0, 0, 0);
}

@keyframes shake {
    20%,
    80% {
        transform: translate3d(2px, 0, 0);
    }

    30%,
    50%,
    70% {
        transform: translate3d(-4px, 0, 0);
    }

    40%,
    60% {
        transform: translate3d(4px, 0, 0);
    }
}
</style>

<template>
    <nav
        id="header"
        class="fixed top-0 left-0 w-full z-50 py-1 bg-white shadow-lg"
    >
        <div
            class="w-full container mx-auto flex flex-wrap items-center justify-between mt-0 px-6 py-3"
        >
            <label for="menu-toggle" class="cursor-pointer md:hidden block">
                <svg
                    class="fill-current text-gray-900"
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    viewBox="0 0 20 20"
                >
                    <title>menu</title>
                    <path
                        d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"
                    ></path>
                </svg>
            </label>
            <input class="hidden" type="checkbox" id="menu-toggle" />

            <div
                class="hidden md:flex md:items-center md:w-auto w-full order-3 md:order-1"
                id="menu"
            >
                <nav>
                    <ul
                        class="md:flex items-center justify-between text-base text-gray-700 pt-4 md:pt-0"
                    >
                        <li>
                            <NavLink
                                class="bg-white"
                                :href="route('home')"
                                :active="route().current('home')"
                            >
                                Home
                            </NavLink>
                        </li>
                        <li>
                            <NavLink
                                :href="route('cart.index')"
                                :active="route().current('product.index')"
                            >
                                Cart
                                <div :class="{ shake: isDisableShake }">
                                    🛒
                                    <span class="text-red">
                                        {{ state.cartCounts }}
                                        <!-- {{ $page.props.count.cart }} -->
                                    </span>
                                </div>
                            </NavLink>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="order-1 md:order-2">
                <a
                    class="flex items-center tracking-wide no-underline hover:no-underline font-bold text-gray-800 text-xl"
                    href="#"
                >
                    <svg
                        class="fill-current text-gray-800 mr-2"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M5,22h14c1.103,0,2-0.897,2-2V9c0-0.553-0.447-1-1-1h-3V7c0-2.757-2.243-5-5-5S7,4.243,7,7v1H4C3.447,8,3,8.447,3,9v11 C3,21.103,3.897,22,5,22z M9,7c0-1.654,1.346-3,3-3s3,1.346,3,3v1H9V7z M5,10h2v2h2v-2h6v2h2v-2h2l0.002,10H5V10z"
                        />
                    </svg>
                    NORDICS
                </a>
            </div>

            <div class="order-2 md:order-3 flex items-center" id="nav-content">
                <div class="hidden sm:flex sm:items-center sm:ms-6 mr-3">
                    <!-- Settings Dropdown -->
                    <div class="ms-3 relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button
                                        type="button"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-cyan-600 hover:bg-cyan-700 focus:outline-none transition ease-in-out duration-150"
                                    >
                                        <div class="relative mr-3">
                                            <img
                                                class="w-6 h-6 rounded-full"
                                                src="https://media.istockphoto.com/id/1337144146/vector/default-avatar-profile-icon-vector.jpg?s=612x612&w=0&k=20&c=BIbFwuv7FxTWvh5S3vB6bkT0Qv8Vn8N5Ffseq84ClGI="
                                                alt=""
                                            />
                                            <span
                                                class="top-0 left-4 absolute w-2.5 h-2.5 bg-green-400 border-1 border-white dark:border-gray-800 rounded-full"
                                            ></span>
                                        </div>
                                        {{ $page.props.auth.user.name }}

                                        <svg
                                            class="ms-2 -me-0.5 h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                </span>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')">
                                    Profile
                                </DropdownLink>
                                <DropdownLink
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                >
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
                <NavLink
                    :href="route('cart.index')"
                    :active="route().current('product.index')"
                >
                    <div
                        class="relative cursor-pointer"
                        :class="{ 'animate-shake': isDisableShake }"
                    >
                        <svg
                            class="w-8 h-8 fill-current hover:text-black"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M21,7H7.462L5.91,3.586C5.748,3.229,5.392,3,5,3H2v2h2.356L9.09,15.414C9.252,15.771,9.608,16,10,16h8 c0.4,0,0.762-0.238,0.919-0.606l3-7c0.133-0.309,0.101-0.663-0.084-0.944C21.649,7.169,21.336,7,21,7z M17.341,14h-6.697L8.371,9 h11.112L17.341,14z"
                            />
                            <circle cx="10.5" cy="18.5" r="1.5" />
                            <circle cx="17.5" cy="18.5" r="1.5" />
                        </svg>

                        <div
                            v-if="state.cartCounts != 0"
                            class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-semibold rounded-full px-2 py-1"
                        >
                            {{ state.cartCounts }}
                        </div>
                    </div>
                </NavLink>
            </div>
        </div>
    </nav>

    <main>
        <slot />
    </main>
</template>
