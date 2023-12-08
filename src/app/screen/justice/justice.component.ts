import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RequestStatus } from '@models/request-status.model';
import { UntypedFormGroup, UntypedFormControl } from '@angular/forms';

@Component({
  selector: 'app-justice',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './justice.component.html',
  styleUrl: './justice.component.scss',
})
export class JusticeComponent {
  copiedMessage: string = '';
  status: RequestStatus = 'init';
  filsArrowDownStyle: Boolean = false;
  filsArrowUpStyle: Boolean = false;
  coursArrowDownStyle: Boolean = false;
  coursArrowUpStyle: Boolean = false;
  StateArrowDownStyle: Boolean = false;
  StateArrowUpStyle: Boolean = false;

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

  searchChing: string = '';

  // filter
  filterPost = '';
  searchJustice: any;
  ngOnInit(): void {
    // iguala  this.searchChing = formControlName="searchJustice"
    this.searchChing = this.searchJustice.value;
    console.log('searchChing', this.searchChing);
  }

  filterFill() {}

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
  copyToClipboard(content: string): void {
    const tempInput = document.createElement('input');
    document.body.appendChild(tempInput);
    tempInput.value = content;
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);

    this.copiedMessage = 'copiado';
    setTimeout(() => {
      this.copiedMessage = ''; // Reiniciar el mensaje después de unos segundos
    }, 2000); // 2000 milisegundos (2 segundos)
  }
}
