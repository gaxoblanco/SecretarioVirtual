import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { FilesService } from 'src/app/services/files.service';

@Component({
  selector: 'app-add-file-screen',
  templateUrl: './add-file-screen.component.html',
  styleUrls: ['./add-file-screen.component.scss']
})
export class AddFileScreenComponent implements OnInit {

  files : FormGroup;

  constructor(
    private FileSer : FilesService,
  ) {
    this.files = new FormGroup({
      fileNumber: new FormControl('', [Validators.required, Validators.minLength(7), Validators.maxLength(7)]),
      department: new FormControl('', Validators.required),
    })
  }

  ngOnInit(): void {
  }
  addFile(){
    let File = this.files.value;
    this.FileSer.addFiles(File);
  }
  get FileNumber() {return this.files.get('fileNumber')}
  get Department() {return this.files.get('department')}
}
