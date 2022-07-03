import { Component, OnInit } from '@angular/core';
import { AutenticationServiceService } from 'src/app/services/autentication-service.service';
import {Route} from '../../models/route';

@Component({
  selector: 'app-navigation-menu-componenet',
  templateUrl: './navigation-menu-componenet.component.html',
  styleUrls: ['./navigation-menu-componenet.component.scss']
})
export class NavigationMenuComponenetComponent implements OnInit {

  pages: Route[] =[];
  menu = false;

  constructor(
    private autServ: AutenticationServiceService,
  ) { }

  ngOnInit(): void {
   // this.pages = this.autServ.pageFilter;
    this.pages = this.autServ.pages;
    console.log("llama mas veces");

  }

  menuStatus(){
    this.menu = !this.menu;
  }

}
