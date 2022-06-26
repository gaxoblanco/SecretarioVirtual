import { TestBed } from '@angular/core/testing';

import { StrengthValidatorService } from './strength-validator.service';

describe('StrengthValidatorService', () => {
  let service: StrengthValidatorService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(StrengthValidatorService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
