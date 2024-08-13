<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Dashboard from "../Dashboard.vue";
import { Head, usePage, useForm, router, useRemember } from "@inertiajs/vue3";
import { onMounted, ref, reactive, computed, watch, watchEffect } from "vue";
import { initFlowbite } from "flowbite";
import Pagination from "@/Components/Pagination.vue";
import ModalCreate from "@/Components/Modal.vue";
import { useToast } from "vue-toastification";

const toast = useToast();

const searchValue = ref(usePage().props.searchTextFilter),
    pageNumber = ref(1),
    isOpenCreateModal = ref(false);

const isOpenUpdateModal = ref(false);

const form = useForm({
    name: "",
    id: null,
});

const props = defineProps({
    errors: Object,

    categories: {
        type: Object,
    },
});

onMounted(() => {
    initFlowbite();
});

let categoryUrl = computed(() => {
    let url = new URL(route("category.index"));

    url.searchParams.append("page", pageNumber.value);

    if (searchValue.value) {
        url.searchParams.append("searchValue", searchValue.value);
        console.log("not empty ");
    } else {
        console.log("empty");
    }

    return url;
});

const editButton = (product) => {
    isOpenUpdateModal.value = true;

    form.name = product.name;
    form.id = product.id;

    console.log("ID:" + form.id);
};

const addCategory = () => {
    form.post("/category/store", {
        onError: (error) => {
            console.log("Error" + error);
        },
        onSuccess: () => {
            console.log("Success");
            toast.success("Category added successfully", {
                timeout: 2000,
            });
            isOpenCreateModal.value = false;
        },
    });
};

const updateCategory = () => {
    form.put(`category/update/${form.id}`, {
        onError: (error) => {
            console.log("Error" + error);
        },
        onSuccess: () => {
            console.log("Success");
            toast.success("Category updated successfully", {
                timeout: 2000,
            });
            isOpenUpdateModal.value = false;
        },
    });
};

const deleteCategory = (id) => {
    form.delete("/category/delete/" + id, {
        onError: (error) => {
            console.log("Error" + error);
        },
        onSuccess: () => {
            console.log("Success");
            toast.success("Category deleted successfully", {
                timeout: 2000,
            });
        },
    });
};

