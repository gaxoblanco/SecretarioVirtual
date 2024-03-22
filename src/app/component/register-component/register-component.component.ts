import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { RequestStatus } from '@models/request-status.model';
import { AutenticationServiceService } from '@services/autentication-service.service';
import { ReactiveFormsModule } from '@angular/forms';

@Component({
  selector: 'app-register-component',
  templateUrl: './register-component.component.html',
  styleUrls: ['./register-component.component.scss'],
})
export class RegisterComponentComponent implements OnInit {
  status: RequestStatus = 'init';
  form: FormGroup;

  constructor(
    private formBuilder: FormBuilder,
    private router: Router,
    private authService: AutenticationServiceService
  ) {
    this.form = this.formBuilder.group(
      {
        email: ['', [Validators.required, Validators.email]],
        password: ['', [Validators.minLength(6), Validators.required]],
        password2: ['', Validators.required],
        firstName: ['', Validators.required],
        lastName: ['', Validators.required],
        id_subscription: ['', Validators.required],
      },
      {
        validators: this.passwordMatchValidator, // Agrega un validador personalizado para las contraseñas
      }
    );
  }

  ngOnInit(): void {}

  create() {
    if (this.form.valid) {
      this.status = 'loading';
      const user = this.form.value;
      this.authService.register(user).subscribe({
        next: () => {
          this.status = 'success';
          this.router.navigate(['/login']);
        },
        error: (error: any) => {
          this.status = 'failed';
          console.log(error);
        },
      });
    } else {
      // Marca todos los campos del formulario como tocados para mostrar los mensajes de error
      this.markFormGroupTouched(this.form);
      // Lleva el scroll al inicio de la página
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }

  passwordMatchValidator(form: FormGroup) {
    const password = form.get('password')?.value;
    const confirmPassword = form.get('password2')?.value;

    if (password !== confirmPassword) {
      form.get('password2')?.setErrors({ mismatch: true });
    } else {
      password;
      form.get('password2')?.setErrors({ required: false });
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
