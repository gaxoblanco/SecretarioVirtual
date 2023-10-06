import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { LoginModel } from 'src/app/models/login-model';
import { UserScreenComponent } from 'src/app/screen/user-screen/user-screen.component';
import { AutenticationServiceService } from 'src/app/services/autentication-service.service';
import { StrengthValidatorService } from 'src/app/services/strength-validator.service';


@Component({
  selector: 'app-edit-user-component',
  templateUrl: './edit-user-component.component.html',
  styleUrls: ['./edit-user-component.component.scss']
})
export class EditUserComponentComponent implements OnInit {

  passwordDTO: FormGroup;
  user ={
    email: "",
    password: "",
    name: "",
    surname: "",
    subscribe: ''
  };

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
    // this.user = this.autServ.user
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
