import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../pages/Dashboard.vue';
import BusinessList from '../pages/BusinessList.vue';
import BusinessView from '../pages/BusinessView.vue';

const routes = [
  {
    path: '/',
    redirect: '/dashboard',
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
  },
  {
    path: '/businesses',
    name: 'businesses',
    component: BusinessList,
  },
  {
    path: '/businesses/:id',
    name: 'business-view',
    component: BusinessView,
    props: true,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;