watch(
    () => categoryUrl.value,
    (newValue) => {
        console.log(newValue);

        router.visit(newValue, {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    }
);

const openModalCreate = () => {
    isOpenCreateModal.value = true;
};

const closeModal = () => {
    isOpenCreateModal.value = false;
    isOpenUpdateModal.value = false;

    console.log("Close" + isOpenCreateModal.value);
};
</script>

<template>
    <AdminLayout>
        <!-- Start block -->

        <div class="mx-auto w-full">
            <!-- Start coding here -->
            <div
                class="bg-white mt-4 mb-4 dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden"
            >
                <div
                    class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4"
                >
                    <div class="w-full md:w-1/2">
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

                    <div
                        class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0"
                    >
                        <button
                            @click="openModalCreate()"
                            :close-on-esc="false"
                            type="button"
                            class="flex items-center justify-center text-white bg-blue-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800"
                        >
                            <svg
                                class="h-3.5 w-3.5 mr-2"
                                fill="currentColor"
                                viewbox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg"
                                aria-hidden="true"
                            >
                                <path
                                    clip-rule="evenodd"
                                    fill-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                />
                            </svg>
                            Add Category
                        </button>
                        <div
                            class="flex items-center space-x-3 w-full md:w-auto"
                        >
                            <button
                                id="actionsDropdownButton"
                                data-dropdown-toggle="actionsDropdown"
                                class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                type="button"
                            >
                                <svg
                                    class="-ml-1 mr-1.5 w-5 h-5"
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
                                Actions
                            </button>
                            <div
                                id="actionsDropdown"
                                class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600"
                            >
                                <ul
                                    class="py-1 text-sm text-gray-700 dark:text-gray-200"
                                    aria-labelledby="actionsDropdownButton"
                                >
                                    <li>
                                        <a
                                            href="#"
                                            class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                            >Mass Edit</a
                                        >
                                    </li>
                                </ul>
                                <div class="py-1">
                                    <a
                                        href="#"
                                        class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white"
                                        >Delete all</a
                                    >
                                </div>
                            </div>
                            <button
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
                                Filter
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
                                class="z-10 hidden w-56 p-3 bg-white rounded-lg shadow dark:bg-gray-700"
                            >
                                <h6
                                    class="mb-3 text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    Category
                                </h6>
                                <ul
                                    class="space-y-2 text-sm"
                                    aria-labelledby="filterDropdownButton"
                                >
                                    <li class="flex items-center">
                                        <input
                                            id="apple"
                                            type="checkbox"
                                            value=""
                                            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                        />
                                        <label
                                            for="apple"
                                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                            >Apple (56)</label
                                        >
                                    </li>
                                    <li class="flex items-center">
                                        <input
                                            id="fitbit"
                                            type="checkbox"
                                            value=""
                                            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                        />
                                        <label
                                            for="fitbit"
                                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                            >Fitbit (56)</label
                                        >
                                    </li>
                                    <li class="flex items-center">
                                        <input
                                            id="dell"
                                            type="checkbox"
                                            value=""
                                            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                        />
                                        <label
                                            for="dell"
                                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                            >Dell (56)</label
                                        >
                                    </li>
                                    <li class="flex items-center">
                                        <input
                                            id="asus"
                                            type="checkbox"
                                            value=""
                                            checked=""
                                            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                        />
                                        <label
                                            for="asus"
                                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                            >Asus (97)</label
                                        >
                                    </li>
                                    <li class="flex items-center">
                                        <input
                                            id="logitech"
                                            type="checkbox"
                                            value=""
                                            checked=""
                                            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                        />
                                        <label
                                            for="logitech"
                                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                            >Logitech (97)</label
                                        >
                                    </li>
                                    <li class="flex items-center">
                                        <input
                                            id="msi"
                                            type="checkbox"
                                            value=""
                                            checked=""
                                            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                        />
                                        <label
                                            for="msi"
                                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                            >MSI (97)</label
                                        >
                                    </li>
                                    <li class="flex items-center">
                                        <input
                                            id="bosch"
                                            type="checkbox"
                                            value=""
                                            checked=""
                                            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                        />
                                        <label
                                            for="bosch"
                                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                            >Bosch (176)</label
                                        >
                                    </li>
                                    <li class="flex items-center">
                                        <input
                                            id="sony"
                                            type="checkbox"
                                            value=""
                                            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                        />
                                        <label
                                            for="sony"
                                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                            >Sony (234)</label
                                        >
                                    </li>
                                    <li class="flex items-center">
                                        <input
                                            id="samsung"
                                            type="checkbox"
                                            value=""
                                            checked=""
                                            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                        />
                                        <label
                                            for="samsung"
                                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                            >Samsung (76)</label
                                        >
                                    </li>
                                    <li class="flex items-center">
                                        <input
                                            id="canon"
                                            type="checkbox"
                                            value=""
                                            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                        />
                                        <label
                                            for="canon"
                                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                            >Canon (49)</label
                                        >
                                    </li>
                                    <li class="flex items-center">
                                        <input
                                            id="microsoft"
                                            type="checkbox"
                                            value=""
                                            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                        />
                                        <label
                                            for="microsoft"
                                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                            >Microsoft (45)</label
                                        >
                                    </li>
                                    <li class="flex items-center">
                                        <input
                                            id="razor"
                                            type="checkbox"
                                            value=""
                                            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                        />
                                        <label
                                            for="razor"
                                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                            >Razor (49)</label
                                        >
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table
                        class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
                    >
                        <thead
                            class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                        >
                            <tr>
                                <th scope="col" class="px-4 py-4">ID No.</th>

                                <th scope="col" class="px-4 py-4">
                                    Category Name
                                </th>

                                <th scope="col" class="px-4 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                class="border-b dark:border-gray-700"
                                v-for="category in categories.data"
                                :key="category.id"
                            >
                                <th
                                    scope="row"
                                    class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                >
                                    {{ category.id }}
                                </th>

                                <th
                                    scope="row"
                                    class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                >
                                    {{ category.name }}
                                </th>

                                <td
                                    class="px-4 py-3 flex items-center justify-end"
                                >
                                    <div class="flex items-center space-x-4">
                                        <button
                                            @click="editButton(category)"
                                            type="button"
                                            aria-controls="drawer-update-category"
                                            class="py-2 px-3 flex items-center text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4 mr-2 -ml-0.5"
                                                viewbox="0 0 20 20"
                                                fill="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"
                                                />
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            aria-controls="drawer-read-product-advanced"
                                            class="py-2 px-3 flex items-center text-sm font-medium text-center text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewbox="0 0 24 24"
                                                fill="currentColor"
                                                class="w-4 h-4 mr-2 -ml-0.5"
                                            >
                                                <path
                                                    d="M12 15a3 3 0 100-6 3 3 0 000 6z"
                                                />
                                                <path
                                                    fill-rule="evenodd"
                                                    clip-rule="evenodd"
                                                    d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 010-1.113zM17.25 12a5.25 5.25 0 11-10.5 0 5.25 5.25 0 0110.5 0z"
                                                />
                                            </svg>
                                            Preview
                                        </button>
                                        <button
                                            @click="deleteCategory(category.id)"
                                            type="button"
                                            class="flex items-center text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4 mr-2 -ml-0.5"
                                                viewbox="0 0 20 20"
                                                fill="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <Pagination class="mt-4" :links="categories.links" />
                </div>
            </div>
        </div>

        <!-- End block -->

        <!-- Create modal -->

        <ModalCreate :show="isOpenCreateModal">
            <div
                tabindex="-1"
                class="top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full overflow-auto"
            >
                <div class="relative p-4 w-full max-w-2md h-full md:h-auto">
                    <!-- Modal header -->
                    <div
                        class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600"
                    >
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white"
                        >
                            Add Category
                        </h3>
                        <button
                            @click="closeModal()"
                            type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-toggle="defaultModal"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->

                    <form @submit.prevent="addCategory">
                        <div class="w-full mb-4">
                            <div>
                                <label
                                    for="name"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                    >Name</label
                                >
                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    v-model="form.name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Type product name"
                                />
                                <div v-if="errors.name" class="text-red-500">
                                    {{ errors.name }}
                                </div>
                            </div>
                        </div>
                        <button
                            type="submit"
                            class="text-white bg-gradient-to-l from-blue-500 via-blue-600 to-blue-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                        >
                            Add New Category
                        </button>
                    </form>
                </div>
            </div>
        </ModalCreate>

        <!-- Start Update Modal -->

        <ModalCreate :show="isOpenUpdateModal">
            <div
                v-if="isOpenUpdateModal"
                tabindex="-1"
                aria-hidden="true"
                class="top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full"
            >
                <div class="relative p-4 w-full max-w-2xl max-h-full">
                    <!-- Modal content -->
                    <div
                        class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5"
                    >
                        <!-- Modal header -->
                        <div
                            class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600"
                        >
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white"
                            >
                                Update Category
                            </h3>
                            <button
                                type="button"
                                @click="closeModal()"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            >
                                <svg
                                    aria-hidden="true"
                                    class="w-5 h-5"
                                    fill="currentColor"
                                    viewbox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <form @submit.prevent="updateCategory">
                            <div class="grid gap-4 mb-4 sm:grid-cols-2">
                                <div>
                                    <label
                                        for="name"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >Name</label
                                    >
                                    <input
                                        type="text"
                                        name="name"
                                        id="name"
                                        v-model="form.name"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    />
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <button
                                    type="submit"
                                    class="text-white bg-gradient-to-l from-blue-500 via-blue-600 to-blue-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                                >
                                    Update product
                                </button>
                                <button
                                    type="button"
                                    class="text-red-600 inline-flex items-center hover:text-white border border-red-600 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900"
                                >
                                    <svg
                                        class="mr-1 -ml-1 w-5 h-5"
                                        fill="currentColor"
                                        viewbox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </ModalCreate>
        <!-- End Update Modal -->
    </AdminLayout>
</template>
