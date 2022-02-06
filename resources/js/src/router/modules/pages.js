export default [
  {
    path: '/payments/result',
    component: () => import('../../pages/PayResponse.vue')
  },
  {
    path: '/shops/list',
    component: () => import('../../pages/landings/shops/List.vue')
  },
  {
    path: '/shops/:id',
    component: () => import('../../pages/landings/shops/Single.vue')
  }
];
