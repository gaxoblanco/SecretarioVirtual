import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  FormBuilder,
  FormGroup,
  ReactiveFormsModule,
  Validators,
} from '@angular/forms';
import { Router } from '@angular/router';
import { PasswordService } from '@services/password.service';
import { RequestStatus } from '@models/request-status.model';
import { LoginComponenetComponent } from '../../login-componenet/login-componenet.component';

@Component({
  selector: 'app-recover-password',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './recover-password.component.html',
  styleUrl: './recover-password.component.scss',
})
export class RecoverPasswordComponent {
  // creo el formgroup recoverPasswordForm para el formulario de recuperar contraseña
  recoverPasswordForm: FormGroup;
  status: RequestStatus = 'init';
  message: string = '';
  // tomo el email del intento de login
  loginEmail = this.loginComponent.email;

  constructor(
    private formBuilder: FormBuilder,
    private router: Router,
    private passwordService: PasswordService,
    private loginComponent: LoginComponenetComponent
  ) {
    // creo el formulario con los campos email y password
    this.recoverPasswordForm = this.formBuilder.group({
      email: [this.loginEmail, Validators.required],
    });
  }

  // creo la función para cambiar el estado de la pantalla
  changeState() {
    this.loginComponent.recoverState = !this.loginComponent.recoverState;
  }

  // creo la función para recuperar la contraseña
  recoverPassword() {
    this.status = 'loading';
    // si el formulario es válido
    if (this.recoverPasswordForm.valid) {
      // creo la variable email para guardar el valor del campo email
      const email = this.recoverPasswordForm.value.email;
      // llamo a la función recoverPassword del servicio authService
      this.passwordService.recoverPassword(email).subscribe(
        (response) => {
          // si la respuesta es correcta
          if (response == 'email no existe') {
            this.status = 'failed';
            this.message = 'El correo electrónico no existe';
          } else {
            this.status = 'success';
            this.message = 'Se ha enviado un correo electrónico a su cuenta';
            setTimeout(() => {
              // cambio el componente y vuelvo al login
              this.status = 'init';
              this.changeState();
            }, 500);
          }
        },
        (error) => {
          // si la respuesta es incorrecta
          console.log('error', error);
          // muestro un mensaje de error
          // alert('Error al recuperar la contraseña');
        }
      );
    }
  }
  get email() {
    return this.recoverPasswordForm.get('email');
  }
}
