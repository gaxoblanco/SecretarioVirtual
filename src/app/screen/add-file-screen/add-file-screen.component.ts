import { Component, OnInit } from '@angular/core';
import { UntypedFormControl, UntypedFormGroup, Validators } from '@angular/forms';
import { FileModel, NewFile } from '@models/file.model';
import { RequestStatus } from '@models/request-status.model';
import { FilesService } from '@services/files.service';

@Component({
  selector: 'app-add-file-screen',
  templateUrl: './add-file-screen.component.html',
  styleUrls: ['./add-file-screen.component.scss'],
})
export class AddFileScreenComponent implements OnInit {
  files: UntypedFormGroup;
  status: RequestStatus = 'init';

  newFileList: FileModel[] = [];

  dependencias = [
    { id: 7441513, name: 'Juzgado 1º Civ. Com. Nº 1' },
    { id: 47322600, name: 'Juzgado 1º Civ. Com. Nº 2' },
    { id: 48324735, name: 'Juzgado 1º Civ. Com. Nº 3' },
    { id: 49330419, name: 'Juzgado 1º Civ. Com. Nº 4' },
    { id: 50332011, name: 'Juzgado 1º Civ. Com. Nº 5' },
    { id: 51332979, name: 'Juzgado 1º Civ. Com. Nº 6' },
    { id: 55531780, name: 'Juzgado 1º Civ. Com. Trab. - Clorinda' },
    { id: 56543833, name: 'Juzgado 1º Civ. Com. Trab. Men. - Las Lomitas' },
    {
      id: 54215613,
      name: 'Juzgado 1º Civ. Com. Trab. Men. Nº 7 - El Colorado',
    },
    { id: 393241140, name: 'Oficina G.A. Fuero Civ. Com.' },
    { id: 522331559, name: 'Familia S Presidencia' },
    { id: 520310359, name: 'Familia S A' },
    { id: 521313159, name: 'Familia S B' },
    { id: 191524358, name: 'Familia S Secretaría Nº 1' },
    { id: 192530876, name: 'Familia S Secretaría Nº 2' },
    { id: 193534515, name: 'Familia S Secretaría Nº 3' },
    { id: 44005284, name: 'Trabajo S I' },
    { id: 45013012, name: 'Trabajo S II' },
    { id: 46023852, name: 'Trabajo S III' },
    { id: 275555824, name: 'JP Menor Ctd. Clorinda' },
    { id: 273525524, name: 'JP Menor Ctd. Cmte. Fontana' },
    { id: 274543239, name: 'JP Menor Ctd. El Colorado' },
    { id: 180402548, name: 'JP Menor Ctd. Estanislao del Campo' },
    { id: 270413499, name: 'JP Menor Ctd. Herradura' },
    { id: 272435360, name: 'JP Menor Ctd. Ibarreta' },
    { id: 183450390, name: 'JP Menor Ctd. Ing. Guillermo N. Juárez' },
    { id: 276571069, name: 'JP Menor Ctd. Laguna Blanca' },
    { id: 182422213, name: 'JP Menor Ctd. Las Lomitas' },
    { id: 278142604, name: 'JP Menor Ctd. Manuel Belgrano' },
    { id: 196181029, name: 'JP Menor Ctd. Nº 1 Formosa' },
    { id: 197192380, name: 'JP Menor Ctd. Nº 2 Formosa' },
    { id: 198285350, name: 'JP Menor Ctd. Nº 3 Formosa' },
    { id: 565110066, name: 'JP Menor Ctd. Nº 4 Formosa' },
    { id: 271424187, name: 'JP Menor Ctd. Palo Santo' },
    { id: 268365655, name: 'JP Menor Ctd. Pirané' },
    { id: 181411781, name: 'JP Menor Ctd. Pozo del Tigre' },
    { id: 269392271, name: 'JP Menor Ctd. San Francisco de Laishí' },
    { id: 277113567, name: 'JP Menor Ctd. Villa Gral. Güemes' },
  ];

  selectedDependencia: number = 0;

  constructor(private fileSer: FilesService) {
    this.files = new UntypedFormGroup({
      fileNumber: new UntypedFormControl('', [
        Validators.required,
        Validators.minLength(4),
        Validators.maxLength(7),
      ]),
      dependenciaSelect: new UntypedFormControl('', [Validators.required]),
    });
  }

  ngOnInit(): void {}
  onChange() {
    // actualizo selectedDependencia con el id del dependenciaSelect
    this.selectedDependencia = this.files.value.dependenciaSelect;
  }

  addFile() {
    // tomo el valor fileNumber del formulario y hago split para separar los numeros
    let fileNumber = this.files.value.fileNumber;
    // console.log('fileData', this.files.value);

    // valido que el campo no este vacio o sea == 0
    if (fileNumber.length >= 4) {
      // consulto si el filNumber tiene / para separar los numeros
      if (fileNumber.includes('/')) {
        // separo los numeros en un array
        let fileNumberSplit = fileNumber.split('/');

        // creo un objeto de tipo NewFile para enviarlo al servicio
        let newFile: NewFile = {
          fileNumber: Number(fileNumberSplit[0]),
          yearNumber: Number(fileNumberSplit[1]),
          // dependencia es = id de la dependencia seleccionada
          dispatch: this.selectedDependencia,
        };

        // envio el objeto al servicio
        this.fileSer.addFiles(newFile).subscribe({
          next: () => {
            this.status = 'success';
            this.newFileList.push({
              id_exp: this.newFileList.length,
              numero_exp: newFile.fileNumber,
              anio_exp: newFile.yearNumber.toString(),
              caratula: '',
              dependencia: '',
            });
          },
          error: (error) => {
            this.status = 'failed';
            // console.log('er1', error);
          },
        });
      } else {
        // creo un objeto de tipo NewFile separando los ultimos 2 numeros del fileNumber asignandolos al yearNumber
        let newFile: NewFile = {
          fileNumber: Number(fileNumber.slice(0, -2)),
          yearNumber: Number(fileNumber.slice(-2)),
          dispatch: Number(this.selectedDependencia),
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
            console.log(error);
            console.log('er2', error);
          },
        });
      }
    } else if (fileNumber.length != 0) {
      this.status = 'failed';
    }
  }

  get FileNumber() {
    return this.files.get('fileNumber');
  }
}
