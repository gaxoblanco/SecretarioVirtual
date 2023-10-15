import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { UserScreenComponent } from 'src/app/screen/user-screen/user-screen.component';
import { UserServiceService } from '../../services/user-service.service';
import { newAdditionalDTO } from '../../models/additional-model';
import { RequestStatus } from '@models/request-status.model';
import { min } from 'rxjs';

@Component({
  selector: 'app-new-user-component',
  templateUrl: './new-user-component.component.html',
  styleUrls: ['./new-user-component.component.scss'],
})
export class NewUserComponentComponent implements OnInit {
  status: RequestStatus = 'init';
  newAdditionalDTO: FormGroup;

  constructor(
    private userServ: UserServiceService,
    private userScreen: UserScreenComponent
  ) {
    this.newAdditionalDTO = new FormGroup({
      name: new FormControl('', Validators.required),
      Semail: new FormControl('', [Validators.required, Validators.email]),
      Spass: new FormControl('', [
        Validators.required,
        Validators.minLength(6),
      ]),
    });
  }

  ngOnInit(): void {}

  saveNewAdditional() {
    // llama al addNewAdditional del servicio y le pasa el newAdditionalDTO
    this.status = 'loading';
    console.log(this.newAdditionalDTO.value);

    if (this.newAdditionalDTO.valid) {
      this.status = 'loading';
      const sData = this.newAdditionalDTO.value;
      this.userServ.addNewAdditional(sData).subscribe({
        next: () => {
          this.status = 'success';
          this.userScreen.moreEmail = false;
          console.log('success');
        },
        error: (error) => {
          this.status = 'failed';
          console.log(error);
        },
      });
    }
  }

  cancelClick() {
    this.userScreen.moreEmail = false;
  }

  get name() {
    return this.newAdditionalDTO.get('name');
  }
  get email() {
    return this.newAdditionalDTO.get('email');
  }
}
