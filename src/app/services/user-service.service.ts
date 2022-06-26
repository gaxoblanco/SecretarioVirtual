import { Injectable, Output, EventEmitter, OnChanges } from '@angular/core';
import { Observable } from 'rxjs';
import { HeaderComponentComponent } from '../component/header-component/header-component.component';
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

  constructor(  ) { }

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

    const edit = this.list.find((item: UpAdditionalDTO) => item.id === edition.id);
    if (edition.id == edit.id){
      edit.name = edition.name;
      edit.email = edition.email;
    }

    // this.list.push(edition);
    // console.log('user service', this.list)
    return this.list;
  }

  deletAdditional(delet: Number){
    const edit = this.list.findIndex((item: UpAdditionalDTO) => item.id === delet);
    this.list.splice(edit, (edit+1))
  }
}
