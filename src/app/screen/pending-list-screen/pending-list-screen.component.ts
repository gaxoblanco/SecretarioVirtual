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
  filsArrowDown(){
    this.fileList.sort((a,b )=> a.fileNumber - b.fileNumber);
    console.log(this.fileList);
  }
  filsArrowUp(){
    this.fileList.sort((a,b )=> b.fileNumber - a.fileNumber);
    console.log(this.fileList);
  }
  coursArrowDown(){
    this.fileList.sort((a,b)=>{
      if (b.department > a.department){ return 1};
      if (b.department < a.department){ return -1};
      return 0;
    });
  }
  coursArrowUp(){
    this.fileList.sort((a,b)=>{
      if (a.department > b.department){ return 1};
      if (a.department < b.department){ return -1};
      return 0;
    });
  }
  StateArrowDown(){
    this.fileList.sort((a,b )=> Number(b.state) - Number(a.state));
    console.log(this.fileList);
  }
  StateArrowUp(){
    this.fileList.sort((a,b )=> Number(a.state) - Number(b.state));
    console.log(this.fileList);
  }
}
