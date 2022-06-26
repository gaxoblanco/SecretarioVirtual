import { Component, OnInit } from '@angular/core';
import { AbstractControl, FormControl, FormGroup, ValidationErrors, ValidatorFn, Validators } from '@angular/forms';
import { UserScreenComponent } from 'src/app/screen/user-screen/user-screen.component';
import { AutenticationServiceService } from 'src/app/services/autentication-service.service';
import { StrengthValidatorService } from 'src/app/services/strength-validator.service';
import { UserServiceService } from 'src/app/services/user-service.service';

@Component({
  selector: 'app-edit-user-component',
  templateUrl: './edit-user-component.component.html',
  styleUrls: ['./edit-user-component.component.scss']
})
export class EditUserComponentComponent implements OnInit {

  passwordDTO: FormGroup;

  statePassword: Boolean = false;
  profileForm: any;

  constructor(
    private autServ : AutenticationServiceService,
    private userScreen : UserScreenComponent
  ) {
    this.passwordDTO = new FormGroup({
      password: new FormControl('', [Validators.required, Validators.minLength(6)]),
      confirmPassword: new FormControl('', [Validators.required]),
    },
      [StrengthValidatorService.MatchValidator('password', 'confirmPassword')]
    );
  }


  ngOnInit(): void {
  }

  changePassword(){
      this.statePassword = true;
  }
  cancelChange(){
    this.statePassword = false;
  }
  cancelPassword(){
    this.statePassword != this.statePassword;
  }
  cancelClick(){
    this.userScreen.editUser = false;
  }

  change(){
    const pass = this.passwordDTO.value;
    this.autServ.changePassword(pass);
    console.log(pass);
  }

  get password() {return this.passwordDTO.get ('password')};
  get confirmPassword() {return this.passwordDTO.get ('confirmPassword')};
  get passwordMatchError() {
    return (
      this.profileForm.getError('mismatch') &&
      this.profileForm.get('confirmPassword')?.touched
    );
  }


}
