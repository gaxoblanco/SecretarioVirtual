import { Component, Input, OnInit } from '@angular/core';
import { FormControl, FormGroup } from '@angular/forms';
import { FileModel } from 'src/app/models/file.model';
import { FilesService } from 'src/app/services/files.service';

@Component({
  selector: 'app-pending-list-screen',
  templateUrl: './pending-list-screen.component.html',
  styleUrls: ['./pending-list-screen.component.scss']
})
export class PendingListScreenComponent implements OnInit {

  filsArrowDownStyle: Boolean =  false;
  filsArrowUpStyle: Boolean =  false;
  coursArrowDownStyle: Boolean =  false;
  coursArrowUpStyle: Boolean =  false;
  StateArrowDownStyle: Boolean =  false;
  StateArrowUpStyle: Boolean =  false;

  searchFile: FormGroup;
  fileList:FileModel[] = [
    {
      id: 0,
      fileNumber: 0,
      department: '',
      state: false,
    },
  ];
  arrowDow = {};




  constructor(private FileSer : FilesService,
    ) {
      this.searchFile = new FormGroup({
        searchNumber : new FormControl(''),
      })
    }

  ngOnInit(): void {
    this.fileList = this.FileSer.files;
  }
  deleteFile(id: any){
    this.FileSer.deleteFiles(id);
  }
  filterFil(){
    let filNumber = this.searchFile.value;
    this.FileSer.filter(filNumber)

  }

  arrowOff(){
    this.filsArrowDownStyle =  false;
    this.filsArrowUpStyle =  false;
    this.coursArrowDownStyle =  false;
    this.coursArrowUpStyle =  false;
    this.StateArrowDownStyle =  false;
    this.StateArrowUpStyle =  false;
  }

  filsArrowDown(){
    this.fileList.sort((a,b )=> a.fileNumber - b.fileNumber);
    this.arrowOff();
    this.filsArrowDownStyle = true;
  }
  filsArrowUp(){
    this.fileList.sort((a,b )=> b.fileNumber - a.fileNumber);
    this.arrowOff();
    this.filsArrowUpStyle = true;
  }
  coursArrowDown(){
    this.fileList.sort((a,b)=>{
      if (b.department > a.department){ return 1};
      if (b.department < a.department){ return -1};
      return 0;
    });
    this.arrowOff();
    this.coursArrowDownStyle = true;
  }
  coursArrowUp(){
    this.fileList.sort((a,b)=>{
      if (a.department > b.department){ return 1};
      if (a.department < b.department){ return -1};
      return 0;
    });
    this.arrowOff();
    this.coursArrowUpStyle = true;
  }
  StateArrowDown(){
    this.fileList.sort((a,b )=> Number(b.state) - Number(a.state));
    this.arrowOff();
    this.StateArrowDownStyle = true;
  }
  StateArrowUp(){
    this.fileList.sort((a,b )=> Number(a.state) - Number(b.state));
    this.arrowOff();
    this.StateArrowUpStyle = true;
  }
}
