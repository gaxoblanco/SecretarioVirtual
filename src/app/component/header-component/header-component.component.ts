import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { AutenticationServiceService } from 'src/app/services/autentication-service.service';
import {Route} from '../../models/route';

@Component({
  selector: 'app-header-component',
  templateUrl: './header-component.component.html',
  styleUrls: ['./header-component.component.scss']
})
export class HeaderComponentComponent implements OnInit {

  // @Input() page: Route={
  //   route: '',
  //   name: '',
  //   acess: false,
  // };
  // @Output() addedPage = new EventEmitter<Route>();


  pages: Route[] =[];

  constructor(
    private autServ: AutenticationServiceService,
  ) { }

  ngOnInit(): void {
   // this.pages = this.autServ.pageFilter;
    this.pages = this.autServ.pages;
    console.log("llama mas veces");

  }


}
