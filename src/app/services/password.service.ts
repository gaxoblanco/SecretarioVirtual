import { Injectable } from '@angular/core';
import { environment } from '@env/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { TokenService } from './token.service';
import { catchError, map, tap } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class PasswordService {
  [x: string]: any;
  // url de API
  apiUrl = environment.API_URL;
  constructor(private http: HttpClient, private tokenService: TokenService) {}

  // creo la función para recuperar la contraseña recoverPassword
  recoverPassword(correo: string) {
    const data = { email: correo };
    console.log('data', data);

    //post a user/password-restart
    return this.http.post(`${this.apiUrl}/user/password-restart`, data).pipe(
      // procesa el tipo de respuesta
      map((response) => {
        // procesa la respuesta, si es http 202 quiere decir que salió todo bien pero no existe el correo
        if (response === 'email no existe') {
          return response;
        } else {
          return true;
        }
      }),
      catchError((error) => {
        console.log('error', error);
        return error;
      })
    );
  }

  // funcion para enviar la nueva contraseña
  resetPassword(correo: string, token: string, password: string) {
    const data = { email: correo, token: token, password: password };
    console.log('data', data);

    return this.http.post(`${this.apiUrl}/user/password-reset`, data).pipe(
      tap((response) => {
        console.log('respuesta', response);
      }),
      catchError((error) => {
        console.log('error', error);
        return error;
      })
    );
  }
}
