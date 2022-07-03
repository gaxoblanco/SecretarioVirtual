import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { FileModel } from '../models/file.model';

@Injectable({
  providedIn: 'root'
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
      id:4656,
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


  constructor() { }

  // getAllFiles():Observable<FileModel[]>{

  // }

  addFiles(file:FileModel){
    this.files.push(file)
    //console.log(this.files);
  }
  deleteFiles(fileId: Number){
    const position = this.files.findIndex(item => item.id === fileId);
    this.files.splice(position, (1))
    console.log(position);
  }
  filter(number: Number){
    this.files.find((item: FileModel) => item.fileNumber === number)
    console.log(this.files);

  }


}
