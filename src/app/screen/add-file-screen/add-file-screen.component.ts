import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { FileModel, NewFile } from '@models/file.model';
import { RequestStatus } from '@models/request-status.model';
import { FilesService } from '@services/files.service';

@Component({
  selector: 'app-add-file-screen',
  templateUrl: './add-file-screen.component.html',
  styleUrls: ['./add-file-screen.component.scss'],
})
export class AddFileScreenComponent implements OnInit {
  files: FormGroup;
  status: RequestStatus = 'init';

  newFileList: FileModel[] = [];

  constructor(private fileSer: FilesService) {
    this.files = new FormGroup({
      fileNumber: new FormControl('', [
        Validators.required,
        Validators.minLength(4),
        Validators.maxLength(6),
      ]),
    });
  }

  ngOnInit(): void {}
  addFile() {
    // tomo el valor fileNumber del formulario y hago split para separar los numeros
    let fileNumber = this.files.value.fileNumber;
    // consulto si el filNumber tiene / para separar los numeros
    if (fileNumber.includes('/')) {
      // separo los numeros en un array
      let fileNumberSplit = fileNumber.split('/');
      // creo un objeto de tipo NewFile para enviarlo al servicio
      let newFile: NewFile = {
        fileNumber: Number(fileNumberSplit[0]),
        yearNumber: Number(fileNumberSplit[1]),
      };
      // envio el objeto al servicio
      this.fileSer.addFiles(newFile).subscribe({
        next: () => {
          this.status = 'success';
          this.newFileList.push({
            id_exp: 0,
            numero_exp: newFile.fileNumber,
            anio_exp: newFile.yearNumber.toString(),
            caratula: '',
            dependencia: '',
          });
        },
        error: (error) => {
          this.status = 'failed';
          console.log('er1', error);
        },
      });
    } else {
      // creo un objeto de tipo NewFile separando los ultimos 2 numeros del fileNumber asignandolos al yearNumber
      let newFile: NewFile = {
        fileNumber: Number(fileNumber.slice(0, -2)),
        yearNumber: Number(fileNumber.slice(-2)),
      };
      // envio el objeto al servicio
      this.fileSer.addFiles(newFile).subscribe({
        next: () => {
          this.status = 'success';
        },
        error: (error) => {
          this.status = 'failed';
          console.log(error);
          console.log('er2', error);
        },
      });
    }
  }

  get FileNumber() {
    return this.files.get('fileNumber');
  }
}
