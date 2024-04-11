import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
import { MpServicesService } from '@services/mp-services.service';

@Component({
  selector: 'app-status',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './status.component.html',
  styleUrl: './status.component.scss',
})
export class StatusComponent {
  preapprovalId: string | undefined;

  constructor(
    private route: ActivatedRoute,
    private mpServices: MpServicesService
  ) {}

  ngOnInit(): void {
    // Captura el valor del parámetro de consulta 'preapproval_id'
    this.route.queryParams.subscribe((params) => {
      this.preapprovalId = params['preapproval_id'];
      console.log('Preapproval ID:', this.preapprovalId);
      // llamo al servicio para realizar la consulta sobre el estado de mi pago
      //valido que preapprovalId no sea undefined
      if (this.preapprovalId) {
        this.mpServices
          .getPaymentStatus(this.preapprovalId)
          .subscribe((data) => {
            console.log('data-', data);
          });
      }
    });
  }
}
