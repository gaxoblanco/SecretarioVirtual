import { Injectable } from '@angular/core';
import {BehaviorSubject, Observable, catchError, tap} from 'rxjs';
import { LoginModel } from '../models/login-model';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AppRoutingModule } from '../app-routing.module';
import { Router } from '@angular/router';
import {Route} from '@models/route';
import { environment } from '@env/environment';

@Injectable({
  providedIn: 'root'
})
export class AutenticationServiceService {
  LogState = false;
  pageFilter: Route[] = [
    {
      route: "/",
      name: 'Home',
      acess: true,
    },
    {
      route: "/login",
      name: 'Login',
      acess: true,
    },
  ];

  pages: Route[] =[
    {
      route: "/",
      name: 'Home',
      acess: true,
    },
    {
      route: "/agregarExpediente",
      name: 'Agregar',
      acess: true,
    },
    {
      route: "/listaExpediente",
      name: 'Lista',
      acess: true,
    },
    {
      route: "/usuario",
      name: 'Usuario',
      acess: false,
    },
    {
      route: "/",
      name: 'Desconectar',
      acess: true,
    },
  ];

  constructor(
    private routerModul : AppRoutingModule,
    private http: HttpClient,
    private router: Router
  ) { }

  // url de trabajo
  apiUrl = environment.API_URL;

  login(value: LoginModel){
    // configuro el hader para enviar email y password
    const headers = new HttpHeaders({
      // authorization: 'Basic ' + btoa(value.email + ':' + value.password),
      'email': value.email, // Agregar el email en las cabeceras
      'password': value.password // Agregar la contraseña en las cabeceras
    });
    console.log('header',headers);

    // Realiza la solicitud POST a la API para el inicio de sesión
    return this.http.post(`${this.apiUrl}/user/login`, null, { headers })
      .pipe(
        // procesamos la respeusta
        tap(response => {
          // guardamos el token en el localstorage
          localStorage.setItem('token', JSON.stringify(response));
          // cambiamos el estado del login
          this.LogState = true;
          // navego a la pagina principal
          this.router.navigate(['/']);
        }
      ))
  }

  changePassword(value: LoginModel){

  }

  filterPages(value: Boolean){
    if(value == true){
      const array = this.pages;
      const filter = array.filter(item => item.acess);
      this.pageFilter = filter;
      console.log(filter, 'hola');
    }
  }
}
