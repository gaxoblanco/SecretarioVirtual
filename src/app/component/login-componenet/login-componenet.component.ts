import { Component, OnInit } from '@angular/core';
import { UntypedFormBuilder, UntypedFormGroup, Validators } from '@angular/forms';
import { LoginModel } from '@models/login-model';
import { RequestStatus } from '@models/request-status.model';
import { AutenticationServiceService } from '@services/autentication-service.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login-componenet',
  templateUrl: './login-componenet.component.html',
  styleUrls: ['./login-componenet.component.scss'],
})
export class LoginComponenetComponent implements OnInit {
  form: UntypedFormGroup;

  constructor(
    private formBuilder: UntypedFormBuilder,
    private autenticacionService: AutenticationServiceService,
    private router: Router
  ) {
    this.form = this.formBuilder.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(8)]],
    });
  }

  status: RequestStatus = 'init'; // funciona como una maquina de state

  ngOnInit(): void {}
  get Email() {
    return this.form.get('email');
  }

  get Password() {
    return this.form.get('password');
  }

  LogCheckin(event: Event) {
    let log: LoginModel = this.form.value;
    event.preventDefault;
    this.autenticacionService.login(log).subscribe({
      next: () => {
        this.status = 'success';
        this.router.navigate(['/']);
      },
      error: (error) => {
        this.status = 'failed';
        console.log(error);
      },
    });
  }
}
