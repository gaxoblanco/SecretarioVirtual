import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { UserScreenComponent } from 'src/app/screen/user-screen/user-screen.component';

@Component({
  selector: 'app-edit-user-component',
  templateUrl: './edit-user-component.component.html',
  styleUrls: ['./edit-user-component.component.scss']
})
export class EditUserComponentComponent implements OnInit {

  passwordDTO: FormGroup;

  statePassword: Boolean = false;

  constructor(
    private userScreen : UserScreenComponent
  ) {
    this.passwordDTO = new FormGroup({
      password: new FormControl('', [Validators.required, Validators.minLength(6)]),
    //   confirmPassword: new FormControl('', [acceptTerms: [false, Validators.requiredTrue]])
    // },      {
    //   validators: [Validators.match('password', 'confirmPassword')]
    });
  }


  ngOnInit(): void {
  }
  password(formGroup: FormGroup) {
    const  value  = formGroup.get('password');
    const  value1 = formGroup.get('confirmpassword');
    return value === value1 ? null : { passwordNotMatch: true };
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


}
