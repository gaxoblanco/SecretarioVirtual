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
  // variable listAditional
  listAditional$ = new BehaviorSubject<Additional[]>([]);

  // obtengo los datos del usuario
  getProfile() {
    return this.http
      .get<User>(`${this.apiUrl}/user/get`, { context: checkToken() })
      .pipe(
        tap((user) => {
          console.log('user', user);

          this.user$.next(user);
        })
      );
  }

  getAllAdditional(): Observable<Additional[]> {
    return this.http
      .get<any>(`${this.apiUrl}/user/secretary/get`, { context: checkToken() })
      .pipe(
        // devuelvo el contenido de data del response
        map((response) => response.data)
      );
  }

  addNewAdditional(addEmail: UpAdditionalDTO) {
    addEmail.id = this.list.length + 1;
    this.list.push(addEmail);
    console.log('user service', this.list);
    return this.list;
  }

  upAdditional(edition: UpAdditionalDTO) {
    const edit = this.list.find(
      (item: UpAdditionalDTO) => item.id === edition.id
    );
    if (edition.id == edit.id) {
      edit.name = edition.firstName;
      edit.email = edition.Semail;
    }

    // this.list.push(edition);
    // console.log('user service', this.list)
    return this.list;
  }

  deletAdditional(delet: Number) {
    const edit = this.list.findIndex(
      (item: UpAdditionalDTO) => item.id === delet
    );
    this.list.splice(edit, edit + 1);
  }
}
