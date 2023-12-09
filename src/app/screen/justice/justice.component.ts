import { Component } from '@angular/core';
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
export class JusticeComponent {
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

  constructor(private formBuilder: FormBuilder) {
    this.searchCityForm = this.formBuilder.group({
      filterCity: new FormControl('', Validators.pattern('^[a-zA-Z ]*$')),
    });
  }

  //justice
  justice: any[] = [
    {
      id: 1,
      provincia: 'Buenos Aires',
      juzgado: 'Juzgado 1',
      email: 'juez1@example.com',
      digitalExp: true,
      tel: 123456789,
    },
    {
      id: 2,
      provincia: 'Buenos Aires',
      juzgado: 'Juzgado 2',
      email: 'juz2@example.com.ar',
      digitalExp: false,
      tel: 123456789,
    },
    {
      id: 3,
      provincia: 'CABA',
      juzgado: 'Juzgado 3',
      email: 'juzgado3@example.com.ar',
      digitalExp: true,
      tel: 123456789,
    },
  ];

  searchChing: any = {
    city: '',
  };

  searchJustice: any;
  ngOnInit(): void {
    // iguala  this.searchChing = formControlName="searchJustice"
    // this.searchChing = this.searchJustice.value;
    // console.log('searchChing', this.searchChing);
  }

  filterFill() {
    let serchInput = this.searchCityForm.value;
    console.log('filterFill', serchInput);

    // filtro searchCityForm segun provincia
    this.serching = this.justice.filter((justice) => {
      //retorno todas las provincias que concidan las primeras letras con el input
      if (justice.provincia.toLowerCase().includes(serchInput.filterCity)) {
        return justice;
      }
    });
    console.log('serching', this.serching);
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
