import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';
import { Subscription } from 'rxjs';
import { PasswordService } from '@services/password.service';
import {
  FormControl,
  FormGroup,
  ReactiveFormsModule,
  UntypedFormGroup,
  Validators,
} from '@angular/forms';
import { RequestStatus } from '@models/request-status.model';
import { StrengthValidatorService } from '@services/strength-validator.service';

@Component({
  selector: 'app-reset-password',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './reset-password.component.html',
  styleUrl: './reset-password.component.scss',
})
export class ResetPasswordComponent implements OnInit {
  email: string | undefined;
  token: string | undefined;
  resetPassword: UntypedFormGroup;
  status: RequestStatus = 'init';
  message: string = '';

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private passwordService: PasswordService,
    private cdr: ChangeDetectorRef
  ) {
    // creo el formulario dinamico con ngForm resetPassword
    this.resetPassword = new FormGroup(
      {
        password: new FormControl('', [
          Validators.required,
          Validators.minLength(6),
        ]),
        repeatPassword: new FormControl('', [
          Validators.required,
          Validators.minLength(6),
        ]),
      },
      {
        validators: StrengthValidatorService.MatchValidator(
          'password',
          'repeatPassword'
        ),
      }
    );
  }

  ngOnInit() {
    this.route.params.subscribe((params) => {
      this.token = decodeURIComponent(params['token']);
      this.email = decodeURIComponent(params['email']);

      console.log('Token', this.token, 'Correo electrónico', this.email);
    });
  }

  cleanToken(rawToken: string): string {
    // Eliminar el prefijo ':token='
    return rawToken.replace(':token=', '');
  }

  cleanEmail(rawEmail: string): string {
    // Eliminar el prefijo ':email='
    return rawEmail.replace(':email=', '');
  }

  onSubmit() {
    this.status = 'loading';
    //prevent default
    event?.preventDefault();
    // Verificar que las contraseñas coincidan
    // Accede directamente a los valores del formulario
    const password = this.resetPassword.value.password;
    const repeatPassword = this.resetPassword.value.repeatPassword;

    // Verificar que las contraseñas coincidan
    if (password == '' || password !== repeatPassword) {
      this.status = 'failed';
      this.message = 'Las contraseñas no coinciden';
      return; // Agrega un return para salir de la función si las contraseñas no coinciden
    }
    console.log('Contraseñas coincidentes', repeatPassword);

    // Llamar al servicio para cambiar la contraseña
    this.passwordService
      .resetPassword(this.email ?? '', this.token ?? '', repeatPassword)
      .subscribe(
        (response) => {
          console.log('Contraseña restablecida con éxito', response);
          // Redirigir a la página de inicio de sesión u otra página
          setTimeout(() => {
            this.status = 'success';
            this.message = 'Contraseña restablecida con éxito';
          }, 600);
        },
        (error) => {
          console.error('Error al restablecer la contraseña', error);
          // Manejar el error, mostrar un mensaje al usuario, etc.
          this.status = 'failed';
          this.message = 'Error al restablecer la contraseña';
        }
      );
  }

  get password() {
    return this.resetPassword?.get('password');
  }
  get repeatPassword() {
    return this.resetPassword?.get('repeatPassword');
  }
}
