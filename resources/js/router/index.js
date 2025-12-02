import Vue from 'vue'
import VueRouter from 'vue-router'
import ExampleComponent from '../components/DashboardComponent.vue'
import UserComponent from "../components/UserComponent";
import ContactComponent from "../components/ContactComponent.vue";
import CategoryComponent from "../components/CategoryComponent.vue";
import OurWorksComponent from "../components/OurWorksComponent.vue";
import WorkPhotosComponent from "../components/WorkPhotosComponent.vue";
import ReviewsComponent from "../components/ReviewsComponent.vue";

Vue.use(VueRouter);

const routes = [
    {
        path: '/:lang/dashboard',
        name: 'Dashboard',
        component: ExampleComponent
    },
    {
        path: '/:lang/dashboard/users',
        name: 'Users',
        component: UserComponent
    },
    {
        path: '/:lang/dashboard/contacts',
        name: 'Contact',
        component: ContactComponent
    },
    {
        path: '/:lang/dashboard/categories',
        name: 'Categories',
        component: CategoryComponent
    },
    {
        path: '/:lang/dashboard/ourworks',
        name: 'OurWorks',
        component: OurWorksComponent
    },
    {
        path: '/:lang/dashboard/workphotos',
        name: 'WorkPhotos',
        component: WorkPhotosComponent
    },
    {
        path: '/:lang/dashboard/reviews',
        name: 'Reviews',
        component: ReviewsComponent
    },

];
Vue.config.devtools = true;
const router = new VueRouter({
    mode: 'history',
    // linkExactActiveClass: "active",
    linkActiveClass: "linkActiveClass",
    // base: process.env.BASE_URL,
    routes
});

export default router
