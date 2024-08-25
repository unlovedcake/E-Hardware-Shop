<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Dashboard from "../Dashboard.vue";
import { Head, usePage, useForm, router, useRemember } from "@inertiajs/vue3";
import { onMounted, ref, reactive, computed, watch, watchEffect } from "vue";
import { initFlowbite } from "flowbite";
import Pagination from "@/Components/Pagination.vue";
import ModalCreate from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useToast } from "vue-toastification";
import { Plus } from "@element-plus/icons-vue";
import Swal from "sweetalert2";
import { PlusCircleIcon } from "@heroicons/vue/24/solid";

const toast = useToast();

const searchValue = ref(usePage().props.searchTextFilter),
    pageNumber = ref(1),
    isOpenCreateModal = ref(false);

const isImagesUpdate = ref(false);
const isButtonFilterClick = ref(false);

const iconAdd = `
<svg class="mr-2 w-6 h-6 text-white-800 dark:text-white"aria-hidden="true"xmlns="http://www.w3.org/2000/svg"
 width="24" height="24" fill="none"   viewBox="0 0 24 24">
   <path stroke="currentColor" stroke-linecap="round"  stroke-linejoin="round" stroke-width="2"  
   d="M12 7.757v8.486M7.757 12h8.486M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />                               
</svg> `;

const iconEdit = `
<svg   class="w-4 h-4  text-white-800 dark:text-white"
    aria-hidden="true"
    xmlns="http://www.w3.org/2000/svg"
    width="24"
    height="24"
    fill="none"
    viewBox="0 0 24 24"">
   <path stroke="currentColor" stroke-linecap="round"  stroke-linejoin="round" stroke-width="2"
    d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 
    1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />                               
</svg> `;

const iconPreview = `<svg
    xmlns="http://www.w3.org/2000/svg"
    viewbox="0 0 24 24"
    fill="currentColor"
    class="w-4 h-4 mr-2 -ml-0.5">
    <path
        d="M12 15a3 3 0 100-6 3 3 0 000 6z"
    />
    <path
        fill-rule="evenodd"
        clip-rule="evenodd"
        d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 010-1.113zM17.25 12a5.25 5.25 0 11-10.5 0 5.25 5.25 0 0110.5 0z"
    />
</svg> `;

const iconDelete = `<svg
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
</svg>`;

const previewModalData = ref({
    name: "",
    price: "",
    description: "",
    image: [],
    category: {
        name: "",
    },
    brand: {
        name: "",
    },
});

const isOpenUpdateModal = ref(false);
const selectedCategoryId = ref(1);
const filterCategoryName = ref([]);
const filterBrandName = ref([]);
const selectedBrandId = ref(1);

const maxLimitImageUpload = ref(3);

const productImages = ref([]);
const productUpdateImages = ref([]);

const labelName = ref("Add Product");
const labelNameEdit = ref("Edit");
const labelNamePreview = ref("Preview");
const labelNameDelete = ref("Delete");

const dialogImageUrl = ref("");
const dialogVisible = ref(false);

const showCheckboxes = ref(false);

const form = useForm({
    name: "",
    description: "",
    price: null,
    quantity: null,
    image: [],
    brand_id: 1,
    category_id: 1,
    index: [],
    url: [],
});

const handleRemove = (file, fileList) => {
    const removedUrl = file.url;

    if (removedUrl.includes("storage/uploads")) {
        form.url.push(removedUrl);
        console.log(`Removed file: ${removedUrl}`);
    } else {
        productUpdateImages.value.pop(file);
        console.log(` productImages.value: ${productImages.value}`);
        console.log(` productUpdateImages.value: ${productUpdateImages.value}`);
    }
};
const handleFileChange = (file) => {
    if (isImagesUpdate.value) {
        productUpdateImages.value.push(file);
        productUpdateImages.value = [...new Set(productUpdateImages.value)];
    } else {
        productImages.value.push(file);

        productImages.value = [...new Set(productImages.value)];

        console.log("Selected Images:" + productImages.value[0].raw);
    }

    console.log("IsImagesUpdate" + productUpdateImages.value);

    // for (const image of productImages.value) {
    //     const formData = new FormData();
    //     formData.append("product_images[]", image.raw);

    //     //console.log("Selected Images:" + image.raw);
    //     //console.log("Selected Images:" + productImages.value.length);
    // }
};

