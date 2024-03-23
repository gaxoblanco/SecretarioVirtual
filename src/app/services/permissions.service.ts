import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';
import { TokenService } from './token.service';
import { Route } from '@models/route';

@Injectable({
  providedIn: 'root',
})
export class PermissionsService {
  private permissionsSubject: BehaviorSubject<Route[]> = new BehaviorSubject<
    Route[]
  >([]);

  constructor(private tokenService: TokenService) {
    // Inicializa los permisos en el constructor.
    this.updatePermissions();
  }

  pages: Route[] = [
    {
      route: '/',
      name: 'Home',
      acess: true,
    },
    {
      route: '/login',
      name: 'Login',
      acess: true,
    },
    {
      route: '/agregarExpediente',
      name: 'Agregar',
      acess: false,
    },
    {
      route: '/listaExpediente',
      name: 'Lista',
      acess: false,
    },
    {
      route: '/usuario',
      name: 'Usuario',
      acess: false,
    },
    {
      route: '/',
      name: 'Desconectar',
      acess: false,
    },
  ];

  // Método para actualizar los permisos basados en el token.
  updatePermissions() {
    // Obtiene el token.
    const token = this.tokenService.getToken();
    if (!token) {
      this.permissionsSubject.next(
        this.pages.filter((page) => page.acess === true)
      );
    } else {
      this.permissionsSubject.next(
        this.pages.filter((page) => page.acess === false)
      );
    }
  }

  // Método para obtener el Observable de permisos.
  filterPages(): Observable<Route[]> {
    return this.permissionsSubject.asObservable();
  }
}
