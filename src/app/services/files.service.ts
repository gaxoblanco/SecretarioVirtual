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
import { ExpData } from '@models/expData';

@Injectable({
  providedIn: 'root',
})
export class FilesService {
  files$ = new BehaviorSubject<FileModel[]>([]);
  // url de API
  apiUrl = environment.API_URL;
  // observable - data del expediente seleccionado/buscado
  fileSelected$ = new BehaviorSubject<any>(null);

  constructor(
    private routerModul: AppRoutingModule,
    private http: HttpClient,
    private router: Router,
    private tokenService: TokenService
  ) {}

  addFiles(data: NewFile) {
    return this.http
      .post(`${this.apiUrl}/dispatch/create`, data, { context: checkToken() })
      .pipe(
        // si es un success devuelvo el body
        tap((response) => {
          console.log(response);
          // si la respuesta es Expediente creado con exito. devuelvo un true
          if (response === 'Expediente creado con exito.') {
            console.log('upDateNewFile-->');

            // una vez almacenado el nuevo exp, hago una solicitud para actualizarlo
            // this.upDateNewFile(data.fileNumber, data.yearNumber, data.dispatch);
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

  getFiles(): Observable<FileModel[]> {
    return this.http
      .get<any[]>(`${this.apiUrl}/dispatch/get`, {
        context: checkToken(),
      })
      .pipe(
        tap((response) => {
          // console.log('files', response);
          console.log('Response from API:', response);
          if (response != null) {
            this.files$.next(response);
            this.upDependencia();
          }
          return response;
        }),
        catchError((error) => {
          console.error('Error en la solicitud HTTP:', error);
          throw error; // Propaga el error para que otros puedan manejarlo.
        })
      );
  }

  // funcion para buscar expediente por id
  getFilById(idExp: object): Observable<ExpData> {
    // hago la consulta a dispatch/getById y le envio el id del expediente
    return this.http
      .post<any>(`${this.apiUrl}/dispatch/getById`, idExp, {
        context: checkToken(),
      })
      .pipe(
        tap((response) => {
          // consulto si el campo dependencia existe en el objeto
          if (response.dependencia) {
            // si existe busco el nombre de la dependencia
            const dependencia = dependencias.find(
              (dependencia) => dependencia.id === response.dependencia
            );
            response.dependencia = dependencia?.nombre || '';
          }
          // console.log('expById --', response);

          return response;
        }),
        catchError((error) => {
          console.error('Error en la solicitud HTTP:', error);
          throw error; // Propaga el error para que otros puedan manejarlo.
        })
      );
  }

  // funcion para actualizar el valor de dependencia numero a nombre
  upDependencia() {
    const files = this.files$.getValue();
    //valido que sea un array iterable
    if (!Array.isArray(files)) {
      console.error('El valor de files no es un array:', files);
      return;
    }

    files.forEach((file) => {
      const dependencia = dependencias.find(
        (dependencia) => dependencia.id === file.dependencia
      );
      file.dependencia = dependencia?.nombre || '';
    });
    // console.log('newDependencia', files);
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
  // funcion para actualizar el fileSelected$
  selectFile(file: number) {
    this.fileSelected$.next(file);
  }

  // funcion para obtener el fileSelected$
  getFileSelected$() {
    return this.fileSelected$.asObservable();
  }

  // actualizo el expediente recien cargado
  upDateNewFile(caseNumber: number, caseYear: number, dispatch: number) {
    console.log(
      'caseNumber',
      caseNumber,
      'caseYear',
      caseYear,
      'dispatch',
      dispatch
    );

    // en el cuerpo de la solicitud envio casaNumber, caseYear y dispatch y en la cabezera el token y id_user
    return (
      this.http
        .post<any>(
          `${this.apiUrl}/dispatch/updateNewFile`,
          {
            fileNumber: caseNumber, // Cambié caseNumber a fileNumber
            yearNumber: caseYear, // Cambié caseYear a yearNumber
            dispatch,
          },
          { context: checkToken() }
        )
        //proceso la respuesta
        .pipe(
          tap((response) => {
            // Imprimo en consola la respuesta del servidor
            console.log('Respuesta del servidor (updateNewFile):', response);
          }),
          catchError((error) => {
            console.error('Error en updateNewFile:', error);
            return throwError(error.error.message || 'Server error');
          })
        )
    );
  }
}
