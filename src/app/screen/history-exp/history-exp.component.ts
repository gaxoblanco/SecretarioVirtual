import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FilesService } from '@services/files.service';
import { ActivatedRoute } from '@angular/router';
import { dependencias } from '@models/dependencias';
import { ExpData } from '@models/expData';

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
  expData: ExpData | undefined; // crear un obj de como se ve un exp completo

  constructor(
    private route: ActivatedRoute,
    private filesService: FilesService
  ) {}

  ngOnInit() {
    this.num_exp = this.route.snapshot.params['numero_exp'];
    this.anio_exp = this.route.snapshot.params['anio_exp'];
    // console.log(this.num_exp, this.anio_exp);

    this.filesService.fileSelected$.subscribe((data) => {
      if (data) {
        this.idExp = { idExp: data };
        console.log('idExp --', this.idExp);

        // Llamar a la función getFilById solo cuando idExp tenga un valor válido
        this.filesService.getFilById(this.idExp).subscribe((responseData) => {
          this.expData = responseData;
        });
      }
    });
  }
}
