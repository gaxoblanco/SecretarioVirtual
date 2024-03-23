import { Component, OnInit } from '@angular/core';
import {
  UntypedFormBuilder,
  UntypedFormGroup,
  Validators,
} from '@angular/forms';
import { LoginModel } from '@models/login-model';
import { RequestStatus } from '@models/request-status.model';
import { AutenticationServiceService } from '@services/autentication-service.service';
import { Router } from '@angular/router';
import { PermissionsService } from '@services/permissions.service';

@Component({
  selector: 'app-login-componenet',
  templateUrl: './login-componenet.component.html',
  styleUrls: ['./login-componenet.component.scss'],
})
export class LoginComponenetComponent implements OnInit {
  formLogin: UntypedFormGroup;
  status: RequestStatus = 'init'; // funciona como una maquina de state
  // state para recuperar el password
  recoverState: boolean = false;
  email: string = '';

  constructor(
    private formBuilder: UntypedFormBuilder,
    private autenticacionService: AutenticationServiceService,
    private permissionsService: PermissionsService,
    private router: Router
  ) {
    this.formLogin = this.formBuilder.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
    });
  }

  ngOnInit(): void {}
  get Email() {
    return this.formLogin.get('email');
  }

  get Password() {
    return this.formLogin.get('password');
  }

  LogCheckin(event: Event) {
    let log: LoginModel = this.formLogin.value;
    event.preventDefault;
    this.status = 'loading';
    this.autenticacionService.login(log).subscribe({
      next: (success) => {
        if (success) {
          // console.log('Inicio de sesión exitoso');
          this.status = 'success';
          this.router.navigate(['/']);
          this.permissionsService.updatePermissions();
        } else {
          console.log('Error al iniciar sesión');
          this.status = 'failed';
        }
      },
      error: (error) => {
        this.status = 'failed';
        console.log(error);
        this.email = log.email;
      },
    });
  }

  // funcion para cambiar el estado de la pantalla
  changeState() {
    this.recoverState = !this.recoverState;
    this.status = 'init';
  }
}
