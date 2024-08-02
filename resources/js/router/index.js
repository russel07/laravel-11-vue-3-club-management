import { createWebHashHistory, createRouter } from "vue-router";
import Dashboard from "../components/Dashboard";
import Sports from "../components/Sports.vue";
import Login from "../components/Login";
import Register from "../components/Register";

const routes = [
    {
		path: '/',
		name:'Dashboard',
		component: Dashboard
	},
	{
		path: '/sports',
		name:'sports',
		component: Sports
	},
    {
		path: '/login',
		name:'Login',
		component: Login
	},
    {
		path: '/register',
		name:'Register',
		component: Register
	}
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

export default router;