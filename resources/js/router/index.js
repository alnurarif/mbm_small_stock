import {createRouter, createWebHistory} from "vue-router";

//admin
import homeAdminIndex from '../components/admin/home/index.vue';
import itemsAdminIndex from '../components/admin/items/index.vue';
import suppliersAdminIndex from '../components/admin/suppliers/index.vue';

//employee
import homeEmployeeIndex from '../components/employee/home/index.vue';

//store executive
import homeStoreExecutiveIndex from '../components/store_executive/home/index.vue';
import requisitionsStoreExecutiveIndex from '../components/store_executive/requisitions/index.vue';
import stockStoreExecutiveIndex from '../components/store_executive/stock/index.vue';

//pages 
// import homepageIndex from '../components/pages/home/index.vue';
//login 
import login from '../components/auth/login.vue';
import register from '../components/auth/register.vue';

import notFound from '../components/notFound.vue';




const routes = [
    // admin 
    {
        path : '/admin/home',
        name : 'adminHome',
        component : homeAdminIndex,
        meta : {
            requiresAuth : true
        }
    },
    {
        path : '/admin/items',
        name : 'adminItems',
        component : itemsAdminIndex,
        meta : {
            requiresAuth : true
        }
    },
    {
        path : '/admin/suppliers',
        name : 'adminSuppliers',
        component : suppliersAdminIndex,
        meta : {
            requiresAuth : true
        }
    },

    // employee 
    {
        path : '/employee/home',
        name : 'employeeHome',
        component : homeEmployeeIndex,
        meta : {
            requiresAuth : true
        }
    },

    // store executive 
    {
        path : '/store_executive/home',
        name : 'storeExecutiveHome',
        component : homeStoreExecutiveIndex,
        meta : {
            requiresAuth : true
        }
    },

    {
        path : '/store_executive/requisitions',
        name : 'storeExecutiveRequisitions',
        component : requisitionsStoreExecutiveIndex,
        meta : {
            requiresAuth : true
        }
    },

    {
        path : '/store_executive/stock',
        name : 'storeExecutiveStock',
        component : stockStoreExecutiveIndex,
        meta : {
            requiresAuth : true
        }
    },

    // login 
    {
        path : '/',
        name : 'login',
        component : login,
        meta : {
            requiresAuth : false
        }
    },

    // register 
    {
        path : '/register',
        name : 'register',
        component : register,
        meta : {
            requiresAuth : false
        }
    },

    

    // notFound 
    {
        path: '/:pathMatch(.*)*',
        name : 'notFound',
        component : notFound,
        meta : {
            requiresAuth : false
        }
    }
]

// const router = createRouter({
//     history : createWebHistory({
//         history : createWebHistory(process.env.BASE_URL)
//     }),
//     routes
// })

const  router = createRouter({
    history : createWebHistory(),
    routes
})
router.beforeEach((to, from) => {
    if(to.meta.requiresAuth && !localStorage.getItem('token')){
        return { name : 'login' }
    }

    if(to.meta.requiresAuth == false && localStorage.getItem('token') && localStorage.getItem('role') == 'admin'){
        return { name : 'adminHome'}
    }

    if(to.meta.requiresAuth == false && localStorage.getItem('token') && localStorage.getItem('role') == 'employee'){
        return { name : 'employeeHome'}
    }

    if(to.meta.requiresAuth == false && localStorage.getItem('token') && localStorage.getItem('role') == 'store-executive'){
        return { name : 'storeExecutiveHome'}
    }
})
export default router;