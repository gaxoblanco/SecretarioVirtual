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
import { JusticeComponent } from './screen/justice/justice.component';

import { AuthGuard } from './guards/auth.guard';
import { ResetPasswordComponent } from '@screen/reset-password/reset-password.component';

const routes: Routes = [
  { path: '', component: HomeScreenComponent },
  {
    path: 'agregarExpediente',
    component: AddFileScreenComponent,
    canActivate: [AuthGuard],
  },
  {
    path: 'listaExpediente',
    component: PendingListScreenComponent,
    canActivate: [AuthGuard],
  },
  { path: 'usuario', component: UserScreenComponent, canActivate: [AuthGuard] },
  { path: 'login', component: LoginScreenComponent },
  { path: 'register', component: RegisterScreenComponent },
  // {path: 'resetpw', component: ResetPwScreenComponent},
  { path: 'subcribe', component: SubcribeScreenComponent },
  { path: 'juzgado', component: JusticeComponent },
  { path: 'reset-password/:token/:email', component: ResetPasswordComponent },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule],
})
export class AppRoutingModule {
  [x: string]: any;
}
