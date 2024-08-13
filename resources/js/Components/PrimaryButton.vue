<script setup>
import { onMounted, ref, reactive, computed, watch, watchEffect } from "vue";
const props = defineProps({
    label: {
        type: String,
    },
    icon: {
        type: String,
        required: false,
    },
    action: {
        type: Function,
        required: false,
    },
});

let getButtonClass = computed(() => {
    let buttonClass = "";
    if (props.label === "Add Product") {
        buttonClass =
            "flex items-center justify-center text-white bg-blue-700 hover:bg-blue-600 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-blue-600 focus:outline-none dark:focus:ring-primary-800";
    } else if (props.label === "Edit") {
        buttonClass =
            "py-2 px-3 flex items-center text-sm font-medium text-center text-gray-900 focus:outline-none bg-white rounded-lg border border-cyan-500 hover:bg-cyan-600 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700";
    } else if (props.label === "Preview") {
        buttonClass =
            "py-2 px-3 flex items-center text-sm font-medium text-center text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700";
    } else {
        buttonClass =
            "flex items-center text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900";
    }

    return buttonClass;
});
</script>
<template>
    <button :class="getButtonClass">
        <!-- Render SVG Icon -->
        <span v-if="icon" class="mr-2" v-html="icon"></span>
        <!-- <span v-if="icon" class="mr-2">
            <component :is="icon" class="h-6 w-6" />
        </span> -->
        {{ label }}

        <slot />
    </button>
</template>