const handlePictureCardPreview = (file) => {
    dialogImageUrl.value = file.url;
    dialogVisible.value = true;
};
const handleExceed = (files) => {
    toast.error("Only max 3 images allowed", {
        timeout: 2000,
    });
    console.log("Error:", `Only max 3 images allowed.`);
};
const handleSuccess = (response, file) => {
    console.log("Success:", response, file);
};
const handleError = (err, file) => {
    console.log("Error:", err, file);
};

const props = defineProps({
    errors: Object,
    // icon: {
    //     type: [Object],
    //     default: () => PlusCircleIcon,
    // },
    products: {
        type: [Object, Array],
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    brands: {
        type: Array,
        default: () => [],
    },
});

onMounted(() => {
    initFlowbite();
});

let productUrl = computed(() => {
    let url = new URL(route("product.index"));

    url.searchParams.append("page", pageNumber.value);

    if (searchValue.value) {
        url.searchParams.append("searchValue", searchValue.value);
        console.log("not empty ");
    } else {
        console.log("empty");
    }

    return url;
});

const addProduct = () => {
    for (const image of productImages.value) {
        form.image.push(image.raw);
    }
    form.post("/product/store", {
        onError: (error) => {
            console.log("Error" + error);
        },
        onSuccess: () => {
            console.log("Success");
            toast.success("Product added successfully", {
                timeout: 2000,
            });
            isOpenCreateModal.value = false;
            dialogImageUrl.value = "";
            productImages.value = [];
            productUpdateImages.value = [];
            form.reset();
        },
    });
};

const editButton = (product) => {
    console.log("ki");
    isOpenUpdateModal.value = true;
    isImagesUpdate.value = true;
    //formProd.id = product;
    // axios.get(route("product.category")).then((res) => {
    //     selectedProduct.value = product;
    //     getCategories.value = res.data;
    //isModalOpen.value = true;
    // });
    form.product = product;
    form.id = product.id;

    form.name = product.name;
    form.description = product.description;
    form.price = product.price;
    form.quantity = product.quantity;
    form.image = product.image;

    selectedCategoryId.value = product.category_id;
    selectedBrandId.value = product.brand_id;

    form.category_id = selectedCategoryId.value;
    form.brand_id = selectedBrandId.value;

    // for (const image of form.image.l) {
    //     productImages.value += [{ url: image }];
    // }

    productImages.value = form.image.map((url, index) => ({
        url,
        index,
    }));
};

const updateProduct = () => {
    if (productUpdateImages.value) {
        form.image = [];
        for (const image of productUpdateImages.value) {
            form.image.push(image.raw);
        }
    }

    // console.log("Files" + form.image);
    //   productImages.value = form.image.map((url, index) => ({
    //     url,
    // }));

    //form.image = productImages.value;

    // console.log("OKEY" + productImages.value);

    form.post(`product/update/${form.id}`, {
        onError: (error) => {
            console.log("Error:" + error);
        },
        onSuccess: () => {
            console.log("Success");
            toast.success("Product updated successfully", {
                timeout: 2000,
            });
            form.url = [];
            form.reset();
            productUpdateImages.value = [];
            productImages.value = [];
            isOpenUpdateModal.value = false;
            isImagesUpdate.value = false;
        },
    });
};

const deleteProduct = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
        showClass: {
            popup: "slow-animation-show",
        },
        hideClass: {
            popup: "slow-animation-hide",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete("/product/delete/" + id, {
                onError: (error) => {
                    console.log("Error" + error);
                },
                onSuccess: () => {
                    console.log("Success");
                    toast.success("Product deleted successfully", {
                        timeout: 2000,
                    });
                },
            });
        }
    });
};

