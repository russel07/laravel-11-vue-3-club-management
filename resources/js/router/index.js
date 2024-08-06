import { createWebHashHistory, createRouter } from "vue-router";
import Dashboard from "../components/Dashboard";
import Sports from "../components/Sports";
import Clubs from "../components/Clubs";
import Teams from "../components/Teams";
import Login from "../components/auth/Login";
import SuperAdmin from "../components/users/SuperAdmin";
import AthleteRegister from "../components/auth/AthleteRegister";
import ForgotPassword from "../components/auth/ForgotPassword";

const routes = [
    {
		path: '/',
		name:'Dashboard',
		component: Dashboard,
        meta: {
            requiresAuth: true
        }
	},
	{
		path: '/sports',
		name:'Sports',
		component: Sports,
        meta: {
            requiresAuth: true
        }
	},
	{
		path: '/clubs',
		name:'Clubs',
		component: Clubs,
        meta: {
            requiresAuth: true
        }
	},
	{
		path: '/teams',
		name:'Teams',
		component: Teams,
        meta: {
            requiresAuth: true
        }
	},
    {
		path: '/register/athlete',
		name:'AthleteRegister',
		component: AthleteRegister,
        meta: {
            guest: true
        }
	},
    {
		path: '/login',
		name:'Login',
		component: Login,
        meta: {
            guest: true
        }
	},
    {
		path: '/forgot-password',
		name:'ForgotPassword',
		component: ForgotPassword,
        meta: {
            guest: true
        }
	}
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

export default router;