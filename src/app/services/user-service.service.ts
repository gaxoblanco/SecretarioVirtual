import { Injectable, Output, EventEmitter, OnChanges } from '@angular/core';
import { BehaviorSubject, Observable, map, tap } from 'rxjs';
import {
  Additional,
  newAdditionalDTO,
  UpAdditionalDTO,
} from '../models/additional-model';
import { User } from '@models/login-model';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '@env/environment';
import { Router } from '@angular/router';
import { TokenService } from './token.service';
import { checkToken } from '../interceptors/token.interceptor';

@Injectable({
  providedIn: 'root',
})
export class UserServiceService {
  // url de API
  apiUrl = environment.API_URL;

  list: any = [
    {
      id: 1,
      name: 'gaston',
      email: 'gaston@blanco.com',
    },
    {
      id: 2,
      name: 'manonitlo',
      email: 'mano@lito.com',
    },
  ];

  @Output() newAdd = new EventEmitter<newAdditionalDTO>();

  constructor(
    private http: HttpClient,
    private router: Router,
    private tokenService: TokenService
  ) {}

  // variable userGlobal
  user$ = new BehaviorSubject<User>({
    email: '',
    password: '',
    firstName: '',
    lastName: '',
    subscribe: '',
  });
  // varaible listSecreataryes
  listSecreataryes$ = new BehaviorSubject<Additional[]>([]);

  // obtengo los datos del usuario
  getProfile(): Observable<User> {
    return this.http
      .get<User>(`${this.apiUrl}/user/get`, { context: checkToken() })
      .pipe(
        tap((user) => {
          console.log('user', user);
          this.user$.next(user);
        })
      );
  }
  // editop el usuario
  editProfile(user: User): Observable<User> {
    return this.http
      .post<any>(`${this.apiUrl}/user/update`, user, { context: checkToken() })
      .pipe(
        tap(() => {
          this.getProfile().subscribe();
        })
      );
  }

  getAllAdditional(): Observable<Additional[]> {
    return this.http
      .get<any>(`${this.apiUrl}/user/secretary/get`, { context: checkToken() })
      .pipe(
        map((response) => response.data), // Aquí obtenemos directamente el arreglo 'data'
        tap((data) => this.listSecreataryes$.next(data))
      );
  }

  addNewAdditional(addEmail: UpAdditionalDTO) {
    // solicitud post a la API
    console.log('addEmail', addEmail);

    return this.http
      .post<any>(`${this.apiUrl}/user/secretary/create`, addEmail, {
        context: checkToken(),
      })
      .pipe(
        // devuelvo el contenido de data del response
        map((response) => response.data),
        // actualizo listSecreataryes$
        tap(() => this.getAllAdditional().subscribe())
      );
  }

  upAdditional(edition: UpAdditionalDTO) {
    console.log('edition', edition);

    return this.http
      .post<any>(`${this.apiUrl}/user/secretary/update`, edition, {
        context: checkToken(),
      })
      .pipe(
        // actualizo listSecreataryes$
        tap(() => this.getAllAdditional().subscribe())
      );
  }

  deletAdditional(delet: any) {
    // post a la apiUrl/user/secretary/delete y procesamos si fue exitoso o no la respuesta
    console.log('delet', delet);

    return this.http
      .post<any>(`${this.apiUrl}/user/secretary/delete`, delet, {
        context: checkToken(),
      })
      .pipe(
        // actualizo listSecreataryes$
        tap(() => this.getAllAdditional().subscribe())
      );
  }
  getListSecreataryes$(): Observable<Additional[]> {
    return this.listSecreataryes$.asObservable();
  }
  getUser$(): Observable<User> {
    return this.user$.asObservable();
  }
}
