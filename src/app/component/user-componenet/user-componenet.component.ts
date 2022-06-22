import { Component, Input, OnInit, Output, EventEmitter } from '@angular/core';
import { Additional, newAdditionalDTO, UpAdditionalDTO } from 'src/app/models/additional-model';
import { UserScreenComponent } from 'src/app/screen/user-screen/user-screen.component';
import { UserServiceService } from 'src/app/services/user-service.service';

@Component({
  selector: 'app-user-componenet',
  templateUrl: './user-componenet.component.html',
  styleUrls: ['./user-componenet.component.scss']
})
export class UserComponenetComponent implements OnInit {
  list: any [] = [];
  editEmail: boolean = false;



  @Input() additional: Additional  ={
    id: 0,
    name: '',
    email: '',
  }
  //@Output() newAdd = new EventEmitter<newAdditionalDTO>();


  constructor(
    private usersServ : UserServiceService,
  ) { }

  ngOnInit(): void {
    this.usersServ.list
  }
  editionEmail(){
    if (this.editEmail == false){
      this.editEmail =! this.editEmail;
    }

  }

    //additional
    editionAdditional(editionValue: UpAdditionalDTO){
      editionValue.id = this.additional.id;

       if(editionValue.name == ''){
         editionValue.name = this.additional.name;
       }
       if(editionValue.email == ''){
         editionValue.email = this.additional.email;
       }

       //console.log('userCompo ', this.additional.id)
       this.usersServ.upAdditional(editionValue)
    }

    deleteAdditional(){
      const delet = this.additional.id;
      this.usersServ.deletAdditional(delet);
    }
}
