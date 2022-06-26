import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { UserScreenComponent } from 'src/app/screen/user-screen/user-screen.component';
import { UserServiceService } from '../../services/user-service.service';
import {newAdditionalDTO} from '../../models/additional-model';

@Component({
  selector: 'app-new-user-component',
  templateUrl: './new-user-component.component.html',
  styleUrls: ['./new-user-component.component.scss']
})
export class NewUserComponentComponent implements OnInit {

  newAdditionalDTO: FormGroup;

  constructor(
    private userServ: UserServiceService,
    private userScreen : UserScreenComponent
  ) {
    this.newAdditionalDTO = new FormGroup({
      name: new FormControl('', Validators.required),
      email: new FormControl('', [Validators.required, Validators.email]),
    })
  }

  ngOnInit(): void {
  }

  saveNewAdditional(){
    let sabeAdditionl: newAdditionalDTO = this.newAdditionalDTO.value;
    this.userServ.addNewAdditional(sabeAdditionl);
    this.userScreen.moreEmail = false;
  }

  cancelClick(){
    this.userScreen.moreEmail = false;
  }

  get name () {return this.newAdditionalDTO.get('name')};
  get email () {return this.newAdditionalDTO.get('email')};
}
