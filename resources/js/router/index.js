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
		component: Dashboard
	},
	{
		path: '/sports',
		name:'Sports',
		component: Sports
	},
	{
		path: '/clubs',
		name:'Clubs',
		component: Clubs
	},
	{
		path: '/teams',
		name:'Teams',
		component: Teams
	},
    {
		path: '/register/athlete',
		name:'AthleteRegister',
		component: AthleteRegister
	},
    {
		path: '/login',
		name:'Login',
		component: Login
	},
    {
		path: '/forgot-password',
		name:'ForgotPassword',
		component: ForgotPassword
	}
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

export default router;