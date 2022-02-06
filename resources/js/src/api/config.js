import Cookies from 'js-cookie';
import Store from '../store';
import Router from '../router';
import axios from 'axios';
import { addMsgId, msgIdExist } from '@/global/Functions';

const URL_PREFIX = '/api';
const APP_VERSION_META = document.querySelector('meta[name="app_version"]');
const APP_VERSION = APP_VERSION_META
  ? APP_VERSION_META.getAttribute('content')
  : '';

// Axios Instance
// ---------------------------------------
const httpClient = axios.create({
  // eslint-disable-next-line no-undef
  baseURL: URL_PREFIX,
  headers: {
    'Content-Type': 'application/json',
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Methods': 'GET, POST',
    'Access-Control-Allow-Headers':
      'Origin, Content-Type, X-Auth-Token, Authorization',
    'Cache-Control': 'no-cache, no-store',
    'X-CSRF-TOKEN': document
      .querySelector('meta[name="csrf-token"]')
      .getAttribute('content'),
    'App-Version': APP_VERSION
  }
});

/**
 * ------------------------------
 * Responses Interceptor
 */
const responseInterceptor = response => {
  const {
    force_update,
    clear_meta_cookies,
    clear_reg_cookies,
    custom_message,
    action_type,
    error,
    result
  } = response.data;

  if (action_type && action_type === 'open_url') {
    window.open(result.url);
  }

  /**
   * Show errors
   */
  if (error) {
    window.vm.$alerts.show({
      msg: error.message,
      type: 'danger',
      style: 'float'
    });
  }

  /**
   * Reload if gets
   * force_update
   */
  if (force_update) {
    window.location.reload();
  }

  /**
   * Clear meta in
   * cookies if gets
   * clear_meta_cookies
   */
  if (clear_meta_cookies) {
    Cookies.remove('meta');
  }

  /**
   * Clear register
   * cookies if gets
   * clear_reg_cookies
   */
  if (clear_reg_cookies) {
    Cookies.remove('meta');
    Cookies.remove('utm');
    Cookies.remove('rfrr');
  }

  /**
   * Show custom
   * message
   */
  if (custom_message) {
    const {
      msg,
      msg_title,
      msg_id,
      type,
      style,
      duration,
      cookie_aware,
      icon,
      buttons
    } = custom_message;

    if (cookie_aware) {
      addMsgId(msg_id);
      if (!msgIdExist(msg_id)) {
        window.vm.$alerts.show({
          msg: msg,
          msg_title: msg_title,
          type: type,
          style: style,
          icon: icon,
          duration: duration,
          buttons: buttons
        });
      }
    } else {
      window.vm.$alerts.show({
        msg: msg,
        msg_title: msg_title,
        type: type,
        style: style,
        icon: icon,
        duration: duration,
        buttons: buttons
      });
    }
  }

  return response;
};

/**
 * ------------------------------
 * Requests Errors Interceptor
 */
const errorInterceptor = error => {
  const { status } = error.response;

  if (status == 401) {
    /**
     * If user is not authenticated
     * this will clear access_token
     * and change the route to login
     */
    Store.dispatch('auth/logout').then(() => {
      Router.push('/login');
    });
  } else {
    return Promise.reject(error);
  }
};

/**
 * ------------------------------
 * Requests Interceptor
 */
const requestInterceptor = config => {
  /**
   * Get access_token
   * from cookies
   */
  let token = Cookies.get('access_token') ? Cookies.get('access_token') : null;

  /**
   * Set access_token
   * to the http request
   * headers
   */
  config.headers.Authorization = token ? `Bearer ${token}` : null;

  /**
   * Pass meta cookie
   * if it is in cookie
   */
  const APP_META_OBJECT = Cookies.get('meta');
  config.headers['App-Meta'] = APP_META_OBJECT ? APP_META_OBJECT : null;

  return config;
};

/**
 * ------------------------------
 * Response Interceptor Instance
 */
httpClient.interceptors.response.use(responseInterceptor, errorInterceptor);

/**
 * ------------------------------
 * Requests Interceptor Instance
 */
httpClient.interceptors.request.use(requestInterceptor);

export default httpClient;
