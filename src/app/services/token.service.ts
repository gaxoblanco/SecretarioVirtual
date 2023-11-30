import { Injectable } from '@angular/core';
import { ResponseLogin } from '@models/auth.model';
import { getCookie, setCookie, removeCookie } from 'typescript-cookie';

@Injectable({
  providedIn: 'root',
})
export class TokenService {
  constructor() {}

  saveToken(token: ResponseLogin) {
    // guardamos el response.token y response.id en una cookie
    // document.cookie = `token=${token.token}; id=${token.id}`;
    setCookie('token', token.token, { expires: 30, path: '/' });
    setCookie('userId', token.id, { expires: 30, path: '/' });
    // console.log('saveToken', token.token, token.id);
  }

  getToken() {
    const token = getCookie('token');
    const id = getCookie('userId');

    if (!token || !id) {
      return null;
    }
    console.log('getToken', token, id);

    return { token, id };
  }

  removeToken() {
    removeCookie('token');
    removeCookie('userId');
  }
}
