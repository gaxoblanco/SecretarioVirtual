import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable, catchError, tap } from 'rxjs';
import { LoginModel, User } from '@models/login-model';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AppRoutingModule } from '../app-routing.module';
import { Router } from '@angular/router';
import { Route } from '@models/route';
import { ResponseLogin } from '@models/auth.model';
import { environment } from '@env/environment';
import { TokenService } from './token.service';
import { PermissionsService } from './permissions.service';

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
    // configuro el hader para enviar email y password
    const headers = new HttpHeaders({
      email: value.email, // Agregar el email en las cabeceras
      password: value.password, // Agregar la contraseña en las cabeceras
    });
    console.log('header', headers);

    // Realiza la solicitud POST a la API para el inicio de sesión
    return this.http
      .post<ResponseLogin>(`${this.apiUrl}/user/login`, null, { headers })
      .pipe(
        // procesamos la respeusta
        tap((response) => {
          console.log(response);
          // guardamos el response.token y response.id en una cookie
          this.tokenService.saveToken(response);
          // actualizo las rutas
          this.permissions.updatePermissions();
          // cambiamos el estado del login
          this.LogState = true;
          // navego a la pagina principal
          this.router.navigate(['/login']);
        })
      );
  }

  register(value: any) {
    //envio el formulario de registro en un post
    // console.log('envio registro', value);
    return this.http.post(`${this.apiUrl}/user/create`, value).pipe();
  }

  changePassword(value: LoginModel) {}

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
    console.log(this.tokenService.getToken());
    // actualizo las rutas
    this.permissions.updatePermissions();
    // navego a la pagina principal
    this.router.navigate(['/']);
  }
}
