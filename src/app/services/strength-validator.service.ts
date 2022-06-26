import { Injectable } from '@angular/core';
import { AbstractControl, FormGroup, ValidationErrors, ValidatorFn,  } from '@angular/forms';

@Injectable({
  providedIn: 'root'
})
export class StrengthValidatorService {

  static MatchValidator(source: string, target: string): ValidatorFn {
    return (control: AbstractControl): ValidationErrors | null => {
      const sourceCtrl = control.get(source);
      const targetCtrl = control.get(target);

      return ()=>{sourceCtrl === targetCtrl}}
  }
//---------------
  constructor() { }

//   mustMatch(){
//     return (form: FormGroup): ValidationErrors | null =>{

//       const pass: string = form.get("password").value;
//       const passMatch: string = form.get("confirmPassword").value;

//       if (pass === passMatch) {
//         const match = true;

//         return match ? null : {data: true};
//       };
//       return null;
//     }
//   }
 }
