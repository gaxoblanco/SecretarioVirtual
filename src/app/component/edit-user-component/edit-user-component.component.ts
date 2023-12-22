import { Component, OnInit } from '@angular/core';
import {
  UntypedFormControl,
  UntypedFormGroup,
  Validators,
} from '@angular/forms';
import { UserServiceService } from '@services/user-service.service';
import { LoginModel } from 'src/app/models/login-model';
import { UserScreenComponent } from 'src/app/screen/user-screen/user-screen.component';
import { AutenticationServiceService } from 'src/app/services/autentication-service.service';
import { StrengthValidatorService } from 'src/app/services/strength-validator.service';
import { User } from '@models/login-model';
import { Observable, defaultIfEmpty } from 'rxjs';
import { RequestStatus } from '@models/request-status.model';

@Component({
  selector: 'app-edit-user-component',
  templateUrl: './edit-user-component.component.html',
  styleUrls: ['./edit-user-component.component.scss'],
})
export class EditUserComponentComponent implements OnInit {
  status: RequestStatus = 'init';
  userDTO: UntypedFormGroup;
  user$: any = {
    email: '',
    password: '',
    firstName: '',
    lastName: '',
    subscription: {},
  };

  statePassword: Boolean = false;
  profileForm: any;

  constructor(
    private userScreen: UserScreenComponent,
    private userServ: UserServiceService
  ) {
    this.userDTO = new UntypedFormGroup({
      firstName: new UntypedFormControl('', [Validators.required]),
      lastName: new UntypedFormControl('', [Validators.required]),
      email: new UntypedFormControl('', [
        Validators.required,
        Validators.email,
      ]),
      subscribe: new UntypedFormControl('', [Validators.required]),
    });
  }

  ngOnInit(): void {
    this.userServ.getUser$().subscribe((user) => {
      this.user$ = user;
      console.log('userrrr', user);
    });
    setTimeout(() => {
      console.log(this.user$);
    }),
      500;
  }

  changePassword() {
    this.statePassword = true;
  }
  cancelChange() {
    this.statePassword = false;
  }
  cancelPassword() {
    this.statePassword != this.statePassword;
  }
  cancelClick() {
    this.userScreen.editUser = false;
  }

  //---
  editUser() {
    const user = this.userDTO.value;
    // comparlo los valores del formulario con los valores del usuario
    if (user.firstName !== '' || user.lastName !== '' || user.email !== '') {
      this.status = 'loading';
      // si user.name = '' le agrego el valor que tiene el usuario
      if (user.firstName == '') {
        user.name = this.userServ.user$.value.firstName;
      }
      // si user.lastName = '' le agrego el valor que tiene el usuario
      if (user.lastName == '') {
        user.lastName = this.userServ.user$.value.lastName;
      }
      // si user.email = '' le agrego el valor que tiene el usuario
      if (user.email == '') {
        user.email = this.userServ.user$.value.email;
      }
      // si user.subscribe = '' le agrego el valor que tiene el usuario
      if (user.subscribe == '') {
        user.subscribe = this.userServ.user$.value.subscribe;
      }
      setTimeout(() => {
        this.userServ.editProfile(user).subscribe({
          next: (data) => {
            this.status = 'success';
            this.userScreen.editUser = false;
            console.log('data', data);
          },
          error: (error) => {
            this.status = 'failed';
            console.log(error);
          },
        });
      }, 500);
    }
  }

  get passwordMatchError() {
    return (
      this.profileForm.getError('mismatch') &&
      this.profileForm.get('confirmPassword')?.touched
    );
  }
}
