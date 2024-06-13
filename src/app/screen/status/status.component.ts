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

  ngOnInit(): void {}
}
