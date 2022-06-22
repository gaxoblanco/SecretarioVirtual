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

  placeOlder: any = {
    name: '',
    email: '',
  }

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
    this.placeOlder.name = this.userComp.additional.name;
    this.placeOlder.email = this.userComp.additional.email;

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

  get name () {return this.edditAdi.get('name')};
  get email () {return this.edditAdi.get('email')};
}
