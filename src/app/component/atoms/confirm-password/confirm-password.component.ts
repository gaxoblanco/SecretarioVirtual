import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  FormControl,
  FormGroup,
  ReactiveFormsModule,
  UntypedFormControl,
  UntypedFormGroup,
  Validators,
} from '@angular/forms';
import { AutenticationServiceService } from '@services/autentication-service.service';
import { StrengthValidatorService } from '@services/strength-validator.service';
import { RequestStatus } from '@models/request-status.model';

@Component({
  selector: 'app-confirm-password',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './confirm-password.component.html',
  styleUrl: './confirm-password.component.scss',
})
export class ConfirmPasswordComponent implements OnInit {
  [x: string]: any;
  @Output() cancelChange: EventEmitter<void> = new EventEmitter<void>();
  passwordDTO: UntypedFormGroup;
  status: RequestStatus = 'init';

  constructor(private autServ: AutenticationServiceService) {
    this.passwordDTO = new FormGroup(
      {
        password: new FormControl('', [
          Validators.required,
          Validators.minLength(6),
        ]),
        confirmPassword: new FormControl('', [
          Validators.required,
          Validators.minLength(6),
        ]),
      },
      [StrengthValidatorService.MatchValidator('password', 'confirmPassword')]
    );
  }

  ngOnInit(): void {}

  isDiferent: boolean = false;
  changeHandler() {
    const pass = this.passwordDTO?.value; // Add null check before accessing value property
    // this.autServ.changePassword(pass);
    // console.log('passas', pass);
    this.status = 'loading';
    if (pass.password !== pass.confirmPassword) {
      this.status = 'failed';
    }

    this.autServ.changePassword(pass.password).subscribe(
      (response) => {
        console.log('respuestA', response);
      },
      (error) => {
        console.log('error', error);
      }
    );
  }

  cancelChangeHandler() {
    // escucho y llamo a cancelChange
    this.cancelChange.emit();
  }
  get password() {
    return this.passwordDTO?.get('password');
  }
  get confirmPassword() {
    return this.passwordDTO?.get('confirmPassword');
  }
  get passwordMatchError() {
    // passwordDTO MatchValidator state
    return this.passwordDTO?.hasError('passwordMatch');
  }
}
