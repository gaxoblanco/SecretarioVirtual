import { Component, Input, OnInit, Output, EventEmitter, OnChanges  } from '@angular/core';
import { Additional, newAdditionalDTO, UpAdditionalDTO } from 'src/app/models/additional-model';
import { UserServiceService } from 'src/app/services/user-service.service';

@Component({
  selector: 'app-user-screen',
  templateUrl: './user-screen.component.html',
  styleUrls: ['./user-screen.component.scss']
})
export class UserScreenComponent implements OnInit {

  list: any [] = [];


  moreEmail: boolean = false;
  editUser: boolean = false;
  editEmail: boolean = false;


  constructor(
    private userServ : UserServiceService,
  ) { };



  ngOnInit(): void {
      this.list = this.userServ.list;
  };

  more(){
    if (this.moreEmail == false){
      this.moreEmail =! this.moreEmail;
    }
  };

  edition(){
    if (this.editUser == false){
      this.editUser =! this.editUser;
    }
  };
  // editionEmail(){
  //   this.editionEmail()
  // };
  adsitionalN(){
    const number = this.userServ.list.length;
    return number;
  }


  //additional
  editionAdditional(editionValue: UpAdditionalDTO){
   // editionValue.id = this.additionals.id;

    // if(editionValue.name == ''){
    //   editionValue.name = this.additionals.name;
    // }
    // if(editionValue.email == ''){
    //   editionValue.email = this.additionals.email;
    // }

    console.log('userScreen ', editionValue.name)
    this.userServ.upAdditional(editionValue)
    .subscribe(()=>{
      this.ngOnInit();
    });
  }
}
