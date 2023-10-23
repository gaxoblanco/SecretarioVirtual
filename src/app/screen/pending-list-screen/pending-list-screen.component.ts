import { Component, Input, OnInit } from '@angular/core';
import { FormControl, FormGroup } from '@angular/forms';
import { RequestStatus } from '@models/request-status.model';
import { FileModel } from 'src/app/models/file.model';
import { FilesService } from 'src/app/services/files.service';

@Component({
  selector: 'app-pending-list-screen',
  templateUrl: './pending-list-screen.component.html',
  styleUrls: ['./pending-list-screen.component.scss'],
})
export class PendingListScreenComponent implements OnInit {
  status: RequestStatus = 'init';
  filsArrowDownStyle: Boolean = false;
  filsArrowUpStyle: Boolean = false;
  coursArrowDownStyle: Boolean = false;
  coursArrowUpStyle: Boolean = false;
  StateArrowDownStyle: Boolean = false;
  StateArrowUpStyle: Boolean = false;

  searchFile: FormGroup;
  fileList: FileModel[] = [
    {
      id_exp: 0,
      numero_exp: 0,
      anio_exp: '',
      caratula: '',
      dependencia: '',
      state: false,
    },
  ];
  arrowDow = {};

  constructor(private FileSer: FilesService) {
    this.searchFile = new FormGroup({
      searchNumber: new FormControl(''),
    });
  }

  ngOnInit(): void {
    // this.fileList = this.FileSer.files;
    this.FileSer.getFiles().subscribe((files) => {});
    this.FileSer.getFiles$().subscribe((files) => {
      this.fileList = files;
      console.log('files', files[0]);
    });
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
    }, 500);
  }
  filterFil() {
    let filNumber = this.searchFile.value;
    // this.FileSer.filter(filNumber);
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
