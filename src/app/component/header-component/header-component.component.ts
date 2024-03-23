import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { AutenticationServiceService } from 'src/app/services/autentication-service.service';
import { PermissionsService } from 'src/app/services/permissions.service';
import { Route } from '../../models/route';

@Component({
  selector: 'app-header-component',
  templateUrl: './header-component.component.html',
  styleUrls: ['./header-component.component.scss'],
})
export class HeaderComponentComponent implements OnInit {
  pages$: Route[] = []; // el estado inicial obligatorio es un array vacio
  isLoggedIn = false;
  pageFilter: Route[] | undefined;

  constructor(
    private autServ: AutenticationServiceService,
    private permissions: PermissionsService
  ) {}

  ngOnInit(): void {
    // me susbribo al observable filterPages
    // this.pages = this.autServ.filterPages();
    this.permissions.filterPages().subscribe((permissions) => {
      this.pages$ = permissions;
    });
  }

  logout() {
    this.autServ.logout();
  }
  updateRoutes(): void {
    // Actualiza las rutas disponibles según el estado de autenticación
    this.pages$ = this.isLoggedIn ? this.pageFilter ?? [] : this.pages$;
  }
}
