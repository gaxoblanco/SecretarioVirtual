import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-reset-password',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './reset-password.component.html',
  styleUrl: './reset-password.component.scss',
})
export class ResetPasswordComponent {
  email: string | undefined;
  token: string | undefined;
  password: string | undefined;
  confirmPassword: string | undefined;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private passwordService: PasswordService
  ) {}

  ngOnInit() {
    // Obtener el token y el correo electrónico desde la URL
    this.route.queryParams.subscribe((params) => {
      this.token = params['token'];
      this.email = params['email'];
    });
  }

  onSubmit() {
    // Verificar que las contraseñas coincidan
    if (this.password === this.confirmPassword) {
      // Llamar al servicio para cambiar la contraseña
      this.passwordService
        .resetPassword(this.email, this.token, this.password)
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
    } else {
      // Manejar el caso en que las contraseñas no coinciden
      console.error('Las contraseñas no coinciden');
    }
  }
}
