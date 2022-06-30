import { Injectable } from '@angular/core';
import {BehaviorSubject, Observable} from 'rxjs';
import { LoginModel } from '../models/login-model';
import { RouterLink, RouterModule, Routes } from '@angular/router';
import { AppRoutingModule } from '../app-routing.module';
import {Route} from '../models/route';
import { HeaderComponentComponent } from '../component/header-component/header-component.component';

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

  user = {
    emailP: "a",
    password: "a",
    name: "Juan",
    surname: "Manuel",
    subscribe: 'Mensual'
  }

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
  ) { }

  login(value: LoginModel){

    if (value.emailP == this.user.emailP && value.password == this.user.password){
     // this.pages[0].acess = true;
      this.pages.forEach(item => item.acess = true);
      this.filterPages(true)
      console.log(this.pages);
    }
    console.log("mal", value);

  }

  changePassword(value: LoginModel){
    this.user.password = value.password;
    console.log(this.user);

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
