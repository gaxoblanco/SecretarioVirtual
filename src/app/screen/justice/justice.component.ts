import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RequestStatus } from '@models/request-status.model';
import {
  UntypedFormGroup,
  UntypedFormControl,
  FormGroup,
  FormBuilder,
  FormsModule,
  ReactiveFormsModule,
  FormControl,
  Validators,
} from '@angular/forms';
import { trigger, transition, style, animate } from '@angular/animations';
import { HttpClient } from '@angular/common/http';
import dataBase from './juzgadosFormosa.json';

@Component({
  selector: 'app-justice',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './justice.component.html',
  styleUrl: './justice.component.scss',
  animations: [
    trigger('fadeInOut', [
      transition(':enter', [
        style({ opacity: 0 }),
        animate(200, style({ opacity: 1 })),
      ]),
      transition(':leave', [animate(200, style({ opacity: 0 }))]),
    ]),
    trigger('errorAnimation', [
      transition(':enter', [
        style({ opacity: 0 }),
        animate(200, style({ opacity: 1 })),
      ]),
      transition(':leave', [animate(200, style({ opacity: 0 }))]),
    ]),
  ],
})
export class JusticeComponent implements OnInit {
  copiedMessage: string = '';
  idMessage: number = 0;
  status: RequestStatus = 'init';
  filsArrowDownStyle: Boolean = false;
  filsArrowUpStyle: Boolean = false;
  coursArrowDownStyle: Boolean = false;
  coursArrowUpStyle: Boolean = false;
  StateArrowDownStyle: Boolean = false;
  StateArrowUpStyle: Boolean = false;

  searchCityForm: UntypedFormGroup;
  serching: any[] = [];
  sBoolean: Boolean = false;
  constructor(private formBuilder: FormBuilder, private http: HttpClient) {
    this.searchCityForm = this.formBuilder.group({
      filterCity: new FormControl('', Validators.pattern('^[a-zA-Z ]*$')),
    });
  }

  //justice
  justice: any[] = [
    {
      id: 1,
      provincia: 'Formosa',
      municipio: 'Formosa',
      juzgado: 'Juez Civil 1',
      email: 'jcc_1_juez@jusformosa.gob.ar',
      movil: '3704282336',
    },
  ];

  searchChing: any[] = [
    {
      city: '',
    },
  ];

  searchJustice: any;

  // Accede a los datos reales
  // justiceDa = justiceData as any[];

  ngOnInit(): void {
    this.justice = dataBase;
    console.log('justice', this.justice);
  }

  filterFill() {
    let serchInput = this.searchCityForm.value;
    console.log('filterFill', serchInput);
    this.sBoolean = true;
    // filtro searchCityForm segun provincia
    this.serching = this.justice.filter((justice) => {
      //retorno todas las provincias que concidan las primeras letras con el input
      if (justice.provincia.toLowerCase().includes(serchInput.filterCity)) {
        return justice;
      }
    });
    if (this.serching.length == 0) {
      this.serching = [
        {
          id: 1,
          provincia: 'No encontrado',
          municipio: '-',
          juzgado: '-',
          email: '-',
          movil: 0,
        },
      ];
    }
    console.log('serching', this.serching);
  }
  cancelFill() {
    this.sBoolean = false;
    this.searchCityForm.reset();
    console.log('cancelFill', this.searchCityForm.value);
    this.serching = [];
  }

  // ordenar por flechas
  arrowOff() {
    this.filsArrowDownStyle = false;
    this.filsArrowUpStyle = false;
    this.coursArrowDownStyle = false;
    this.coursArrowUpStyle = false;
    this.StateArrowDownStyle = false;
    this.StateArrowUpStyle = false;
  }

  filsArrowDown() {
    this.justice.sort((a, b) => a.provincia - b.provincia);
    this.arrowOff();
    this.filsArrowDownStyle = true;
  }
  filsArrowUp() {
    this.justice.sort((a, b) => b.provincia - a.provincia);
    this.arrowOff();
    this.filsArrowUpStyle = true;
  }
  coursArrowDown() {
    this.justice.sort((a, b) => {
      if (b.juzgado > a.juzgado) {
        return 1;
      }
      if (b.juzgado < a.juzgado) {
        return -1;
      }
      return 0;
    });
    this.arrowOff();
    this.coursArrowDownStyle = true;
  }
  coursArrowUp() {
    this.justice.sort((a, b) => {
      if (a.juzgado > b.juzgado) {
        return 1;
      }
      if (a.juzgado < b.juzgado) {
        return -1;
      }
      return 0;
    });
    this.arrowOff();
    this.coursArrowUpStyle = true;
  }
  StateArrowDown() {
    this.justice.sort((a, b) => Number(b.state) - Number(a.state));
    this.arrowOff();
    this.StateArrowDownStyle = true;
  }
  StateArrowUp() {
    this.justice.sort((a, b) => Number(a.state) - Number(b.state));
    this.arrowOff();
    this.StateArrowUpStyle = true;
  }

  // Función para copiar el contenido al portapapeles y mostrar el mensaje emergente
  copyToClipboard(content: string, id: number): void {
    const tempInput = document.createElement('input');
    document.body.appendChild(tempInput);
    tempInput.value = content;
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);

    this.copiedMessage = 'copiado';
    this.idMessage = id;
    setTimeout(() => {
      this.copiedMessage = ''; // Reiniciar el mensaje después de unos segundos
      this.idMessage = 0;
    }, 1200); // 2000 milisegundos (2 segundos)
  }
}
