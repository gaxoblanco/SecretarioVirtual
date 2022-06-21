import { Injectable } from '@angular/core';
import {BehaviorSubject, Observable} from 'rxjs';
import { LoginModel } from '../models/login-model';
import { RouterLink, RouterModule, Routes } from '@angular/router';
import { AppRoutingModule } from '../app-routing.module';

@Injectable({
  providedIn: 'root'
})
export class AutenticationServiceService {
  user: LoginModel = {
    InputEmail1: "gaston@blanco.com",
    InputPassword: "blanco123",
  }
  LogState = false;

  constructor(
    private routerModul : AppRoutingModule,
  ) { }

  login(credentials:LoginModel) {
    if(
      credentials.InputEmail1 == this.user.InputEmail1 &&
      credentials.InputPassword == this.user.InputPassword){
      this.LogState = true;
      console.log("logeado creo que ya estaba ");
      this.routerModul ="['/listaExpediente']"
    }
    console.log('email o password mal')

  }
  logOut(){
    this.LogState = false;
  }
}
