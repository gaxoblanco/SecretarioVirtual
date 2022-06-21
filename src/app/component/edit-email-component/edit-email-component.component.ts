import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { UserScreenComponent } from 'src/app/screen/user-screen/user-screen.component';
import {newAdditionalDTO} from '../../models/additional-model';

@Component({
  selector: 'app-edit-email-component',
  templateUrl: './edit-email-component.component.html',
  styleUrls: ['./edit-email-component.component.scss']
})
export class EditEmailComponentComponent implements OnInit {

  edditAdi: FormGroup;

  constructor(
    private userScreen : UserScreenComponent
  ) {
    this.edditAdi = new FormGroup({
      name: new FormControl('', Validators.required),
      email: new FormControl('', [Validators.required, Validators.email]),
    })
  }

  ngOnInit(): void {
  }

  cancelClick(){
    this.userScreen.editEmail = false;
  }
  save(){
    let edition:newAdditionalDTO = this.edditAdi.value;
    this.userScreen.editionAdditional(edition);
    this.userScreen.editEmail = false;
  }
}
