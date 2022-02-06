import account from '@/api/account';
import admin from '@/api/admin';
import auth from '@/api/auth';
import custom from '@/api/custom';
import inbox from '@/api/inbox';
import orders from '@/api/orders';
import organ from '@/api/organ';
import payment from '@/api/payment';
import seller from '@/api/seller';
import upload from '@/api/upload';

const api = {
  Account: account,
  Admin: admin,
  ...auth,
  ...custom,
  ...inbox,
  Orders: orders,
  Organ: organ,
  ...payment,
  Shop: seller,
  ...upload
};

const wrapper = (promise, errorMessage) => (
  promise
    .then(data => data)
    .catch(() => {
      /**
       * Custom error messages
       * and other toast options
       */
      if (errorMessage) {
        window.vm.$alerts.show({
          msg: errorMessage,
          type: 'danger',
          style: 'float'
        });
      }

      return '';
    })
);

export { api, wrapper };
