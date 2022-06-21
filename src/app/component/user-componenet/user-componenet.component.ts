import { Component, Input, OnInit, Output, EventEmitter } from '@angular/core';
import { Additional, newAdditionalDTO } from 'src/app/models/additional-model';
import { UserScreenComponent } from 'src/app/screen/user-screen/user-screen.component';
import { UserServiceService } from 'src/app/services/user-service.service';

@Component({
  selector: 'app-user-componenet',
  templateUrl: './user-componenet.component.html',
  styleUrls: ['./user-componenet.component.scss']
})
export class UserComponenetComponent implements OnInit {
  list: any [] = [];
  aditionals: Additional[] = [
    {  id: 0,
      name: '',
      email: '',}
  ];

  @Input() additional: Additional  ={
    name: '',
    email: '',
    id: 0
  }
  //@Output() newAdd = new EventEmitter<newAdditionalDTO>();


  constructor(
    private userScreen : UserScreenComponent,
    private users : UserServiceService,
  ) { }

  ngOnInit(): void {
    this.users.list
  }
  editionEmail(){
    this.userScreen.editEmail = true;
  }

}
