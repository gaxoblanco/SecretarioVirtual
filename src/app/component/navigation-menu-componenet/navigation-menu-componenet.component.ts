import { Component, OnInit } from '@angular/core';
import { AutenticationServiceService } from 'src/app/services/autentication-service.service';
import { Route } from '../../models/route';
import { PermissionsService } from '@services/permissions.service';

@Component({
  selector: 'app-navigation-menu-componenet',
  templateUrl: './navigation-menu-componenet.component.html',
  styleUrls: ['./navigation-menu-componenet.component.scss'],
})
export class NavigationMenuComponenetComponent implements OnInit {
  pages$: Route[] = []; // el estado inicial obligatorio es un array vacio
  menu = false;

  constructor(
    private autServ: AutenticationServiceService,
    private permissions: PermissionsService
  ) {}

  ngOnInit(): void {
    this.permissions.filterPages().subscribe((permissions) => {
      this.pages$ = permissions;
    });
  }

  menuStatus() {
    this.menu = !this.menu;
  }
}
