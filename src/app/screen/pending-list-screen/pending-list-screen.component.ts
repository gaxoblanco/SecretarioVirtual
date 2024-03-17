import { Component, OnInit } from '@angular/core';
import { FormControl, UntypedFormGroup, Validators } from '@angular/forms';
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
    trigger('errorAnimation', [
      transition(':enter', [
        style({ opacity: 0 }),
        animate(200, style({ opacity: 1 })),
      ]),
      transition(':leave', [animate(200, style({ opacity: 0 }))]),
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
  ];
  fileFilter: FileModel[] = [];
  arrowDow = {};
  deletedFileId: any;
  isFilter: Boolean = false;
  constructor(private FileSer: FilesService) {
    this.searchFile = new UntypedFormGroup({
      searchNumber: new FormControl('', Validators.pattern('^[0-9/]*$')),
    });
  }

  ngOnInit(): void {
    // this.fileList = this.FileSer.files;
    this.FileSer.getFiles().subscribe((files) => {
      // valido que sea un array
      if (Array.isArray(files)) {
        // si es un array lo guardo en fileList
        this.fileList = files;
      } else {
        console.log('sin expedientes');
      }
      console.log('files', files);
    });
  }

  deleteFile(id: any) {
    // this.FileSer.deleteFiles(id);
    // Guardo el ID del archivo que está siendo eliminado
    this.deletedFileId = id;
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
        // Reinicio la propiedad deletedFileId después de la eliminación
        this.deletedFileId = null;
      });
      this.FileSer.getFiles().subscribe((files) => {
        this.fileList = files;
        // console.log('files-com', files[0]);
      });
    }, 500);
  }
  filterFil() {
    let filNumber = this.searchFile.value;
    this.isFilter = true;

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
    // console.log(this.fileFilter);
  }
  //clear filter
  clearFil() {
    this.fileFilter = [];
    this.searchFile.reset();
    this.isFilter = false;
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

  mouseX = 0;
  mouseY = 0;
  public showDeleteMessageFlag = false;
  // mouse position
  showDeleteMessage(event: MouseEvent) {
    this.mouseX = event.clientX;
    this.mouseY = event.clientY;
    this.showDeleteMessageFlag = true;
  }

  hideDeleteMessage() {
    this.showDeleteMessageFlag = false;
  }

  // fucion para actualizar el idExpWorking
  expWorking(id: number) {
    this.FileSer.selectFile(id);
  }
}
