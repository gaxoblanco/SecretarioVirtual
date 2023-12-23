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

  constructor(
    private formBuilder: FormBuilder,
    private router: Router,
    private passwordService: PasswordService
  ) {
    // creo el formulario con los campos email y password
    this.recoverPasswordForm = this.formBuilder.group({
      email: ['', Validators.required],
    });
  }

  // creo la función para recuperar la contraseña
  recoverPassword() {
    // si el formulario es válido
    if (this.recoverPasswordForm.valid) {
      // creo la variable email para guardar el valor del campo email
      const email = this.recoverPasswordForm.value.email;
      // llamo a la función recoverPassword del servicio authService
      this.passwordService.recoverPassword(email).subscribe(
        (response) => {
          // si la respuesta es correcta
          console.log('respuesta', response);
          // redirijo al usuario a la página de login
          this.router.navigate(['/login']);
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
}
