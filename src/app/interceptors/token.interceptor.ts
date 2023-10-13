import { Injectable } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor,
  HttpContextToken,
  HttpContext,
  HttpHeaders,
} from '@angular/common/http';
import { Observable } from 'rxjs';
import { TokenService } from '@services/token.service';

const CHECK_TOKEN_URL = new HttpContextToken<boolean>(() => false);

export function checkToken() {
  return new HttpContext().set(CHECK_TOKEN_URL, true);
}

@Injectable()
export class TokenInterceptor implements HttpInterceptor {
  constructor(private tokenServices: TokenService) {}

  intercept(
    request: HttpRequest<unknown>,
    next: HttpHandler
  ): Observable<HttpEvent<unknown>> {
    if (request.context.get(CHECK_TOKEN_URL)) {
      return this.addtoken(request, next);
    }
    return next.handle(request);
  }

  //metodo para agregar el token a la peticion
  private addtoken(
    request: HttpRequest<unknown>,
    next: HttpHandler
  ): Observable<HttpEvent<unknown>> {
    const token = this.tokenServices.getToken();
    if (token) {
      const authRequest = request.clone({
        setHeaders: {
          token: `${token.token}`,
          UserId: token.id, // Aquí puedes definir el nombre de la cabecera según tu API
        },
      });
      return next.handle(authRequest);
    } else {
      return next.handle(request);
    }
  }
}
