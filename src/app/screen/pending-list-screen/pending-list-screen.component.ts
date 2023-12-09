import { Component, Input, OnInit } from '@angular/core';
import { UntypedFormControl, UntypedFormGroup } from '@angular/forms';
import { RequestStatus } from '@models/request-status.model';
import { FileModel } from 'src/app/models/file.model';
import { FilesService } from 'src/app/services/files.service';
import { trigger, transition, style, animate } from '@angular/animations';

@Component({
  selector: 'app-pending-list-screen',
  templateUrl: './pending-list-screen.component.html',
  styleUrls: ['./pending-list-screen.component.scss'],
  animations: [
    trigger('fadeInOut', [
      transition(':enter', [
        style({ opacity: 0 }),
        animate(600, style({ opacity: 1 })),
        animate(500, style({ fontSize: '1rem' })),
      ]),
      transition(':leave', [
        animate(600, style({ opacity: 0 })),
        animate(500, style({ fontSize: '0rem' })),
      ]),
    ]),
  ],
})
export class PendingListScreenComponent implements OnInit {
  status: RequestStatus = 'init';
  filsArrowDownStyle: Boolean = false;
  filsArrowUpStyle: Boolean = false;
  coursArrowDownStyle: Boolean = false;
  coursArrowUpStyle: Boolean = false;
  StateArrowDownStyle: Boolean = false;
  StateArrowUpStyle: Boolean = false;

  searchFile: UntypedFormGroup;
  fileList: FileModel[] = [
    {
      id_exp: 0,
      numero_exp: 0,
      anio_exp: 0,
      caratula: '',
      dependencia: '',
      state: false,
    },
    {
      id_exp: 1,
      numero_exp: 11,
      anio_exp: 11,
      caratula: 'caratulera',
      dependencia: 'juzgado N1',
      state: false,
    },
    {
      id_exp: 2,
      numero_exp: 22,
      anio_exp: 22,
      caratula: 'caratulera',
      dependencia: 'juzgado N2',
      state: false,
    },
    {
      id_exp: 3,
      numero_exp: 33,
      anio_exp: 33,
      caratula: 'caratulera',
      dependencia: 'juzgado N3',
      state: false,
    },
    {
      id_exp: 4,
      numero_exp: 44,
      anio_exp: 44,
      caratula: 'caratulera',
      dependencia: 'juzgado N4',
      state: false,
    },
    {
      id_exp: 5,
      numero_exp: 55,
      anio_exp: 55,
      caratula: 'caratulera',
      dependencia: 'juzgado N5',
      state: false,
    },
    {
      id_exp: 6,
      numero_exp: 66,
      anio_exp: 66,
      caratula: 'caratulera',
      dependencia: 'juzgado N6',
      state: false,
    },
    {
      id_exp: 7,
      numero_exp: 77,
      anio_exp: 77,
      caratula: 'caratulera',
      dependencia: 'juzgado N7',
      state: false,
    },
    {
      id_exp: 8,
      numero_exp: 88,
      anio_exp: 88,
      caratula: 'caratulera',
      dependencia: 'juzgado N8',
      state: false,
    },
    {
      id_exp: 9,
      numero_exp: 99,
      anio_exp: 99,
      caratula: 'caratulera',
      dependencia: 'juzgado N9',
      state: false,
    },
    {
      id_exp: 10,
      numero_exp: 100,
      anio_exp: 100,
      caratula: 'caratulera',
      dependencia: 'juzgado N10',
      state: false,
    },
  ];
  fileFilter: FileModel[] = [];
  arrowDow = {};

  constructor(private FileSer: FilesService) {
    this.searchFile = new UntypedFormGroup({
      searchNumber: new UntypedFormControl(''),
    });
  }

  ngOnInit(): void {
    // this.fileList = this.FileSer.files;
    this.FileSer.getFiles().subscribe((files) => {
      this.fileList = files;
      // console.log('files-com', files[0]);
    });
    // this.FileSer.getFiles$().subscribe((files) => {
    //   this.fileList = files;
    //   console.log('files-com', files[0]);
    // });
  }
  deleteFile(id: any) {
    // this.FileSer.deleteFiles(id);
    // muestro el spinner
    this.status = 'loading';
    setTimeout(() => {
      this.FileSer.deleteFiles(id).subscribe((response) => {
        // si la respuesta es true
        if (response) {
          // muestro el success
          this.FileSer.getFiles().subscribe((files) => {});
          this.status = 'success';
        }
        // si la respuesta es false
        if (!response) {
          // muestro el error
          console.log('error');
        }
      });
      this.FileSer.getFiles().subscribe((files) => {
        this.fileList = files;
        console.log('files-com', files[0]);
      });
    }, 500);
  }
  filterFil() {
    let filNumber = this.searchFile.value;
    console.log(filNumber);

    // divido el value usando / como referencia
    let filNumberSplit = filNumber.searchNumber.split('/');

    // filtro el array fileList por numero de expediente
    this.fileFilter = this.fileList.filter((file: any) => {
      // si el numero de expediente es igual al primer valor del array
      if (
        file.numero_exp == filNumberSplit[0] &&
        (filNumberSplit[1] == undefined || file.anio_exp == filNumberSplit[1])
      ) {
        // retorno el expediente
        return file;
      }
    });
    console.log(this.fileFilter);
  }

  arrowOff() {
    this.filsArrowDownStyle = false;
    this.filsArrowUpStyle = false;
    this.coursArrowDownStyle = false;
    this.coursArrowUpStyle = false;
    this.StateArrowDownStyle = false;
    this.StateArrowUpStyle = false;
  }

  filsArrowDown() {
    this.fileList.sort((a, b) => a.numero_exp - b.numero_exp);
    this.arrowOff();
    this.filsArrowDownStyle = true;
  }
  filsArrowUp() {
    this.fileList.sort((a, b) => b.numero_exp - a.numero_exp);
    this.arrowOff();
    this.filsArrowUpStyle = true;
  }
  coursArrowDown() {
    this.fileList.sort((a, b) => {
      if (b.dependencia > a.dependencia) {
        return 1;
      }
      if (b.dependencia < a.dependencia) {
        return -1;
      }
      return 0;
    });
    this.arrowOff();
    this.coursArrowDownStyle = true;
  }
  coursArrowUp() {
    this.fileList.sort((a, b) => {
      if (a.dependencia > b.dependencia) {
        return 1;
      }
      if (a.dependencia < b.dependencia) {
        return -1;
      }
      return 0;
    });
    this.arrowOff();
    this.coursArrowUpStyle = true;
  }
  StateArrowDown() {
    this.fileList.sort((a, b) => Number(b.state) - Number(a.state));
    this.arrowOff();
    this.StateArrowDownStyle = true;
  }
  StateArrowUp() {
    this.fileList.sort((a, b) => Number(a.state) - Number(b.state));
    this.arrowOff();
    this.StateArrowUpStyle = true;
  }
}
