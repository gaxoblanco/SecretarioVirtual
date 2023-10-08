import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { FileModel, NewFile } from '@models/file.model';
import { environment } from '@env/environment';
import { AppRoutingModule } from '../app-routing.module';
import { Router } from '@angular/router';

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
    private router: Router
  ) {}

  // getAllFiles():Observable<FileModel[]>{

  // }

  addFiles(value: NewFile) {
    // this.files.push(file);
    console.log(value);
    return this.http.post(`${this.apiUrl}/dispatch/create`, value).pipe();
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
