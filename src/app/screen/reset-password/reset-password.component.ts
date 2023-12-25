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
    // Obtener el token y el correo electrónico desde la URL
    // this.route.params.subscribe((params) => {
    //   this.token = params['token'];
    //   this.email = params['email'];
    // });

    // // Verificar que el token y el correo electrónico no estén vacíos
    // if (this.token == '' || this.email == '') {
    //   console.log('Token o correo electrónico vacíos');
    //   return; // Agrega un return para salir de la función si el token o el correo electrónico están vacíos
    // }

    // this.cdr.detectChanges();

    console.log('Token', this.token, 'Correo electrónico', this.email);
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
      console.log('Las contraseñas no coinciden');
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
          this.router.navigate(['/login']);
        },
        (error) => {
          console.error('Error al restablecer la contraseña', error);
          // Manejar el error, mostrar un mensaje al usuario, etc.
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
