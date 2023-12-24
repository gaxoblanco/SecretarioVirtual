import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-login-screen',
  templateUrl: './login-screen.component.html',
  styleUrls: ['./login-screen.component.scss'],
})
export class LoginScreenComponent implements OnInit {
  // state para recuperar el password
  recoverState: boolean = false;
  constructor() {}

  ngOnInit(): void {}
}
