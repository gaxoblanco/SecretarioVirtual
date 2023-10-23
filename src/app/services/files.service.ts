import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable, catchError, tap, throwError } from 'rxjs';
import { Router } from '@angular/router';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { FileModel, NewFile } from '@models/file.model';
import { environment } from '@env/environment';
import { AppRoutingModule } from '../app-routing.module';
import { TokenService } from './token.service';
import { checkToken } from '../interceptors/token.interceptor';
import { dependencias, Dependencia } from '@models/dependencias';

@Injectable({
  providedIn: 'root',
})
export class FilesService {
  files$ = new BehaviorSubject<FileModel[]>([]);
  // url de API
  apiUrl = environment.API_URL;

  constructor(
    private routerModul: AppRoutingModule,
    private http: HttpClient,
    private router: Router,
    private tokenService: TokenService
  ) {}

  // getAllFiles():Observable<FileModel[]>{

  // }

  addFiles(data: NewFile) {
    // Obtengo el token y id
    const token = this.tokenService.getToken();
    console.log(token!.id);
    console.log(data);

    return this.http
      .post(`${this.apiUrl}/dispatch/create`, data, { context: checkToken() })
      .pipe(
        // si es un success devuelvo el body
        tap((response) => {
          console.log(response);
          // si la respuesta es Expediente creado con exito. devuelvo un true
          if (response === 'Expediente creado con exito.') {
            return true;
          }
          // si la respuesta es "El expediente ya existe." devuelvo false
          if (response === 'El expediente ya existe.') {
            return false;
          }
          // si es distinto devuelvo un error
          return throwError(response);
        })
      );
  }

  getFiles(): Observable<FileModel[]> {
    return this.http
      .get<FileModel[]>(`${this.apiUrl}/dispatch/get`, {
        context: checkToken(),
      })
      .pipe(
        tap((files) => {
          this.files$.next(files);
          this.upDependencia();
        })
      );
  }
  // funcion para actualizar el valor de dependencia numero a nombre
  upDependencia() {
    const files = this.files$.getValue();
    files.forEach((file) => {
      const dependencia = dependencias.find(
        (dependencia) => dependencia.id === file.dependencia
      );
      file.dependencia = dependencia!.nombre;
    });
    console.log('newDependencia', files);

    this.files$.next(files);
  }

  getFiles$(): Observable<FileModel[]> {
    return this.files$.asObservable();
  }

  //----
  // deleteFiles(fileId: Number) {
  //   const position = this.files.findIndex((item) => item.id === fileId);
  //   this.files.splice(position, 1);
  //   console.log(position);
  // }
  // filter(number: Number) {
  //   this.files.find((item: FileModel) => item.numero_exp === number);
  //   console.log(this.files);
  // }
}
