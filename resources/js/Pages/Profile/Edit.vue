<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DeleteUserForm from "./Partials/DeleteUserForm.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import { Head } from "@inertiajs/vue3";
import Nav from "@/Layouts/Nav.vue";
import { ref, onMounted, onUnmounted, watch, watchEffect, computed } from "vue";

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const imagePreview = ref("https://via.placeholder.com/150");

const imagePreviews = ref([]);
const imageFiles = ref([]);
const fileNames = ref([]);

const inputFiles = ref(null);

// function onFileChange(event) {
//     const file = event.target.files[0];
//     if (file) {
//         const reader = new FileReader();
//         reader.onload = (e) => {
//             imagePreview.value = e.target.result;
//         };
//         reader.readAsDataURL(file);
//     }
// }

//Multiple Images
function onFilesChange(event) {
    const files = event.target.files;
    // Clear previous previews

    Array.from(files).map((file) => fileNames.value.push(file.name));

    Array.from(files).forEach((file) => {
        imageFiles.value.push(file);
        console.log("Data Transfer", imageFiles.value);

        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreviews.value.push(e.target.result); // Add each image preview to the array
        };
        reader.readAsDataURL(file);
    });
}

const fileInput = ref(null);

onMounted(() => {
    fileInput.value.focus();
});
function removeImage(index) {
    imagePreviews.value.splice(index, 1);
    fileNames.value.splice(index, 1); // Remove the selected image from the array
    imageFiles.value.splice(index, 1);

    updateFileInput();
}
// Create a new DataTransfer object to hold the new FileList

function updateFileInput() {
    console.log("Data Transfer", imageFiles.value.length);
    const dataTransfer = new DataTransfer();
    // Append the remaining files to the DataTransfer object
    imageFiles.value.forEach((image) => {
        dataTransfer.items.add(image);
    });

    fileInput.value.files = dataTransfer.files;
}

function handleBeforeLeave(el) {
    el.style.opacity = 1;
}
function handleLeave(el, done) {
    el.style.transition = "opacity 0.5s";
    el.style.opacity = 0;
    el.addEventListener("transitionend", done, { once: true });
}
</script>

<style scoped>
/* .fade-in {
    opacity: 0;
    animation: fadeIn 0.5s forwards;
} */

@keyframes fadeIn {
    to {
        opacity: 1;
    }
}

.fade-enter-active {
    opacity: 0;
    animation: fadeIn 0.8s forwards;
}
.fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
    opacity: 0;
}
</style>

<template>
    <Head title="Profile" />

    <div>
        <h1 v-if="route().current('profile*')">Home</h1>
        <h1 v-else>Admin</h1>
    </div>

    <div class="py-12">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div
                    class="p-4 sm:p-8 bg-white shadow sm:rounded-lg flex flex-col md:flex-row gap-4 justify-between"
                >
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                    <div class="w-full md:w-1/2">
                        <div class="mb-4">
                            <!-- Multiple Image Previews -->
                            <div
                                class="flex flex-wrap justify-center gap-4 mb-4"
                            >
                                <div
                                    v-if="imagePreviews.length === 0"
                                    class="flex justify-center mb-4"
                                >
                                    <img
                                        src="https://via.placeholder.com/150"
                                        alt="Profile Image"
                                        class="w-32 h-32 rounded-full object-cover"
                                    />
                                </div>
                                <transition-group
                                    v-else
                                    name="fade"
                                    @before-leave="handleBeforeLeave"
                                    @leave="handleLeave"
                                >
                                    <div
                                        v-for="(image, index) in imagePreviews"
                                        :key="index"
                                        class="relative w-32 h-32 rounded-full overflow-hidden border-2 border-black-600 fade-in"
                                    >
                                        <img
                                            :src="image"
                                            alt="Profile Image"
                                            class="object-cover w-full h-full"
                                        />
                                        <!-- Remove Button -->
                                        <button
                                            @click="removeImage(index)"
                                            class="w-8 h-8 absolute top-4 right-4 bg-gray-200 text-white rounded-full p-1 focus:outline-none"
                                        >
                                            &times;
                                        </button>
                                    </div></transition-group
                                >
                            </div>
                            <label
                                for="profileImages"
                                class="block text-gray-700 text-sm font-bold mb-2"
                                >Profile Images</label
                            >
                            <input
                                ref="fileInput"
                                type="file"
                                id="profileImages"
                                @change="onFilesChange"
                                class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                                multiple
                            />
                            <ul v-if="fileNames.length">
                                <li
                                    v-for="(fileName, index) in fileNames"
                                    :key="index"
                                >
                                    {{ fileName }}
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- <div class="w-full md:w-1/2 py-4">
                        <div class="mb-4">
                           
                            <div class="flex justify-center mb-4">
                                <img
                                    :src="imagePreview"
                                    alt="Profile Image"
                                    class="w-32 h-32 rounded-full object-cover"
                                    v-if="imagePreview"
                                />
                            </div>
                            <label
                                for="profileImage"
                                class="block text-gray-700 text-sm font-bold mb-2"
                                >Profile Image</label
                            >
                            <input
                                type="file"
                                id="profileImage"
                                @change="onFileChange"
                                class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                            />
                        </div>
                    </div> -->
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </div>
</template>
