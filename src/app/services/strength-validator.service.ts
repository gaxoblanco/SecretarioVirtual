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


 }
