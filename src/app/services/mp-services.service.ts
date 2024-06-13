import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from '@env/environment';
import { Observable, map } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class MpServicesService {
  // url de API
  apiUrl = environment.API_URL;

  constructor(private http: HttpClient) {}

  // Método para obtener el estado del pago
  getPaymentStatus(id_subscription: string): Observable<Object> {
    // hago un get a ${this.apiUrl}/mp/status y le paso el id_subscription en el body con un clave valor
    // proceso la respuesta para obtener los datos a mostrar
    return this.http
      .get<any>(`${this.apiUrl}/mp/getById`, {
        params: { id_subscription },
      })
      .pipe(
        map((data) => {
          console.log('data getById - ', data);

          return data;
        })
      );
  }
}
