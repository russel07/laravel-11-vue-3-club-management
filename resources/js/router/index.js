import { createWebHashHistory, createRouter } from "vue-router";
import Dashboard from "../components/Dashboard";
import Sports from "../components/Sports";
import Clubs from "../components/Clubs";
import Teams from "../components/Teams";
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
		path: '/login2',
		name:'Login',
		component: Login
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