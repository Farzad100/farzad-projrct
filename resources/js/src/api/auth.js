import http from './config';

const auth = {
  sendOtp : (payload) => http.post('/otp', { ...payload }),
  checkOtp: (payload) => http.post('/otpv', { ...payload }),
  register: (payload) => http.post('/register', { ...payload }),
  login   : (payload) => http.post('/login', { ...payload }),
  reset   : (payload) => http.post('/password-recovery', { ...payload }),
  
  sendOtpShop : (payload) => http.post('/otpx', { ...payload }),
};

export default auth;
