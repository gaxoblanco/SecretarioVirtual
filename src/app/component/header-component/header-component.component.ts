import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { AutenticationServiceService } from 'src/app/services/autentication-service.service';
import {Route} from '../../models/route';

@Component({
  selector: 'app-header-component',
  templateUrl: './header-component.component.html',
  styleUrls: ['./header-component.component.scss']
})
export class HeaderComponentComponent implements OnInit {

  @Input() page: Route={
    route: '',
    name: '',
    acess: false,
  };
  @Output() addedPage = new EventEmitter<Route>();


  pages: Route[] =[
    {
      route: "/agregarExpediente",
      name: 'Agregar',
      acess: false,
    },
    {
      route: "/listaExpediente",
      name: 'Lista',
      acess: false,
    },
    {
      route: "/usuario",
      name: 'Usuario',
      acess: false,
    },
    {
      route: "/",
      name: 'Desconectar',
      acess: false,
    },
  ];

  constructor(
    private autenticacionService: AutenticationServiceService,
  ) { }

  ngOnInit(): void {
    // if (this.autenticacionService.LogState =! this.autenticacionService.LogState){

    // }
  }

}
