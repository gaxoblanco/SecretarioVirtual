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
    if(credentials.InputEmail1 == this.user.InputEmail1 ){
      this.LogState = true;
      console.log("logeado");
      this.routerModul ="['/listaExpediente']"
    }
    console.log(credentials, this.user)

  }
  logOut(){
    this.LogState = false;
  }
}
