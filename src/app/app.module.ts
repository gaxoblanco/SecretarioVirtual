import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { FormsModule } from '@angular/forms';
import { ReactiveFormsModule} from '@angular/forms';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HomeScreenComponent } from './screen/home-screen/home-screen.component';
import { AddFileScreenComponent } from './screen/add-file-screen/add-file-screen.component';
import { PendingListScreenComponent } from './screen/pending-list-screen/pending-list-screen.component';
import { UserScreenComponent } from './screen/user-screen/user-screen.component';
import { HeaderComponentComponent } from './component/header-component/header-component.component';
import { FooterComponentComponent } from './component/footer-component/footer-component.component';
import { ButtonComponentComponent } from './component/button-component/button-component.component';
import { MenuComponentComponent } from './component/menu-component/menu-component.component';
import { CarouselComponentComponent } from './component/carousel-component/carousel-component.component';
import { FrequentQuestionsComponenComponent } from './component/frequent-questions-componen/frequent-questions-componen.component';
import { SubcribeCardComponentComponent } from './component/subcribe-card-component/subcribe-card-component.component';
import { UserComponenetComponent } from './component/user-componenet/user-componenet.component';
import { NewUserComponentComponent } from './component/new-user-component/new-user-component.component';
import { EditUserComponentComponent } from './component/edit-user-component/edit-user-component.component';
import { EditEmailComponentComponent } from './component/edit-email-component/edit-email-component.component';
import { LoginComponenetComponent } from './component/login-componenet/login-componenet.component';
import { RegisterComponenetComponent } from './component/register-componenet/register-componenet.component';
import { LoginScreenComponent } from './screen/login-screen/login-screen.component';
import { RegisterScreenComponent } from './screen/register-screen/register-screen.component';
import { ResetPwScreenComponent } from './screen/reset-pw-screen/reset-pw-screen.component';
import { SubcribeScreenComponent } from './screen/subcribe-screen/subcribe-screen.component';
import { SubcribeOptionComponentComponent } from './component/subcribe-option-component/subcribe-option-component.component';
import { PendingFileComponenetComponent } from './component/pending-file-componenet/pending-file-componenet.component';

@NgModule({
  declarations: [
    AppComponent,
    HomeScreenComponent,
    AddFileScreenComponent,
    PendingListScreenComponent,
    UserScreenComponent,
    HeaderComponentComponent,
    FooterComponentComponent,
    ButtonComponentComponent,
    MenuComponentComponent,
    CarouselComponentComponent,
    FrequentQuestionsComponenComponent,
    SubcribeCardComponentComponent,
    UserComponenetComponent,
    NewUserComponentComponent,
    EditUserComponentComponent,
    EditEmailComponentComponent,
    LoginComponenetComponent,
    RegisterComponenetComponent,
    LoginScreenComponent,
    RegisterScreenComponent,
    ResetPwScreenComponent,
    SubcribeScreenComponent,
    SubcribeOptionComponentComponent,
    PendingFileComponenetComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    FormsModule,
    ReactiveFormsModule,
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
