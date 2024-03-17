import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FilesService } from '@services/files.service';
import { ActivatedRoute } from '@angular/router';
import { dependencias } from '@models/dependencias';

@Component({
  selector: 'app-history-exp',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './history-exp.component.html',
  styleUrl: './history-exp.component.scss',
})
export class HistoryExpComponent implements OnInit {
  num_exp: any;
  anio_exp: any;

  idExp: object = {};
  expData: any; // crear un obj de como se ve un exp completo

  constructor(
    private route: ActivatedRoute,
    private filesService: FilesService
  ) {}

  ngOnInit() {
    this.num_exp = this.route.snapshot.params['numero_exp'];
    this.anio_exp = this.route.snapshot.params['anio_exp'];

    // console.log(this.num_exp, this.anio_exp);

    // obtengo el valor del observable getFileSelected$
    this.filesService.fileSelected$.subscribe((data) => {
      this.idExp = { idExp: data };
      console.log('idExp --', this.idExp);
    });

    // hago la solicitud de los datos a mostrar
    this.filesService.getFilById(this.idExp).subscribe((data) => {
      this.expData = data;
    });
  }
}
