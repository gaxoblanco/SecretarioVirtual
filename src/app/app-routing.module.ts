import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AddFileScreenComponent } from './screen/add-file-screen/add-file-screen.component';
import { HomeScreenComponent } from './screen/home-screen/home-screen.component';
import { LoginScreenComponent } from './screen/login-screen/login-screen.component';
import { PendingListScreenComponent } from './screen/pending-list-screen/pending-list-screen.component';
import { RegisterScreenComponent } from './screen/register-screen/register-screen.component';
import { ResetPwScreenComponent } from './screen/reset-pw-screen/reset-pw-screen.component';
import { SubcribeScreenComponent } from './screen/subcribe-screen/subcribe-screen.component';
import { UserScreenComponent } from './screen/user-screen/user-screen.component';

const routes: Routes = [
  {path:'', component: HomeScreenComponent},
  {path:'agregarExpediente', component: AddFileScreenComponent},
  {path:'listaExpediente', component: PendingListScreenComponent},
  {path: 'usuario', component: UserScreenComponent},
  {path: 'login', component: LoginScreenComponent},
  {path: 'register', component: RegisterScreenComponent},
  {path: 'resetpw', component: ResetPwScreenComponent},
  {path: 'subcribe', component: SubcribeScreenComponent},
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {
  [x: string]: any;
}
