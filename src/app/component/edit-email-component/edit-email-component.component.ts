import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import {newAdditionalDTO} from '../../models/additional-model';
import { UserComponenetComponent } from '../user-componenet/user-componenet.component';

@Component({
  selector: 'app-edit-email-component',
  templateUrl: './edit-email-component.component.html',
  styleUrls: ['./edit-email-component.component.scss']
})
export class EditEmailComponentComponent implements OnInit {

  edditAdi: FormGroup;

  constructor(
    private userComp :UserComponenetComponent,
  ) {
    this.edditAdi = new FormGroup({
      name: new FormControl('', Validators.required),
      email: new FormControl('', [Validators.required, Validators.email]),
    })
  }

  ngOnInit(): void {
  }

  cancelClick(){
    this.userComp.editEmail = false;
  }
  save(){
    let edition:newAdditionalDTO = this.edditAdi.value;
    this.userComp.editionAdditional(edition);
    this.userComp.editEmail = false;
  }
  delet(){
    this.userComp.deleteAdditional();
    this.userComp.editEmail = false;
    console.log();

  }
}
