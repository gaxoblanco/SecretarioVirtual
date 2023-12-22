import { Component, OnInit } from '@angular/core';
import {
  UntypedFormControl,
  UntypedFormGroup,
  Validators,
} from '@angular/forms';
import { UserScreenComponent } from 'src/app/screen/user-screen/user-screen.component';
import { UserServiceService } from '../../services/user-service.service';
import { newAdditionalDTO } from '../../models/additional-model';
import { RequestStatus } from '@models/request-status.model';
import { min } from 'rxjs';

@Component({
  selector: 'app-new-user-component',
  templateUrl: './new-user-component.component.html',
  styleUrls: ['./new-user-component.component.scss'],
})
export class NewUserComponentComponent implements OnInit {
  status: RequestStatus = 'init';
  newAdditionalDTO: UntypedFormGroup;

  constructor(
    private userServ: UserServiceService,
    private userScreen: UserScreenComponent
  ) {
    this.newAdditionalDTO = new UntypedFormGroup({
      name: new UntypedFormControl('', Validators.required),
      Semail: new UntypedFormControl('', [
        Validators.required,
        Validators.email,
      ]),
      Spass: new UntypedFormControl('', [
        Validators.required,
        Validators.minLength(6),
      ]),
    });
  }

  ngOnInit(): void {}

  saveNewAdditional() {
    // console.log('start: ', this.newAdditionalDTO.value);
    if (this.newAdditionalDTO.valid) {
      this.status = 'loading';
      const sData = this.newAdditionalDTO.value;
      setTimeout(() => {
        this.userServ.addNewAdditional(sData).subscribe({
          // si devuelve true es porque se guardo correctamente
          next: (data) => {
            console.log('data: ', data);

            if (data === 'Secretario creado correctamente') {
              this.status = 'success';
              this.userScreen.moreEmail = false;
              console.log('success');
            }
            if (data === 'El correo electrónico ya existe') {
              console.log('failed');
              this.status = 'failed';
            }
          },
          error: (error) => {
            console.log('error: ', error);
            this.status = 'failed';
          },
        });
      }, 400);
    }
  }

  cancelClick() {
    this.userScreen.moreEmail = false;
  }

  get name() {
    return this.newAdditionalDTO.get('name');
  }
  get email() {
    return this.newAdditionalDTO.get('email');
  }
}
