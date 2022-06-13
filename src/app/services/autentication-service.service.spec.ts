import { TestBed } from '@angular/core/testing';

import { AutenticationServiceService } from './autentication-service.service';

describe('AutenticationServiceService', () => {
  let service: AutenticationServiceService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(AutenticationServiceService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
