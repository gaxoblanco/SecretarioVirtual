import { Component, OnInit } from '@angular/core';
import { UserScreenComponent } from 'src/app/screen/user-screen/user-screen.component';

@Component({
  selector: 'app-edit-user-component',
  templateUrl: './edit-user-component.component.html',
  styleUrls: ['./edit-user-component.component.scss']
})
export class EditUserComponentComponent implements OnInit {

  constructor(
    private userScreen : UserScreenComponent
  ) { }

  ngOnInit(): void {
  }

  cancelClick(){
    this.userScreen.editUser = false;
  }
}
