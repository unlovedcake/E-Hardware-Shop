import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import Toast, { POSITION } from 'vue-toastification';
// Import the CSS or use your own!
import "vue-toastification/dist/index.css";





import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';



const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(ElementPlus)
            .use(VueSweetalert2)
            .use(Toast, {
                //Vue-Toastification__fade
                // Vue-Toastification__bounce
                // Vue-Toastification__zoom
                // Vue-Toastification__slideBlurred

                position: POSITION.TOP_CENTER,
                transition: 'Vue-Toastification__fade',
                timeout: 3000,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: false,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: false,
                closeButton: true,
                icon: true,
                rtl: false,
                newestOnTop: true,
                maxToasts: 5,
              })
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
