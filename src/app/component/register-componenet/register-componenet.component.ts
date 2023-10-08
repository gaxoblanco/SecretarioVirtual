import { Component, OnInit } from '@angular/core';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { Router } from '@angular/router';
// import { CustomValidators } from '@utils/validators';
import { AutenticationServiceService } from '@services/autentication-service.service';
import { RequestStatus } from '@models/request-status.model';

@Component({
  selector: 'app-register-componenet',
  templateUrl: './register-componenet.component.html',
  styleUrls: ['./register-componenet.component.scss'],
})
export class RegisterComponenetComponent implements OnInit {
  status: RequestStatus = 'init';

  form = this.formBuilder.group(
    {
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.minLength(6), Validators.required]],
      password2: ['', Validators.required],
      firstName: ['', Validators.required],
      lastName: ['', Validators.required],
    }
    // valido que concindan las password
    // validators: [ CustomValidators.MatchValidator('password', 'password2')]
  );

  constructor(
    private formBuilder: FormBuilder,
    private router: Router,
    private authService: AutenticationServiceService
  ) {}

  ngOnInit(): void {}

  // registro el usuario
  create() {
    this.status = 'loading';
    if (this.form.valid) {
      this.status = 'loading';
      // guardo los valores del formulario en un objeto
      const user = this.form.value;
      this.authService.register(user).subscribe({
        next: () => {
          // console.log('success');
          this.status = 'success';
          this.router.navigate(['/login']);
        },
        error: (error) => {
          this.status = 'failed';
          console.log(error);
        },
      });
    }
  }

  // Función para la validación personalizada del campo "Nombre"
  customNameValidator(control: AbstractControl): { [key: string]: any } | null {
    const forbiddenNames = ['admin', 'root']; // Puedes agregar nombres prohibidos aquí

    if (forbiddenNames.includes(control.value?.toLowerCase())) {
      return { forbiddenName: { value: control.value } };
    }
    return null;
  }
}
