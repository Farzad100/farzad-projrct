export default [
  //ADMIN
  {
    title: 'داشبورد',
    icon: 'tachometer-alt-slowest',
    route_name: 'dashboard',
    params: { role: 'admin' },
    exact: true,
    classes: null,
    roles: ['admin']
  },
  {
    title: 'کاربران',
    icon: 'users',
    route_name: 'dash-users',
    params: { role: 'admin' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['admin']
  },
  {
    title: 'سفارش‌ها',
    icon: 'credit-card',
    route_name: 'dash-orders',
    params: { role: 'admin' },
    exact: false,
    classes: null,
    roles: ['admin']
  },
  {
    title: 'قسطاکارت‌ها',
    icon: 'credit-card-front',
    route_name: 'dash-cards',
    params: { role: 'admin' },
    exact: false,
    classes: null,
    roles: ['admin']
  },
  {
    title: 'فروشگاه‌ها',
    icon: 'store',
    route_name: 'dash-shops',
    params: { role: 'admin' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['admin']
  },
  {
    title: 'سازمان‌ها',
    icon: 'building',
    route_name: 'dash-organs',
    params: { role: 'admin' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['admin']
  },
  {
    title: 'اقساط',
    icon: 'money-check',
    route_name: 'dash-installments',
    params: { role: 'admin' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['admin']
  },
  {
    title: 'تراکنش‌ها',
    icon: 'receipt',
    route_name: 'dash-transactions',
    params: { role: 'admin' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['admin']
  },
  {
    title: 'کارمزد فروشگاه‌ها',
    icon: 'box-usd',
    route_name: 'dash-incomes',
    params: { role: 'admin' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['admin']
  },
  {
    title: 'کدهای تخفیف',
    icon: 'percentage',
    route_name: 'dash-discounts',
    params: { role: 'admin' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['admin']
  },
  {
    title: 'لینک‌های کوتاه',
    icon: 'link',
    route_name: 'dash-links',
    params: { role: 'admin' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['admin']
  },
  /* {
    title: 'نظرات کاربران',
    icon: 'comment',
    route_name: 'dash-comments',
    params: { role: 'admin' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['admin']
  }, */
  {
    title: 'پیامک‌ها',
    icon: 'sms',
    route_name: 'dash-sms',
    params: { role: 'admin' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['admin']
  },
  {
    title: 'اطلاعیه‌ها',
    icon: 'bell',
    route_name: 'dash-inbox',
    params: { role: 'admin' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['admin']
  },
  {
    title: 'حسابداری',
    icon: 'chart-pie',
    route_name: 'dash-accounting',
    params: { role: 'admin' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['admin']
  },
  {
    title: 'حساب کاربری',
    icon: 'user',
    route_name: 'dash-profile',
    params: { role: 'admin' },
    exact: false,
    classes: null,
    roles: ['admin']
  },
  {
    title: 'تنظیمات',
    icon: 'cog',
    route_name: 'dash-settings',
    params: { role: 'admin' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['admin']
  },

  //USER
  {
    title: 'داشبورد',
    icon: 'tachometer-alt-slowest',
    route_name: 'dashboard',
    params: { role: 'user' },
    exact: true,
    classes: null,
    roles: ['user']
  },
  {
    title: 'سفارش‌ها',
    icon: 'credit-card',
    route_name: 'dash-orders',
    params: { role: 'user' },
    exact: false,
    classes: null,
    roles: ['user']
  },
  {
    title: 'سفارش جدید',
    icon: 'plus',
    route_name: 'dash-new-order',
    params: { role: 'user' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['user']
  },
  {
    title: 'اطلاعیه‌ها',
    icon: 'bell',
    route_name: 'dash-inbox',
    params: { role: 'user' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['user']
  },
  {
    title: 'حساب کاربری',
    icon: 'user',
    route_name: 'dash-profile',
    params: { role: 'user' },
    exact: false,
    classes: null,
    roles: ['user']
  },

  //ESHOP
  {
    title: 'داشبورد',
    icon: 'tachometer-alt-slowest',
    route_name: 'dashboard',
    params: { role: 'eshop' },
    exact: true,
    classes: null,
    roles: ['eshop']
  },
  {
    title: 'سفارش‌ها',
    icon: 'credit-card',
    route_name: 'dash-orders',
    params: { role: 'eshop' },
    exact: false,
    classes: null,
    roles: ['eshop']
  },
  /* {
    title: 'درآمد‌ها',
    icon: 'box-usd',
    route_name: 'dash-incomes',
    params: { role: 'eshop' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['eshop']
  }, */
  {
    title: 'اطلاعیه‌ها',
    icon: 'bell',
    route_name: 'dash-inbox',
    params: { role: 'eshop' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['eshop']
  },
  {
    title: 'حساب کاربری',
    icon: 'user',
    route_name: 'dash-profile',
    params: { role: 'eshop' },
    exact: false,
    classes: null,
    roles: ['eshop']
  },

  //SHOP
  {
    title: 'داشبورد',
    icon: 'tachometer-alt-slowest',
    route_name: 'dashboard',
    params: { role: 'shop' },
    exact: true,
    classes: null,
    roles: ['shop']
  },
  {
    title: 'سفارش‌ها',
    icon: 'credit-card',
    route_name: 'dash-orders',
    params: { role: 'shop' },
    exact: false,
    classes: null,
    roles: ['shop']
  },
  {
    title: 'سفارش جدید',
    icon: 'plus',
    route_name: 'dash-new-order',
    params: { role: 'shop' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['shop']
  },
  {
    title: 'اقساط',
    icon: 'money-check',
    route_name: 'dash-installments',
    params: { role: 'shop' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['shop']
  },
  {
    title: 'درآمد‌ها',
    icon: 'box-usd',
    route_name: 'dash-incomes',
    params: { role: 'shop' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['shop']
  },
  {
    title: 'اطلاعیه‌ها',
    icon: 'bell',
    route_name: 'dash-inbox',
    params: { role: 'shop' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['shop']
  },
  {
    title: 'حساب کاربری',
    icon: 'user',
    route_name: 'dash-profile',
    params: { role: 'shop' },
    exact: false,
    classes: null,
    roles: ['shop']
  },

  //ORGAN
  {
    title: 'داشبورد',
    icon: 'tachometer-alt-slowest',
    route_name: 'dashboard',
    params: { role: 'organ' },
    exact: true,
    classes: null,
    roles: ['organ']
  },
  {
    title: 'سفارش‌ها',
    icon: 'credit-card',
    route_name: 'dash-orders',
    params: { role: 'organ' },
    exact: false,
    classes: null,
    roles: ['organ']
  },
  {
    title: 'اقساط',
    icon: 'money-check',
    route_name: 'dash-installments',
    params: { role: 'organ' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['organ']
  },
  {
    title: 'اطلاعیه‌ها',
    icon: 'bell',
    route_name: 'dash-inbox',
    params: { role: 'organ' },
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['organ']
  },
  {
    title: 'حساب کاربری',
    icon: 'user',
    route_name: 'dash-profile',
    params: { role: 'organ' },
    exact: false,
    classes: null,
    roles: ['organ']
  }

  /* {
    title: 'تغییر شماره موبایل',
    icon: 'mobile',
    route_name: 'dash-user-mobilechange',
    exact: false,
    classes: 'd-none d-lg-flex',
    roles: ['user']
  }, */
];
