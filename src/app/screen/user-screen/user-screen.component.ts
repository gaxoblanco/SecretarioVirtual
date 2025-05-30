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
import { FilesService } from '@services/files.service';
import { UserServiceService } from '@services/user-service.service';
import { FileModel } from '../../models/file.model';
import { RequestStatus } from '@models/request-status.model';
import { subscribeStatus } from '@models/subscribe-status';
import { UserGet } from '@models/userGet';

@Component({
  selector: 'app-user-screen',
  templateUrl: './user-screen.component.html',
  styleUrls: ['./user-screen.component.scss'],
})
export class UserScreenComponent implements OnInit {
  status: RequestStatus = 'loading';
  isActive = true;
  filList: FileModel[] = [];
  NumberFile = 0;
  list$: any[] = [];
  init_point: string | null = null;
  subStatus: subscribeStatus = 'active';
  user$: UserGet = {
    email: '',
    password: '',
    firstName: '',
    lastName: '',
    subscribe: '',
    subscription: {
      id_subscription: 0,
      name: '',
      num_exp: 0,
      num_secretary: 0,
    },
  };

  moreEmail: boolean = false;
  editUser: boolean = false;

  constructor(
    private fileSer: FilesService,
    private autServ: AutenticationServiceService,
    private userServ: UserServiceService
  ) {}

  ngOnInit(): void {
    this.userServ.getProfile().subscribe((response) => {
      // this.user = response;
    });
    this.userServ.getUser$().subscribe((user) => {
      this.user$ = user;
    });
    //obtengo los secretarios
    this.userServ.getAllAdditional().subscribe((response) => {
      // console.log(response);
    });
    this.userServ.getListSecreataryes$().subscribe((list) => {
      this.list$ = list;
      this.status = 'success';
    });
    // obengo el numero de expedientes -- falta actualizar para hacer una sola solicitud al inicio de la webApp
    this.fileSer.getFiles().subscribe((files) => {});
    this.fileSer.getFiles$().subscribe((response) => {
      // console.log('getFiles$', response);

      this.NumberFile = response.length;
    });

    // decodifico el email
    this.user$.email = this.userServ.encodeToHtml(this.user$.email);

    // si el numero maximo de secretarios == num_secretary desactivo al add secretary
    setTimeout(() => {
      if (this.list$.length < this.user$.subscription.num_secretary) {
        this.isActive = true;
        // valido que this.user$.mercado_pago.init_point exista
        if (this.user$.mercado_pago?.init_point) {
          this.init_point = this.user$.mercado_pago.init_point;
          this.subStatus = 'Pendiente';
        }
      } else {
        this.isActive = false;
      }
    }, 1);
  }

  more() {
    console.log(
      'more',
      this.list$.length < this.user$.subscription.num_secretary
    );

    if (this.list$.length < this.user$.subscription.num_secretary) {
      if (this.moreEmail == false) {
        this.moreEmail = !this.moreEmail;
      }
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
