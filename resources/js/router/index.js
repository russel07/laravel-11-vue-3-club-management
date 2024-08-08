import { createWebHashHistory, createRouter } from "vue-router";
import Dashboard from "../components/Dashboard";
import Sports from "../components/Sports";
import Clubs from "../components/Clubs";
import Teams from "../components/Teams";
import Login from "../components/auth/Login";
import Admin from "../components/users/Admin";
import ClubAdmin from "../components/users/ClubAdmin";
import Coach from "../components/users/Coach";
import Athlete from "../components/users/Athlete";
import AthleteRegister from "../components/auth/AthleteRegister";
import ForgotPassword from "../components/auth/ForgotPassword";
import Logout from "../components/auth/Logout";

const routes = [
    {
		path: '/',
		name:'Dashboard',
		component: Dashboard,
        meta: {
            requiresAuth: true,
			roles: ['Admin', 'Club Admin', 'Coach', 'Athlete']
        }
	},
	{
		path: '/sports',
		name:'Sports',
		component: Sports,
        meta: {
            requiresAuth: true,
			roles: ['Admin']
        }
	},
	{
		path: '/clubs',
		name:'Clubs',
		component: Clubs,
        meta: {
            requiresAuth: true, 
			roles: ['Admin']
        }
	},
	{
		path: '/teams',
		name:'Teams',
		component: Teams,
        meta: {
            requiresAuth: true, 
			roles: ['Club Admin']
        }
	},
    {
        path: '/users/admin',
        name: 'Admin',
        component: Admin,
        meta: {
            requiresAuth: true,
			roles: ['Admin'] 
        }
    },
    {
        path: '/users/club-admin',
        name: 'ClubAdmin',
        component: ClubAdmin,
        meta: {
            requiresAuth: true,
			roles: ['Admin'] 
        }
    },
    {
        path: '/coach',
        name: 'ManageCoach',
        component: Coach,
        meta: {
            requiresAuth: true,
			roles: ['Club Admin'] 
        }
    },
    {
        path: '/athlete',
        name: 'ManageAthlete',
        component: Athlete,
        meta: {
            requiresAuth: true,
			roles: ['Coach'] 
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
	},
    {
        path: '/logout',
        name: 'Logout',
        component: Logout
    }
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const isAuthenticated 	= Boolean(localStorage.getItem('_GymAppUserToken'));
	const loggedInUser 		= JSON.parse(localStorage.getItem('_GymAppLoggedInUser'));
    let userRole 			= '';
    
    if( loggedInUser ) {
        userRole = loggedInUser.user_type;
    }

    if ( to.meta.requiresAuth ) {
        if ( !isAuthenticated ) {
            next({ name: 'Login' });
        } else if (to.meta.roles && !to.meta.roles.includes(userRole)) {
            next({ name: 'Dashboard' }); 
        } else {
            next();
        }
    } else if ( to.meta.guest && isAuthenticated ) {
        next({ name: 'Dashboard' });
    } else {
        next(); 
    }
});


export default router;