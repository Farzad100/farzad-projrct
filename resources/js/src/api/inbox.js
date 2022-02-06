import http from './config';

const url = '/inbox';

const inbox = {

  get: {
    all: (role) => http.get(`${role}${url}`),
    single: (role, id) => http.get(`${role}${url}/${id}`)
  },

  seen: (id) => http.post(`${url}/${id}/seen`),

  create: (payload) => http.post('/admin/inbox/create', { ...payload })

};

export default inbox;
