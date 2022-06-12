import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-user-screen',
  templateUrl: './user-screen.component.html',
  styleUrls: ['./user-screen.component.scss']
})
export class UserScreenComponent implements OnInit {

  moreEmail: boolean = false;
  editUser: boolean = false;
  editEmail: boolean = false;

  constructor() { }

  ngOnInit(): void {
  }

  more(){
    if (this.moreEmail == false){
      this.moreEmail =! this.moreEmail;
    }
  }

  edition(){
    if (this.editUser == false){
      this.editUser =! this.editUser;
    }
  }
  editionEmail(){
    if (this.editEmail == false){
      this.editEmail =! this.editEmail;
    }
  }
}
