import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable, catchError, tap, throwError } from 'rxjs';
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
  pageFilter: Route[] = [
    {
      route: '/',
      name: 'Home',
      acess: true,
    },
    {
      route: '/login',
      name: 'Login',
      acess: true,
    },
    {
      route: '/listaExpediente',
      name: 'Lista',
      acess: true,
    },
  ];

  pages: Route[] = [
    {
      route: '/',
      name: 'Home',
      acess: true,
    },
    {
      route: '/login',
      name: 'Login',
      acess: true,
    },
    {
      route: '/agregarExpediente',
      name: 'Agregar',
      acess: true,
    },
    {
      route: '/listaExpediente',
      name: 'Lista',
      acess: true,
    },
    {
      route: '/usuario',
      name: 'Usuario',
      acess: false,
    },
    {
      route: '/',
      name: 'Desconectar',
      acess: true,
    },
  ];

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
        // Procesamos la respuesta
        tap(
          (response) => {
            console.log(response);

            // Guardamos el response.token y response.id en una cookie
            this.tokenService.saveToken(response);
            // Actualizamos las rutas
            this.permissions.updatePermissions();
            // Cambiamos el estado del login
            this.LogState = true;
            // Navegamos a la página principal
            this.router.navigate(['/login']);
          },
          (error) => {
            // Manejamos errores aquí
            console.error('Error en la solicitud:', error);
          }
        )
      );
  }

  register(value: any) {
    //envio el formulario de registro en un post
    // console.log('envio registro', value);
    return this.http
      .post(`${this.apiUrl}/user/create`, value)
      .pipe
      //proceso la respuesta
      ();
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

  filterPages() {
    // console.log(this.tokenTrue!.token);

    //si token existe  devuelvoel array pages
    if (this.tokenTrue!.token) {
      return this.pages;
    } else {
      // si no existe devuelvo el array pageFilter
      return this.pageFilter;
    }
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
