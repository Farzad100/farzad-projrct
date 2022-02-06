import http from './config';

const url = '/admin';

const adminRoutes = {

  get: {
    installments: (queries) => http.get(`${url}/ghests?${ queries ? queries : '' }`),
    installments_export: (queries) => http.get(`${url}/ghests/export?${ queries ? queries : '' }`),
  },

  charts: {

    users: {
      pie: (payload) => http.get(`${url}/charts/users/pie/${payload | '1'}`),
      line: (payload, queries) => http.get(
        `${url}/charts/users/line/${payload | '1'}?${queries ? queries : null}`
      ),
      overview: () => http.get(`${url}/charts/users/overview`),
    },

    orders: {
      pie: (payload) => http.get(`${url}/charts/orders/pie/${payload | '1'}`),
      line: (payload, queries) => http.get(
        `${url}/charts/orders/line/${payload | '1'}?${queries ? queries : null}`
      ),
      overview: () => http.get(`${url}/charts/orders/overview`),
    },

    sales: {
      pie: (payload) => http.get(`${url}/charts/sales/pie/${payload | '1'}`),
      line: (payload, queries) => http.get(
        `${url}/charts/sales/line/${payload | '1'}?${queries ? queries : null}`
      ),
      overview: () => http.get(`${url}/charts/sales/overview`),
    },

    installment: {
      pie: (payload) => http.get(`${url}/charts/installment/pie/${payload | '1'}`),
      line: (payload, queries) => http.get(
        `${url}/charts/installment/line/${payload | '1'}?${queries ? queries : null}`
      ),
      overview: () => http.get(`${url}/charts/installment/overview`),
    }

  },
  
  orders: {
    all: (queries) => http.get(`${url}/orders?${ queries ? queries : '' }`),
    export: (queries) => http.get(`${url}/orders/export?${ queries ? queries : '' }`),
    single: (role, oid) => http.get(`${role}/orders/${oid}`),
    docs: (oid, role) => http.get(`${role}/orders/${oid}/docs`),
    ghests: (role, oid) => http.get(`${role}/orders/${oid}/ghests`),
    reject: (oid, payload, isShop) => http.post(`${isShop === 'shop' ? '/shop' : url}/orders/${oid}/change/rejected`, { ...payload }),
    edit: (oid, payload) => http.post(`${url}/orders/${oid}/edit`, { ...payload }),
    changeStatus: (oid, status) => http.post(`${url}/orders/${oid}/change/${status}`),
    changeStatusSilent: (oid, status) => http.post(`${url}/orders/${oid}/change/${status}/silent`),
    sendMessage: (id, payload) => http.post(`${url}/orders/${id}/send-message`, { ...payload }),
  },
  
  users: { 
    all: (queries) => http.get(`${url}/users?${ queries ? queries : '' }`),
    export: (queries) => http.get(`${url}/users/export?${ queries ? queries : '' }`),
    single: (id) => http.get(`${url}/users/${id}`),
    edit: (id,payload) => http.post(`${url}/users/${id}/edit`,payload),
    delete: (id) => http.post(`${url}/users/${id}/delete`),
    sendMessage: (id, payload) => http.post(`${url}/users/${id}/send-message`, { ...payload }),

    backCheques: (oid, payload) => http.get(`${url}/users/${oid}/inquiry/back-cheques${payload ? '/1' : ''}`),
    facilities: (oid, payload) => http.get(`${url}/users/${oid}/inquiry/facilities${payload ? '/1' : ''}`),
  },

  shops: { 
    all: (queries) => http.get(`${url}/shops?${ queries ? queries : '' }`),
    export: (queries) => http.get(`${url}/shops/export?${ queries ? queries : '' }`),
    single: (id) => http.get(`${url}/shops/${id}`),
    edit: (id, payload) => http.post(`${url}/shops/${id}/edit`, { ...payload }),
    editForm: (id) => http.get(`${url}/shops/${id}/edit`),
    delete: (id) => http.post(`${url}/shops/${id}/delete`),
    sendMessage: (id, payload) => http.post(`${url}/shops/${id}/send-message`, { ...payload }),
    changeActivity: (id, status) => http.post(`${url}/shops/${id}/change/${status}`)
  },

  organs: { 
    all: (queries) => http.get(`${url}/organs?${ queries ? queries : '' }`),
    export: (queries) => http.get(`${url}/organs/export?${ queries ? queries : '' }`),
    single: (id) => http.get(`${url}/organs/${id}`),
    edit: (id,payload) => http.post(`${url}/organs/${id}/edit`,payload),
    editForm: (id) => http.get(`${url}/organs/${id}/edit`),
    delete: (id) => http.post(`${url}/organs/${id}/delete`),
    sendMessage: (id, payload) => http.post(`${url}/organs/${id}/send-message`, { ...payload }),
    create: (payload) => http.post(`${url}/organs/create`, { ...payload }),
    changeActivity: (id, status) => http.post(`${url}/organs/${id}/change/${status}`),
  },

  discounts: { 
    all: (queries) => http.get(`${url}/discounts?${ queries ? queries : '' }`), 
    edit: (id, payload) => http.post(`${url}/discounts/${id}/edit`, { ...payload }),
    delete: (id) => http.post(`${url}/discounts/${id}/delete`),
    create: (payload) => http.post(`${url}/discounts/create`,payload),
  },

  comments: { 
    all: (queries) => http.get(`${url}/comments?${ queries ? queries : '' }`), 
    reply: (id,payload) => http.post(`${url}/comments/${id}/reply`,payload),
    delete: (id) => http.post(`${url}/discounts/${id}/delete`), 
  },

  links: { 
    all: (queries) => http.get(`${url}/links?${ queries ? queries : '' }`), 
    reply: (id,payload) => http.post(`${url}/links/${id}/reply`,payload),
    delete: (id) => http.post(`${url}/links/${id}/delete`), 
    create: (payload) => http.post(`${url}/links/create`, { ...payload })
  },

  cards: { 
    all: (queries) => http.get(`${url}/cards?${ queries ? queries : '' }`), 
    export: (queries) => http.get(`${url}/cards/export?${ queries ? queries : '' }`), 
    edit: (id,payload) => http.post(`${url}/cards/${id}/edit`,payload),
    delete: (id) => http.post(`${url}/cards/${id}/delete`),
    create: (payload) => http.post(`${url}/cards/create`, { ...payload }),
    charge: (id,payload) => http.post(`${url}/cards/${id}/charge`,payload),
  },

  notes: { 
    all: (oid, route) => http.get(`${url}/${route}/${oid}/notes`), 
    create: (payload) => http.post(`${url}/notes/create`, { ...payload }),
    edit: (id, payload) => http.post(`${url}/notes/${id}/edit`, { ...payload }),
    delete: (id) => http.post(`${url}/notes/${id}/delete`),
  },

  settings: { 
    get: () => http.get(`${url}/settings`),
    save: (payload) => http.post(`${url}/settings`, { ...payload }),
  },

};

export default adminRoutes;
