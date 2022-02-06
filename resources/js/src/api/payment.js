import http from './config';

const url = '/payments';

const payment = {

  check: {
    result: (payload) => http.get(`${url}/result/${payload}`),
    transaction: (oid, id) => http.get(`/admin/orders/${oid}/payment/check/${id}`)
  }

};

export default payment;
