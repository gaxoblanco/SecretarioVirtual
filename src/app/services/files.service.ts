import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class FilesService {

  files: any = [
    {
      id: 1,
      fileNumber: '00/02020',
      department: 'Familia',
    },
    {
      id: 2,
      fileNumber: '11/20230',
      department: 'Penal',
    },
    {
      id: 3,
      fileNumber: '22/356132',
      department: 'Penal',
    },
    {
      id:4,
      fileNumber: '33/54615',
      department: 'Familia',
    },
    {
      id: 5,
      fileNumber: '44/561652',
      department: 'defaul1',
    },
  ];

  constructor() { }

  getAllFiles(){
    return this.files
  }

  addFiles(file:any){
    this.files.push(file)
    console.log(this.files);

  }
}
