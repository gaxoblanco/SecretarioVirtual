import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { newAdditionalDTO } from '../../models/additional-model';
import { UserComponenetComponent } from '../user-componenet/user-componenet.component';
import { RequestStatus } from '@models/request-status.model';
import { UserServiceService } from '@services/user-service.service';

@Component({
  selector: 'app-edit-email-component',
  templateUrl: './edit-email-component.component.html',
  styleUrls: ['./edit-email-component.component.scss'],
})
export class EditEmailComponentComponent implements OnInit {
  status: RequestStatus = 'init';
  placeOlder: any = {
    name: '',
    newSemail: '',
  };
  oldSemail: any = '';
  secreataryId: any = '';

  edditAdi: FormGroup;

  constructor(
    private userComp: UserComponenetComponent,
    private userServ: UserServiceService
  ) {
    this.edditAdi = new FormGroup({
      name: new FormControl('', Validators.required),
      newSemail: new FormControl('', [Validators.required, Validators.email]),
      Spass: new FormControl('', [
        Validators.required,
        Validators.minLength(6),
      ]),
    });
  }

  ngOnInit(): void {
    this.placeOlder.name = this.userComp.additional.firstName;
    this.placeOlder.email = this.userComp.additional.Semail;
    this.oldSemail = this.userComp.additional.Semail;
    this.secreataryId = this.userComp.additional.secreataryId;
    // console.log(this.userComp.additional);
  }

  cancelClick() {
    this.userComp.editEmail = false;
  }
  save() {
    let edition: newAdditionalDTO = this.edditAdi.value;
    this.userComp.editionAdditional(edition);
    this.userComp.editEmail = false;
  }
  delet() {
    this.userComp.deleteAdditional();
    this.userComp.editEmail = false;
    console.log();
  }

  editAdditional() {
    this.status = 'loading';
    // console.log(this.edditAdi.value);

    //consulto si algun campo del formulario tiene contenido
    if (
      this.edditAdi.value.name !== '' ||
      this.edditAdi.value.email !== '' ||
      this.edditAdi.value.Spass !== ''
    ) {
      this.status = 'loading';
      const sData = this.edditAdi.value;
      //agrego el campo oldSemail al objeto sData
      sData.oldSemail = this.oldSemail;
      //agrego el secreataryId al objeto sData
      sData.secreataryId = this.secreataryId;
      console.log(sData);
      // agregamos un delay de 2s
      setTimeout(() => {
        this.userServ.upAdditional(sData).subscribe({
          next: () => {
            this.status = 'success';
            this.userComp.editEmail = false;
            // console.log('success');
          },
          error: (error) => {
            this.status = 'failed';
            console.log(error);
          },
        });
      }, 500);
    }
  }

  get name() {
    return this.edditAdi.get('name');
  }
  get newSemail() {
    return this.edditAdi.get('email');
  }
}
