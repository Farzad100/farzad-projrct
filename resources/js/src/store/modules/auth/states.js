import Cookies from 'js-cookie';

export default {
  user: {},
  access_token: Cookies.get('access_token') ?? '',
  roles: Cookies.get('roles') ? JSON.parse(Cookies.get('roles')) : []
};
