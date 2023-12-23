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
      //proceso el tipo de respuesta
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
