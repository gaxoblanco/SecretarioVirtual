import { Component, OnInit } from '@angular/core';
import {
  FormBuilder,
  FormGroup,
  Validators,
  AbstractControl,
} from '@angular/forms';
import { Router } from '@angular/router';
import { RequestStatus } from '@models/request-status.model';
import { AutenticationServiceService } from '@services/autentication-service.service';

@Component({
  selector: 'app-register-component',
  templateUrl: './register-component.component.html',
  styleUrls: ['./register-component.component.scss'],
})
export class RegisterComponentComponent implements OnInit {
  status: RequestStatus = 'init';
  formRegister: FormGroup;

  constructor(
    private formBuilder: FormBuilder,
    private router: Router,
    private authService: AutenticationServiceService
  ) {
    this.formRegister = this.formBuilder.group(
      {
        email: ['', [Validators.required, Validators.email]],
        password: ['', [Validators.minLength(6), Validators.required]],
        password2: ['', Validators.required],
        firstName: ['', Validators.required],
        lastName: ['', Validators.required],
        id_subscription: ['', Validators.required],
      },
      {
        // Usar bind para mantener el contexto del componente y tener la funcion por separado
        validators: this.passwordMatchValidator.bind(this),
      }
    );
  }

  ngOnInit(): void {}

  // create() {
  //   console.log('submit');
  //   if (this.formRegister.valid) {
  //     this.status = 'loading';
  //     const user = this.formRegister.value;
  //     console.log('formulario correcto, solicito creacion');
  //     this.authService.register(user).subscribe((success) => {
  //       if (success) {
  //         console.log('Usuario creado con éxito--', success);
  //         this.status = 'success';
  //         // Redirige al usuario a la página de inicio de sesión
  //         this.router.navigate(['/status']);
  //       } else {
  //         console.log('Error al crear usuario--', success);
  //         this.status = 'failed';
  //       }
  //     });
  //   } else {
  //     // Marca todos los campos del formulario como tocados para mostrar los mensajes de error
  //     this.markFormGroupTouched(this.formRegister);
  //     // Lleva el scroll al inicio de la página
  //     window.scrollTo({ top: 0, behavior: 'smooth' });
  //     console.log(
  //       'formulario invalido por algun motivo',
  //       this.formRegister.errors
  //     );
  //   }
  // }

  create() {
    console.log('submit');
    if (this.formRegister.valid) {
      this.status = 'loading';
      const user = this.formRegister.value;
      console.log('formulario correcto, solicito creacion');
      this.authService.register(user).subscribe(
        (response: any) => {
          if (response.status === 200) {
            console.log('Usuario creado con éxito--', response.message);
            this.status = 'success';
            // Redirige al usuario a la página de inicio de sesión
            // this.router.navigate(['/login']);

            // Redirige al usuario a la URL proporcionada por la API de mercado pago
            window.location.href = response.message;
          } else {
            console.log('Error al crear usuario--', response.message);
            this.status = 'failed';
          }
        },
        (error: any) => {
          console.log('Error al crear usuario:', error);
          this.status = 'failed';
        }
      );
    } else {
      // Marca todos los campos del formulario como tocados para mostrar los mensajes de error
      this.markFormGroupTouched(this.formRegister);
      // Lleva el scroll al inicio de la página
      window.scrollTo({ top: 0, behavior: 'smooth' });
      console.log(
        'formulario invalido por algun motivo',
        this.formRegister.errors
      );
    }
  }

  passwordMatchValidator(control: AbstractControl) {
    const password = control.get('password')?.value;
    const confirmPassword = control.get('password2')?.value;

    if (password !== confirmPassword) {
      control.get('password2')?.setErrors({ mismatch: true });
    } else {
      control.get('password2')?.setErrors(null);
    }
  }

  // Marca todos los campos del formulario como tocados para mostrar los mensajes de error
  markFormGroupTouched(formGroup: FormGroup) {
    Object.values(formGroup.controls).forEach((control) => {
      control.markAsTouched();
      if (control instanceof FormGroup) {
        this.markFormGroupTouched(control);
      }
    });
  }
}
