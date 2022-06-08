import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

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
    SubcribeCardComponentComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
