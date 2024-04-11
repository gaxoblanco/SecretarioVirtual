import { TestBed } from '@angular/core/testing';

import { MpServicesService } from './mp-services.service';

describe('MpServicesService', () => {
  let service: MpServicesService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(MpServicesService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
