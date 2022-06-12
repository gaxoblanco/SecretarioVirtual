import { Component, OnInit } from '@angular/core';
import { UserScreenComponent } from 'src/app/screen/user-screen/user-screen.component';

@Component({
  selector: 'app-new-user-component',
  templateUrl: './new-user-component.component.html',
  styleUrls: ['./new-user-component.component.scss']
})
export class NewUserComponentComponent implements OnInit {

  constructor(
    private userScreen : UserScreenComponent
  ) { }

  ngOnInit(): void {
  }

  cancelClick(){
    this.userScreen.moreEmail = false;
  }
}
