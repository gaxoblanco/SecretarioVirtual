import { Injectable, Output, EventEmitter, OnChanges } from '@angular/core';
import { Observable } from 'rxjs';
import { Additional, newAdditionalDTO, UpAdditionalDTO } from '../models/additional-model';

@Injectable({
  providedIn: 'root'
})
export class UserServiceService {

  list: any = [
    {
      id: 1,
      name: 'gaston',
      email: 'gaston@blanco.com',
    },
    {
      id: 2,
      name: 'manonitlo',
      email: 'mano@lito.com',
    },
  ];

  @Output() newAdd = new EventEmitter<newAdditionalDTO>();

  getAllAdditional():Observable<Additional[]>{
    return this.list
  }

  addNewAdditional(addEmail: UpAdditionalDTO){
    addEmail.id = this.list.length + 1;
    this.list.push(addEmail);
    console.log('user service', this.list)
    return this.list;
  }

  upAdditional(edition: UpAdditionalDTO){

   // const edit = this.list.findIndex((item: UpAdditionalDTO) => item.id === edition.id);
    console.log('filtrado', edition.id);

    this.list.push(edition);
    console.log('user service', this.list)
    return this.list;
  }

  constructor() { }
}
