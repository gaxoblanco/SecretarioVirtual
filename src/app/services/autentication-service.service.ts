import { Injectable } from '@angular/core';
import {
  BehaviorSubject,
  Observable,
  catchError,
  map,
  of,
  tap,
  throwError,
} from 'rxjs';
import { LoginModel, User } from '@models/login-model';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AppRoutingModule } from '../app-routing.module';
import { Router } from '@angular/router';
import { Route } from '@models/route';
import { ResponseLogin } from '@models/auth.model';
import { environment } from '@env/environment';
import { TokenService } from './token.service';
import { PermissionsService } from './permissions.service';
import { checkToken } from '../interceptors/token.interceptor';

@Injectable({
  providedIn: 'root',
})
export class AutenticationServiceService {
  LogState = false;

  constructor(
    private routerModul: AppRoutingModule,
    private http: HttpClient,
    private router: Router,
    private tokenService: TokenService,
    private permissions: PermissionsService
  ) {}

  // url de trabajo
  apiUrl = environment.API_URL;
  tokenTrue = this.tokenService.getToken() || null;

  login(value: LoginModel) {
    // Configuro el header para enviar email y password
    const headers = new HttpHeaders({
      email: value.email, // Agregar el email en las cabeceras
      password: value.password, // Agregar la contraseña en las cabeceras
    });
    // console.log('header', headers);

    // Realiza la solicitud POST a la API para el inicio de sesión
    return this.http
      .post<ResponseLogin>(`${this.apiUrl}/user/login`, null, { headers })
      .pipe(
        map((response: any) => {
          console.log('Usuario logeado:', response);
          // Guarda el token utilizando el TokenService
          this.tokenService.saveToken(response);
          return true;
        }),
        catchError((error: any) => {
          console.log('Error al crear usuario:', error);
          return of(false); // Devuelve un observable booleano con valor false
        })
      );
  }

  register(value: any) {
    //envio el formulario de registro en un post
    // console.log('envio registro', value);
    return this.http.post(`${this.apiUrl}/user/create`, value).pipe(
      //proceso la respuesta
      map(
        (response: any) => {
          // console.log('creando', response == 'Usuario creado correctamente');
          if (response.status === 200) {
            return (response = true);
          }

          return (response = false);
        },
        catchError((error: any): Observable<any> => {
          console.log('Error al crear usuario:', error);
          return of(false); // Return an Observable with value false in case of error
        })
      )
    );
  }

  // post para realizar cambio de password
  changePassword(value: LoginModel) {
    console.log('cambio password', value);

    // envío el value ($password) en el header
    const headers = {
      password: value.toString(),
    };
    console.log('header', headers);

    // hago un post enviando el checkToken y el headers
    return this.http
      .post(`${this.apiUrl}/user/password-change`, null, {
        headers: headers,
        context: checkToken(),
      })
      .pipe(
        // Procesamos la respuesta
        tap(
          (response) => {
            console.log(response);
            // Navegamos a la página principal
            // this.router.navigate(['/']);
          },
          (error) => {
            // Manejamos errores aquí
            console.error('Error en la solicitud:', error);
          }
        )
      );
  }

  logout() {
    // borro la cookie
    this.tokenService.removeToken();
    // cambio el estado del login
    this.LogState = false;
    // console.log(this.tokenService.getToken());
    // actualizo las rutas
    this.permissions.updatePermissions();
    // navego a la pagina principal
    this.router.navigate(['/']);
  }
}
