import { Component, Input, OnInit, Output, EventEmitter } from '@angular/core';
import {
  Additional,
  newAdditionalDTO,
  UpAdditionalDTO,
} from 'src/app/models/additional-model';
import { UserScreenComponent } from 'src/app/screen/user-screen/user-screen.component';
import { UserServiceService } from 'src/app/services/user-service.service';

@Component({
  selector: 'app-user-componenet',
  templateUrl: './user-componenet.component.html',
  styleUrls: ['./user-componenet.component.scss'],
})
export class UserComponenetComponent implements OnInit {
  list: any[] = [];
  editEmail: boolean = false;

  @Input() additional: Additional = {
    id: 0,
    firstName: '',
    Semail: '',
    secreataryId: '',
  };
  //@Output() newAdd = new EventEmitter<newAdditionalDTO>();

  constructor(private userServ: UserServiceService) {}

  ngOnInit(): void {
    this.userServ.list;
    this.additional.Semail = this.userServ.encodeToHtml(this.additional.Semail);
  }
  editionEmail() {
    if (this.editEmail == false) {
      this.editEmail = !this.editEmail;
    }
  }

  deleteAdditional() {
    const data = {
      secreataryId: this.additional.secreataryId,
      Semail: this.additional.Semail,
    };
    console.log('data', data);

    // A deletAdditional le envia el idUser y el semail
    this.userServ.deletAdditional(data).subscribe((res) => {
      console.log(res);
    });
  }
}
