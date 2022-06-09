import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AddFileScreenComponent } from './screen/add-file-screen/add-file-screen.component';
import { HomeScreenComponent } from './screen/home-screen/home-screen.component';
import { PendingListScreenComponent } from './screen/pending-list-screen/pending-list-screen.component';
import { UserScreenComponent } from './screen/user-screen/user-screen.component';

const routes: Routes = [
  {path:'', component: HomeScreenComponent},
  {path:'agregarExpediente', component: AddFileScreenComponent},
  {path:'listaExpediente', component: PendingListScreenComponent},
  {path: 'usuario', component: UserScreenComponent},
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
