import { Injectable } from '@angular/core';
import { Observable, catchError, tap, throwError } from 'rxjs';
import { Router } from '@angular/router';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { FileModel, NewFile } from '@models/file.model';
import { environment } from '@env/environment';
import { AppRoutingModule } from '../app-routing.module';
import { TokenService } from './token.service';
import { checkToken } from '../interceptors/token.interceptor';

@Injectable({
  providedIn: 'root',
})
export class FilesService {
  files: FileModel[] = [
    {
      id: 4456,
      fileNumber: 5461215,
      department: 'Penal',
      state: false,
    },
    {
      id: 453,
      fileNumber: 6542165,
      department: 'Penal',
      state: true,
    },
    {
      id: 4656,
      fileNumber: 5641651,
      department: 'Familia',
      state: true,
    },
    {
      id: 5456,
      fileNumber: 5646518,
      department: 'defaul1',
      state: true,
    },
    {
      id: 14564,
      fileNumber: 5156200,
      department: 'Familia',
      state: false,
    },
  ];
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

    // Agrego el token y el userId al header
    if (token) {
      const headers = new HttpHeaders({
        'Content-Type': 'application/json',
        token: token!.token!,
        userId: token!.id!,
      });
      console.log(headers);

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
    } else {
      // Manejo de error si no se pudo obtener el token
      console.error('No se pudo obtener el token');
      // Puedes lanzar una excepción o manejarlo de acuerdo a tus necesidades.
      return throwError('No se pudo obtener el token');
    }
  }

  deleteFiles(fileId: Number) {
    const position = this.files.findIndex((item) => item.id === fileId);
    this.files.splice(position, 1);
    console.log(position);
  }
  filter(number: Number) {
    this.files.find((item: FileModel) => item.fileNumber === number);
    console.log(this.files);
  }
}
