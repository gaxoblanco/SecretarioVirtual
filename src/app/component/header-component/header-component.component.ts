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
  // @Input() page: Route={
  //   route: '',
  //   name: '',
  //   acess: false,
  // };
  // @Output() addedPage = new EventEmitter<Route>();

  pages: Route[] = [];

  constructor(
    private autServ: AutenticationServiceService,
    private permissions: PermissionsService
  ) {}

  ngOnInit(): void {
    // me susbribo al observable filterPages
    // this.pages = this.autServ.filterPages();
    this.permissions.filterPages().subscribe((permissions) => {
      this.pages = permissions;
    });
  }

  logout() {
    this.autServ.logout();
  }
}
