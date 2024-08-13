<template>
    <!-- <div>
        <Component
            :is="link.url ? 'Link' : 'span'"
            v-for="link in links"
            :key="link.id"
            :href="link.url"
            class="px-1"
            :class="{
                'text-red-500': !link.url,
                'bg-blue-700 text-white font-bold': link.active,
            }"
        />
    </div> -->

    <div v-if="links.length > 0">
        <div class="flex flex-wrap -mb-1">
            <template v-for="link in links" :key="link.id">
                <div
                    v-if="link.url === null"
                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border-rounded"
                    :class="{
                        'text-gray-500': !link.url,
                        'bg-blue-700 text-white font-bold': link.active,
                    }"
                    v-html="link.label"
                ></div>

                <Link
                    v-else
                    :is="link.url ? 'Link' : 'span'"
                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border-rounded hover:bg-white focus:border-indigo-500 inline-block focus:text-indigo-500"
                    :class="{
                        'bg-gray-300 text-white': link.active,
                    }"
                    :href="link.url"
                    preserve-state
                >
                    <span
                        v-html="link.label"
                        :class="!link.url ? '' : 'text-black  font-bold'"
                    ></span>
                </Link>
            </template>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from "@inertiajs/vue3";
defineProps({
    links: Object,
});
</script>
