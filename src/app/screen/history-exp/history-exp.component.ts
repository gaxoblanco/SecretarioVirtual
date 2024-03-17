import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FilesService } from '@services/files.service';
import { ActivatedRoute } from '@angular/router';

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

  constructor(
    private route: ActivatedRoute,
    private filesService: FilesService
  ) {}

  ngOnInit() {
    this.num_exp = this.route.snapshot.params['numero_exp'];
    this.anio_exp = this.route.snapshot.params['anio_exp'];

    console.log(this.num_exp, this.anio_exp);

    // obtengo el expediente
    // this.filesService
    //   .searchFiles(this.num_exp, this.anio_exp)
    //   .subscribe((data) => {
    //     console.log(data);
    //   });

    // obtengo el valor del observable getFileSelected$
    this.filesService.fileSelected$.subscribe((data) => {
      this.idExp = { idExp: data };
      console.log(this.idExp);
    });
    console.log('this.idExp', this.idExp);

    // hago la solicitud de los datos a mostrar
    this.filesService.getFilById(this.idExp).subscribe((data) => {
      console.log(data);
    });
  }
}
