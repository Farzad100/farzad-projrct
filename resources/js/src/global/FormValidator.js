import { extend } from 'vee-validate';
import { required, regex } from 'vee-validate/dist/rules';

extend('required', {
  ...required,
  message: '{_field_} خود را وارد کنید'
});

extend('regex', {
  ...regex,
  message: '{_field_} صحیح نیست'
});

extend('mobileCheck', value => {
  if (value && value.length >= 10 && value.length <= 11) {

    const persianNumbers = [/۰/g, /۱/g, /۲/g, /۳/g, /۴/g, /۵/g, /۶/g, /۷/g, /۸/g, /۹/g];
    const arabicNumbers = [/٠/g, /١/g, /٢/g, /٣/g, /٤/g, /٥/g, /٦/g, /٧/g, /٨/g, /٩/g];

    // Replace non-english numbers with eng numbers
    if (typeof value === 'string') {
      for (let i = 0; i < 10; i++) {
        value = value
          .replace(persianNumbers[i], i)
          .replace(arabicNumbers[i], i);
      }
    }

    const mobileRegex = RegExp(
      /(0|\+98)?([ ]|-|[()]){0,2}9[0|1|2|3|4|9]([ ]|-|[()]){0,2}(?:[0-9]([ ]|-|[()]){0,2}){8}/gi
    );
    if (mobileRegex.test(value)) return true;
  }
  return '{_field_} صحیح نیست';
});

extend('nid', value => {
  const persianNumbers = [
    /۰/g,
    /۱/g,
    /۲/g,
    /۳/g,
    /۴/g,
    /۵/g,
    /۶/g,
    /۷/g,
    /۸/g,
    /۹/g
  ];
  const arabicNumbers = [
    /٠/g,
    /١/g,
    /٢/g,
    /٣/g,
    /٤/g,
    /٥/g,
    /٦/g,
    /٧/g,
    /٨/g,
    /٩/g
  ];

  // Replace non-english numbers with eng numbers
  if (typeof value === 'string') {
    for (let i = 0; i < 10; i++) {
      value = value.replace(persianNumbers[i], i).replace(arabicNumbers[i], i);
    }
  }

  if (value.length === 10) {
    if (
      inArray(value, [
        '1111111111',
        '2222222222',
        '3333333333',
        '4444444444',
        '5555555555',
        '6666666666',
        '7777777777',
        '8888888888',
        '9999999999',
        '0000000000'
      ])
    ) {
      return false;
    } else {
      let c = parseInt(value.charAt(9));
      let n =
        parseInt(value.charAt(0)) * 10 +
        parseInt(value.charAt(1)) * 9 +
        parseInt(value.charAt(2)) * 8 +
        parseInt(value.charAt(3)) * 7 +
        parseInt(value.charAt(4)) * 6 +
        parseInt(value.charAt(5)) * 5 +
        parseInt(value.charAt(6)) * 4 +
        parseInt(value.charAt(7)) * 3 +
        parseInt(value.charAt(8)) * 2;
      let r = n - parseInt(n / 11) * 11;
      if ((r == 0 && r == c) || (r == 1 && c == 1) || (r > 1 && c == 11 - r)) {
        return true;
      }
    }
  }
  return '{_field_} صحیح نیست';
});

extend('nic', value => {
  if (value.length === 11) {
    return true;
  }
  return '{_field_} صحیح نیست';
});

extend('isFa', value => {
  var ltrChars =
      'A-Za-z\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02B8\u0300-\u0590\u0800-\u1FFF' +
      '\u2C00-\uFB1C\uFDFE-\uFE6F\uFEFD-\uFFFF',
    rtlChars = '\u0591-\u07FF\uFB1D-\uFDFD\uFE70-\uFEFC',
    rtlDirCheck = new RegExp('^[^' + ltrChars + ']*[' + rtlChars + ']');

  if (rtlDirCheck.test(value)) {
    return true;
  }
  return '{_field_} باید فارسی باشد';
});

extend('ec', value => {
  if (value.length === 12) {
    return true;
  }
  return '{_field_} صحیح نیست';
});

extend('rn', value => {
  if (value.length === 6) {
    return true;
  }
  return '{_field_} صحیح نیست';
});

extend('cardNumber', value => {
  if (value.length === 16) {
    return true;
  }
  return '{_field_} صحیح نیست';
});

extend('postCode', value => {
  //var reg = /^\d+$/;
  
  if (value.length === 10) {
    return true;
  }
  return '{_field_} صحیح نیست';
});

extend('phone', value => {
  if (value.length === 11) {
    return true;
  }
  return '{_field_} صحیح نیست';
});

extend('sayadi', value => {
  if (value.length === 16) {
    return true;
  }
  return '{_field_} صحیح نیست';
});

extend('sheba', value => {
  if (value.length === 24) {
    return true;
  }
  return '{_field_} صحیح نیست';
});

extend('chequeNumber', value => {
  if (value.length === 6) {
    return true;
  }
  return '{_field_} باید ۶ رقم باشد';
});


const inArray = (member, array) => {
  let length = array.length;
  for (let i = 0; i < length; i++) {
    if (array[i] === member) return true;
  }
  return false;
};

export default {};
