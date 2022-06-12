import { Component, OnInit } from '@angular/core';
import { UserScreenComponent } from 'src/app/screen/user-screen/user-screen.component';

@Component({
  selector: 'app-user-componenet',
  templateUrl: './user-componenet.component.html',
  styleUrls: ['./user-componenet.component.scss']
})
export class UserComponenetComponent implements OnInit {

  constructor(
    private userScreen : UserScreenComponent
  ) { }

  ngOnInit(): void {
  }
  editionEmail(){
    this.userScreen.editEmail = true;
  }
}