const filterCat = [];
const filterBran = [];
let url = new URL(route("product.index"));
function routeFilterCategory(param, value) {
    url.searchParams.append(param, value);

    router.visit(url, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}
function routeFilterBrand(param, value) {
    url.searchParams.append(param, value);

    router.visit(url, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
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

const fetchCategoryName = () => {
    const selectedCategory = props.categories.find(
        (category) => category.id === selectedCategoryId.value
    );
    const categoryName = selectedCategory ? selectedCategory.name : "";

    form.category_id = selectedCategoryId.value;
};

const fetchBrandName = () => {
    const selectedBrand = props.brands.find(
        (brand) => brand.id === selectedBrandId.value
    );
    const brandName = selectedBrand ? selectedBrand.name : "";

    console.log(brandName + selectedBrandId.value);

    form.brand_id = selectedBrandId.value;
};

watch(
    () => productUrl.value,
    (newValue) => {
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

const openModalCreate = () => {
    isOpenCreateModal.value = true;
    isImagesUpdate.value = false;
};

const openModalPreview = (product) => {
    console.log(product);
    previewModalData.value = product;
};

const closeModal = () => {
    isOpenCreateModal.value = false;
    isOpenUpdateModal.value = false;

    productImages.value = [];

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
                        <PrimaryButton
                            @click="openModalCreate"
                            :label="labelName"
                            :icon="iconAdd"
                        />

                        <div
                            class="flex items-center space-x-3 w-full md:w-auto"
                        >
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
                                <th scope="col" class="px-4 py-4">Image</th>
                                <th scope="col" class="px-4 py-4">
                                    Product name
                                </th>
                                <th scope="col" class="px-4 py-4">Brand</th>
                                <th scope="col" class="px-4 py-3">Category</th>

                                <th scope="col" class="px-4 py-3">
                                    Description
                                </th>
                                <th scope="col" class="px-4 py-3">Price</th>
                                <th scope="col" class="px-4 py-3">Quantity</th>
                                <th scope="col" class="px-4 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                class="border-b dark:border-gray-700"
                                v-for="product in products.data"
                                :key="product.id"
                            >
                                <th
                                    scope="row"
                                    class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                >
                                    {{ product.id }}
                                </th>
                                <th
                                    scope="row"
                                    class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                >
                                    <div class="flex">
                                        <div class="flex justify-end mr-2">
                                            <img
                                                v-for="image in product.image"
                                                :key="image.id"
                                                :src="image"
                                                class="border-1 border-black dark:border-gray-800 rounded-full h-10 w-10 -mr-2"
                                            />
                                        </div>
                                    </div>
                                    <!-- <div class="grid grid-cols-3">
                                        <img
                                            v-for="image in product.image"
                                            :key="image.id"
                                            :src="image"
                                            alt="Product Images"
                                            class="w-32 h-32 object-contain rounded-lg"
                                        />
                                    </div> -->
                                </th>

                                <th
                                    scope="row"
                                    class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                >
                                    {{ product.name }}
                                </th>
                                <th
                                    scope="row"
                                    class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                >
                                    {{ product.brand.name }}
                                </th>
                                <td class="px-4 py-3">
                                    {{ product.category.name }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ product.description }}
                                </td>
                                <td class="px-4 py-3 max-w-[12rem] truncate">
                                    {{ product.price }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ product.quantity }}
                                </td>
                                <td
                                    class="px-4 py-3 flex items-center justify-end"
                                >
                                    <div class="flex items-center space-x-4">
                                        <PrimaryButton
                                            @click="editButton(product)"
                                            :label="labelNameEdit"
                                            :icon="iconEdit"
                                        />

                                        <PrimaryButton
                                            @click="openModalPreview(product)"
                                            data-modal-target="previewProductModal"
                                            data-modal-toggle="previewProductModal"
                                            :label="labelNamePreview"
                                            :icon="iconPreview"
                                        />

                                        <PrimaryButton
                                            @click="deleteProduct(product.id)"
                                            :label="labelNameDelete"
                                            :icon="iconDelete"
                                        />
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <nav
                    class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4"
                    aria-label="Table navigation"
                >
                    <span
                        class="text-sm font-normal text-gray-500 dark:text-gray-400"
                    >
                        Showing
                        <span
                            class="font-semibold text-gray-900 dark:text-white"
                            >1-10</span
                        >
                        of
                        <span
                            class="font-semibold text-gray-900 dark:text-white"
                            >1000</span
                        >
                    </span>
                    <Pagination class="mt-4" :links="products.links" />
                </nav>
            </div>
        </div>

        <!-- End block -->

        <!-- Preview Modal -->
        <div
            id="previewProductModal"
            tabindex="-1"
            aria-hidden="true"
            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full"
        >
            <div
                class="relative p-4 w-full max-w-xl h-full md:h-auto flex flex-col"
            >
                <!-- Modal content -->
                <div
                    class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5"
                >
                    <!-- Modal header -->
                    <div class="flex justify-between mb-4 rounded-t sm:mb-5">
                        <div
                            class="text-lg text-gray-900 md:text-xl dark:text-white"
                        >
                            <h3 class="font-semibold">
                                {{ previewModalData.name }}
                            </h3>
                            <p class="font-bold">
                                {{ previewModalData.price }}
                            </p>
                        </div>
                        <div>
                            <button
                                type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex dark:hover:bg-gray-600 dark:hover:text-white"
                                data-modal-toggle="previewProductModal"
                            >
                                <svg
                                    aria-hidden="true"
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
                    </div>

                    <div class="flex gap-x-4 mb-4">
                        <img
                            v-for="image in previewModalData.image"
                            :key="image.id"
                            :src="image"
                            alt="Image 1"
                            class="w-24 h-24"
                        />
                    </div>

                    <dl>
                        <dt
                            class="mb-2 font-semibold leading-none text-gray-900 dark:text-white"
                        >
                            Description
                        </dt>
                        <dd
                            class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400"
                        >
                            {{ previewModalData.description }}
                        </dd>

                        <div class="flex gap-x-3">
                            <div class="flex flex-col justify-between">
                                <dt
                                    class="mb-2 font-semibold leading-none text-gray-900 dark:text-white"
                                >
                                    Category
                                </dt>
                                <dd
                                    class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400"
                                >
                                    {{ previewModalData.category.name }}
                                </dd>
                            </div>
                            <div class="flex flex-col">
                                <dt
                                    class="mb-2 font-semibold leading-none text-gray-900 dark:text-white"
                                >
                                    Brand
                                </dt>
                                <dd
                                    class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400"
                                >
                                    {{ previewModalData.brand.name }}
                                </dd>
                            </div>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Create modal -->

        <ModalCreate :show="isOpenCreateModal">
            <div
                tabindex="-1"
                class="top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full overflow-auto"
            >
                <div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
                    <!-- Modal header -->
                    <div
                        class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600"
                    >
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white"
                        >
                            Add Product
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

                    <form @submit.prevent="addProduct">
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
                                    placeholder="Type product name"
                                />

                                <InputError :message="errors.name" />
                            </div>
                            <div>
                                <label
                                    for="brand"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                    >Brands</label
                                >
                                <select
                                    v-model="selectedBrandId"
                                    @change="fetchBrandName"
                                    id="category"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                >
                                    <option
                                        v-for="brand in brands"
                                        :key="brand.id"
                                        :value="brand.id"
                                    >
                                        {{ brand.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="grid gap-4 mb-4 sm:grid-cols-2">
                                <div class="w-50">
                                    <label
                                        for="price"
                                        class="w-50 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >Price</label
                                    >
                                    <input
                                        type="number"
                                        v-model="form.price"
                                        value="399"
                                        name="price"
                                        id="price"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="$299"
                                    />

                                    <InputError :message="errors.price" />
                                </div>
                                <div class="w-50">
                                    <label
                                        for="price"
                                        class="w-50 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >Quantity</label
                                    >
                                    <input
                                        type="number"
                                        v-model="form.quantity"
                                        value="399"
                                        name="price"
                                        id="price"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="$299"
                                    />

                                    <InputError :message="errors.quantity" />
                                </div>
                            </div>

                            <div>
                                <label
                                    for="category"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                    >Categories</label
                                >
                                <select
                                    @change="fetchCategoryName"
                                    v-model="selectedCategoryId"
                                    id="category"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                >
                                    <option
                                        v-for="category in categories"
                                        :key="category.id"
                                        :value="category.id"
                                    >
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label
                                    for="description"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                    >Description</label
                                >
                                <textarea
                                    id="description"
                                    rows="4"
                                    v-model="form.description"
                                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Write product description here"
                                ></textarea>
                            </div>
                            <!-- Multiple images upload -->

                            <div class="sm:col-span-2 grid md:gap-6">
                                <div class="relative z-0 w-full h-50">
                                    <el-upload
                                        v-model:file-list="productImages"
                                        list-type="picture-card"
                                        v-model:limit="maxLimitImageUpload"
                                        method="get"
                                        multiple
                                        :on-preview="handlePictureCardPreview"
                                        :on-remove="handleRemove"
                                        :on-change="handleFileChange"
                                        :on-exceed="handleExceed"
                                        :on-success="handleSuccess"
                                        :on-error="handleError"
                                    >
                                        <el-icon>
                                            <Plus />
                                        </el-icon>
                                    </el-upload>
                                    <dd
                                        class="mb-4 font-light text-sm text-gray-500 sm:mb-5 dark:text-gray-400"
                                    >
                                        Max limit is 3 images
                                    </dd>

                                    <el-dialog v-model="dialogVisible">
                                        <figure class="max-w-lg">
                                            <img
                                                w-full
                                                :src="dialogImageUrl"
                                                alt="Preview Image"
                                            />
                                        </figure>
                                    </el-dialog>
                                </div>
                            </div>
                            <!-- end -->
                        </div>
                        <button
                            type="submit"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            class="text-white bg-gradient-to-l from-blue-500 via-blue-600 to-blue-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                        >
                            Add New Product
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
                <div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
                    <!-- Modal content -->

                    <div
                        class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600"
                    >
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white"
                        >
                            Update Product
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
                    <form @submit.prevent="updateProduct()">
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
                            <div>
                                <label
                                    for="brand"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                    >Brand</label
                                >
                                <select
                                    v-model="selectedBrandId"
                                    @change="fetchBrandName"
                                    id="brand"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                >
                                    <option
                                        v-for="brand in brands"
                                        :key="brand.id"
                                        :value="brand.id"
                                    >
                                        {{ brand.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="grid gap-4 mb-4 sm:grid-cols-2">
                                <div class="w-50">
                                    <label
                                        for="price"
                                        class="w-50 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >Price</label
                                    >
                                    <input
                                        type="number"
                                        v-model="form.price"
                                        value="399"
                                        name="price"
                                        id="price"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="$299"
                                    />
                                </div>
                                <div class="w-50">
                                    <label
                                        for="price"
                                        class="w-50 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >Quantity</label
                                    >
                                    <input
                                        type="number"
                                        v-model="form.quantity"
                                        value="399"
                                        name="price"
                                        id="price"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="$299"
                                    />
                                </div>
                            </div>

                            <div>
                                <label
                                    for="category"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                    >Category</label
                                >
                                <select
                                    v-model="selectedCategoryId"
                                    @change="fetchCategoryName"
                                    id="category"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                >
                                    <option
                                        v-for="category in categories"
                                        :key="category.id"
                                        :value="category.id"
                                    >
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label
                                    for="description"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                    >Description</label
                                ><textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="5"
                                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Write a description..."
                                ></textarea>
                                <!-- Add images upload -->

                                <div class="sm:col-span-2 grid md:gap-6 mt-3">
                                    <div
                                        class="relative z-0 w-full h-50 mb-6 group"
                                    >
                                        <el-upload
                                            v-model:file-list="productImages"
                                            list-type="picture-card"
                                            v-model:limit="maxLimitImageUpload"
                                            method="get"
                                            multiple
                                            :on-preview="
                                                handlePictureCardPreview
                                            "
                                            :on-remove="handleRemove"
                                            :on-change="handleFileChange"
                                            :on-exceed="handleExceed"
                                            :on-success="handleSuccess"
                                            s
                                            :on-error="handleError"
                                        >
                                            <!-- <img
                                                    v-for="image in form.image"
                                                    :key="image.id"
                                                    :src="image"
                                                    class="picture-card"
                                                /> -->

                                            <el-icon>
                                                <Plus />
                                            </el-icon>
                                        </el-upload>
                                        <dd
                                            class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400"
                                        >
                                            Max limit is 3 images
                                        </dd>
                                        <el-dialog v-model="dialogVisible">
                                            <figure class="max-w-lg">
                                                <img
                                                    w-full
                                                    :src="dialogImageUrl"
                                                    alt="Preview Image"
                                                />
                                            </figure>
                                        </el-dialog>
                                    </div>
                                </div>
                                <!-- end -->
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <!-- <div class="flex items-center space-x-4"> -->
                            <button
                                type="submit"
                                class="text-white bg-gradient-to-l from-blue-500 via-blue-600 to-blue-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                            >
                                Update product
                            </button>
                            <button
                                @click="closeModal()"
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
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </ModalCreate>
        <!-- End Update Modal -->
    </AdminLayout>
</template>
