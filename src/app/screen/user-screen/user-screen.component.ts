import {
  Component,
  Input,
  OnInit,
  Output,
  EventEmitter,
  OnChanges,
} from '@angular/core';
import {
  Additional,
  newAdditionalDTO,
  UpAdditionalDTO,
} from 'src/app/models/additional-model';
import { AutenticationServiceService } from 'src/app/services/autentication-service.service';
import { FilesService } from 'src/app/services/files.service';
import { UserServiceService } from 'src/app/services/user-service.service';
import { FileModel } from '../../models/file.model';
import { RequestStatus } from '@models/request-status.model';

@Component({
  selector: 'app-user-screen',
  templateUrl: './user-screen.component.html',
  styleUrls: ['./user-screen.component.scss'],
})
export class UserScreenComponent implements OnInit {
  status: RequestStatus = 'loading';
  filList: FileModel[] = [];
  NumberFile = 0;
  list$: any[] = [];
  user = {
    email: '',
    password: '',
    firstName: '',
    lastName: '',
    subscribe: '',
  };

  moreEmail: boolean = false;
  editUser: boolean = false;
  editEmail: boolean = false;

  constructor(
    private fileSer: FilesService,
    private autServ: AutenticationServiceService,
    private userServ: UserServiceService
  ) {}

  ngOnInit(): void {
    this.userServ.getProfile().subscribe((response) => {
      this.user = response;
    });
    //obtengo los secretarios
    this.userServ.getAllAdditional().subscribe((response) => {
      // console.log(response);
    });
    this.userServ.getListSecreataryes$().subscribe((list) => {
      this.list$ = list;
      this.status = 'success';
    });
  }

  more() {
    if (this.moreEmail == false) {
      this.moreEmail = !this.moreEmail;
    }
  }

  edition() {
    if (this.editUser == false) {
      this.editUser = !this.editUser;
    }
  }
  // editionEmail(){
  //   this.editionEmail()
  // };
  adsitionalN() {
    const number = this.userServ.list.length;
    return number;
  }

  //additional
  editionAdditional(editionValue: UpAdditionalDTO) {
    this.userServ.upAdditional(editionValue).subscribe(() => {
      this.ngOnInit();
    });
  }
}
