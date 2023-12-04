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
import { map } from 'rxjs/operators';

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
    // console.log(data);

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
          if (response === 'null') {
            return false;
          }
          // si es distinto devuelvo un error
          return response;
        }),
        catchError((error) => {
          return throwError(error.error.message || 'Server error');
        })
      );
  }

  // sabiendo que dispatch/get devuelve:
  // {
  //   "0": 200,
  //   "dispatches": [
  //       {
  //           "id_exp": 3,
  //           "id_lista_despacho": 15754,
  //           "numero_exp": 948,
  //           "anio_exp": 19,
  //           "caratula": "ALCALA, Francisco Rene C/ VENICA, Diamela Luisa S/ Juicio Ejecutivo",
  //           "reservado": 0,
  //           "dependencia": "7441513",
  //           "tipo_lista": "1",
  //           "id_user": 2
  //       },

  getFiles(): Observable<FileModel[]> {
    return this.http
      .get<any[]>(`${this.apiUrl}/dispatch/get`, {
        context: checkToken(),
      })
      .pipe(
        tap((response) => {
          console.log('files', response);
          if (response != null) {
            this.files$.next(response);
            this.upDependencia();
          }
          return response;
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

  // delete file by id -- falta mejorar para usar delete
  deleteFiles(id: number) {
    const dispatchId = { dispatchId: id };
    console.log('dispatchId', dispatchId);

    return this.http
      .post(`${this.apiUrl}/dispatch/delete`, dispatchId, {
        context: checkToken(),
      })
      .pipe(
        // proceso la respuesta
        tap((response) => {
          // si la respuesta es Expediente eliminado con exito. devuelvo un true
          if (response === 'Expediente eliminado con exito.') {
            return true;
          }
          // si la respuesta es "El expediente no existe." devuelvo false
          if (response === 'El expediente no existe.') {
            return false;
          }
          // si es distinto devuelvo un error
          return throwError(response);
        })
      );
  }
}
