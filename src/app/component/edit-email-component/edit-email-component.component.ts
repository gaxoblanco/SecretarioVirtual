import { Component, OnInit } from '@angular/core';
import { UserScreenComponent } from 'src/app/screen/user-screen/user-screen.component';

@Component({
  selector: 'app-edit-email-component',
  templateUrl: './edit-email-component.component.html',
  styleUrls: ['./edit-email-component.component.scss']
})
export class EditEmailComponentComponent implements OnInit {

  constructor(
    private userScreen : UserScreenComponent
  ) { }

  ngOnInit(): void {
  }

  cancelClick(){
    this.userScreen.editEmail = false;
  }
}
